<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Payssion\Payment\Controller\Checkout;

use Magento\Checkout\Model\Session;

class Notify extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var \Payssion\Payment\Model\Config
     */
    protected $_config;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    const STATE_PAID = 2;
    const TRANSACTION_TYPE_ORDER = 'order';
    
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Payssion\Payment\Model\Config $config
     * @param Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Payssion\Payment\Model\Config $config,
        Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_config = $config;
        $this->_checkoutSession = $checkoutSession;
        $this->_logger = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
    	$params = $this->getRequest()->getParams();	 
    	
        if ($this->validateNotify($params)) {
        	$orderModel = $this->_objectManager->get('Magento\Sales\Model\Order');
        	
        	$orderIncrementId = $params['order_id'];
        	$order = $orderModel->loadByIncrementId($orderIncrementId);
        	if (empty($order)) {
        		echo 'order not found';
        	} else {
        		$orderStatus = null;
        		switch ($params['state']) {
        			case 'completed':
        				$orderStatus = $orderModel::STATE_PROCESSING;
        				$this->createOrderInvoice($orderModel, $params);
        				break;
        			case 'cancelled_by_user':
        			case 'cancelled':
        			case 'failed':
        			case 'error':
        			case 'expired':
        				$orderStatus = $orderModel::STATE_CANCELED;
        				break;
        			default:
        				break;
        		}
        		 
        		if ($orderStatus) {
        			$orderModel->setStatus($orderStatus);
        			$orderModel->save();
        			echo 'success';
        		} else {
        			echo 'failed to update';
        		}
        	}

        } else {
        	echo 'failed to check api_sig';
        }
    }

    protected function validateNotify($params)
    {
    	$check_parameters = array(
    			$this->_config->getApiKey(),
    			$params['pm_id'],
    			$params['amount'],
    			$params['currency'],
    			$params['order_id'],
    			$params['state'],
    			$this->_config->getSecretKey()
    	);
    	$check_msg = implode('|', $check_parameters);
    	$check_sig = md5($check_msg);
    	$notify_sig = $params['notify_sig'];
    	return ($notify_sig == $check_sig);
    }
    
    public function createOrderInvoice($order, $params)
    {
    	if ($order->canInvoice()) {
    		$invoice = $this->_objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
    		$invoice->register();
    		$invoice->setState(self::STATE_PAID);
    		$invoice->save();
    
    		$transactionSave = $this->_objectManager->create('Magento\Framework\DB\Transaction')
    		->addObject($invoice)
    		->addObject($invoice->getOrder());
    		$transactionSave->save();
    
    		$order->addStatusHistoryComment(__('Created invoice #%1.', $invoice->getId()))->setIsCustomerNotified(true)->save();
    
    		$this->createTransaction($order, $params);
    	}
    }
    
    public function createTransaction($order, $params)
    {
    	$payment = $this->_objectManager->create('Magento\Sales\Model\Order\Payment');
    	$payment->setTransactionId($params['transaction_id']);
    	$payment->setOrder($order);
    	$payment->setIsTransactionClosed(1);
    	$transaction = $payment->addTransaction(self::TRANSACTION_TYPE_ORDER);
    	$transaction->beforeSave();
    	$transaction->save();
    }
    
    /**
     * Return checkout session object
     *
     * @return Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
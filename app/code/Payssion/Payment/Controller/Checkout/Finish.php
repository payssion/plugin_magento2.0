<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Payssion\Payment\Controller\Checkout;

use Magento\Checkout\Model\Session;

class Finish extends \Magento\Framework\App\Action\Action
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
        $resultRedirect = $this->resultRedirectFactory->create();

        $params = $this->getRequest()->getParams();
        if(!isset($params['order_id'])){
            $this->messageManager->addNotice(__('Invalid return, no transactionId specified'));
            $this->_logger->critical('Invalid return, no transactionId specified', $params);
            $resultRedirect->setPath('checkout/cart');
            return $resultRedirect;
        }
        
        $orderModel = $this->_objectManager->get('Magento\Sales\Model\Order');
        if(isset($params['order_id'])) {
        	$order = $orderModel->loadByIncrementId($params['order_id']);
        }
        
        if (empty($order)) {
        	$this->messageManager->addNotice(__('Invalid return, no transactionId specified'));
        	$this->_logger->critical('Invalid return, no transactionId specified', $params);
        	$resultRedirect->setPath('checkout/cart');
        } else {
            if (isset($params['state']) && 'completed' == $params['state']) {
        		$this->_getCheckoutSession()->start();
        		$resultRedirect->setPath('checkout/onepage/success');
        	} else {
        		$resultRedirect->setPath('checkout/cart');
        	}
        }
        
        return $resultRedirect;
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
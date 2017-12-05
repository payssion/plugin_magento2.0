<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Payssion\Payment\Controller\Checkout;

use Magento\Payment\Helper\Data as PaymentHelper;
use Payssion\Error\Error;

/**
 * Description of Redirect
 *
 * @author Payssion Technical <technical@payssion.com>
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Payssion\Payment\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Payssion\Payment\Model\Config $config
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Payssion\Payment\Model\Config $config,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        PaymentHelper $paymentHelper
    )
    {
        $this->_config = $config; // Payssion config helper
        $this->_checkoutSession = $checkoutSession;
        $this->_logger = $logger;
        $this->_paymentHelper = $paymentHelper;

        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $order = $this->_getCheckoutSession()->getLastRealOrder();

            $method = $order->getPayment()->getMethod();

            $methodInstance = $this->_paymentHelper->getMethodInstance($method);
            if ($methodInstance instanceof \Payssion\Payment\Model\Paymentmethod\Paymentmethod) {
                $redirectUrl = $methodInstance->startTransaction($order, $this->_url);
                $this->_redirect($redirectUrl);
            } else {
                throw new Error('Method is not a payssion payment method');
            }

        } catch (\Exception $e) {
            //$this->messageManager->addException($e, __('Something went wrong, please try again later'));
            $this->_logger->critical($e);
            $this->_getCheckoutSession()->restoreQuote();
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Return checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
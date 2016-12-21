<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Payssion\Payment\Model;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Description of Config
 *
 * @author Payssion Technical <technical@payssion.com>
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $configInterface
    )
    {
        $this->_scopeConfigInterface = $configInterface;
    }

    public function getApiKey()
    {
        $api_key = $this->_scopeConfigInterface->getValue('payment/payssion/api_key', 'store');
        return $api_key;
    }

    public function getSecretKey()
    {
    	$secret_key = $this->_scopeConfigInterface->getValue('payment/payssion/secret_key', 'store'); 
        return $secret_key;
    }

    public function isTestMode()
    {
       return $this->_scopeConfigInterface->getValue('payment/payssion/test_mode', 'store') == 1;
    }
}
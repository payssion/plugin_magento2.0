<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Payssion\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;


class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
     	'payssion_payment_alipay_cn',
    	'payssion_payment_atmva_id',
    	'payssion_payment_bitcoin',
    	'payssion_payment_boleto_br',
    	'payssion_payment_cashu',
    	'payssion_payment_enets_sg',
    	'payssion_payment_eps_at',
    	'payssion_payment_fpx_my',
    	'payssion_payment_giropay_de',
    	'payssion_payment_ideal_nl',
    	'payssion_payment_maybank2u_my',
    	'payssion_payment_onecard',
    	'payssion_payment_bancontact_be',
    	'payssion_payment_p24_pl',
    	'payssion_payment_paysbuy_th',
    	'payssion_payment_poli_au',
    	'payssion_payment_poli_nz',
    	'payssion_payment_qiwi',
    	'payssion_payment_sberbank_ru',
    	'payssion_payment_singpost_sg',
    	'payssion_payment_sofort',
    	'payssion_payment_tenpay_cn',
    	'payssion_payment_unionpay_cn',
    	'payssion_payment_webmoney',
    	'payssion_payment_yamoney',
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper
    ) {
    	$this->escaper = $escaper;
        foreach ($this->methodCodes as $code) {
        	$this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
    	$config = [];
        foreach ($this->methodCodes as $code) {
        	if ($this->methods[$code]->isAvailable()) {
                $config['payment']['instructions'][$code] = $this->getInstructions($code);
                $config['payment']['icon'][$code] = $this->getIcon($code);
            }
        }
        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     * @return string
     */
    protected function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }

    /**
     * Get payment method icon
     *
     * @param string $code
     * @return string
     */
    protected function getIcon($code)
    {
    	$pm_id = substr($code, strlen('payssion_payment_'));
        return "https://www.payssion.com/static/images/checkout/$pm_id.png";
    }
}

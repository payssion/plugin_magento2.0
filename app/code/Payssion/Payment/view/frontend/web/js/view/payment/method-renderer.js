define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function ($,
              Component,
              rendererList) {
        'use strict';

        var defaultComponent = 'Payssion_Payment/js/view/payment/method-renderer/default';

        var methods = [
            {type: 'payssion_payment_alipay_cn', component: defaultComponent},
            {type: 'payssion_payment_atmva_id', component: defaultComponent},
            {type: 'payssion_payment_bitcoin', component: defaultComponent},
            {type: 'payssion_payment_boleto_br', component: defaultComponent},
            {type: 'payssion_payment_cashu', component: defaultComponent},
            {type: 'payssion_payment_enets_sg', component: defaultComponent},
            {type: 'payssion_payment_eps_at', component: defaultComponent},
            {type: 'payssion_payment_fpx_my', component: defaultComponent},
            {type: 'payssion_payment_giropay_de', component: defaultComponent},
            {type: 'payssion_payment_ideal_nl', component: defaultComponent},
            {type: 'payssion_payment_maybank2u_my', component: defaultComponent},
            {type: 'payssion_payment_onecard', component: defaultComponent},
            {type: 'payssion_payment_p24_pl', component: defaultComponent},
            {type: 'payssion_payment_paysbuy_th', component: defaultComponent},
            {type: 'payssion_payment_poli_au', component: defaultComponent},
            {type: 'payssion_payment_poli_nz', component: defaultComponent},
            {type: 'payssion_payment_qiwi', component: defaultComponent},
            {type: 'payssion_payment_sberbank_ru', component: defaultComponent},
            {type: 'payssion_payment_singpost_sg', component: defaultComponent},
            {type: 'payssion_payment_sofort', component: defaultComponent},
            {type: 'payssion_payment_webmoney', component: defaultComponent},
            {type: 'payssion_payment_yamoney', component: defaultComponent}
        ];
        $.each(methods, function (k, method) {
            rendererList.push(method);
        });

        return Component.extend({});
    }
);
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Qenta Central Eastern Europe GmbH
 * (abbreviated to Qenta CEE) and are explicitly not part of the Qenta CEE range of
 * products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License Version 2 (GPLv2) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Qenta CEE does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Qenta CEE does not guarantee their full
 * functionality neither does Qenta CEE assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Qenta CEE does not guarantee the full functionality
 * for customized shop systems or installed plugins of other vendors of plugins within the same
 * shop system.
 *
 * Customers are responsible for testing the plugin's functionality before starting productive
 * operation.
 *
 * By installing the plugin into the shop system the customer agrees to these terms of use.
 * Please do not use the plugin if you do not agree to these terms of use!
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'qenta_checkoutseamless_ccard',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/ccard'
            },
            {
                type: 'qenta_checkoutseamless_ccardmoto',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/ccard'
            },
            {
                type: 'qenta_checkoutseamless_maestro',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/ccard'
            },
            {
                type: 'qenta_checkoutseamless_eps',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/eps'
            },
            {
                type: 'qenta_checkoutseamless_ideal',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/ideal'
            },
            {
                type: 'qenta_checkoutseamless_giropay',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/giropay'
            },
            {
                type: 'qenta_checkoutseamless_tatrapay',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_sofortbanking',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_skrillwallet',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_bmc',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_p24',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_poli',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_moneta',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_ekonto',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_trustly',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_paybox',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/paybox'
            },
            {
                type: 'qenta_checkoutseamless_paysafecard',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_paypal',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_epaybg',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutseamless_sepa',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/sepa'
            },
            {
                type: 'qenta_checkoutseamless_invoice',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'qenta_checkoutseamless_invoiceb2b',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'qenta_checkoutseamless_installment',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'qenta_checkoutseamless_voucher',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/voucher'
            },
            {
                type: 'qenta_checkoutseamless_trustpay',
                component: 'Qenta_CheckoutSeamless/js/view/payment/method-renderer/trustpay'
            }
        );

        return Component.extend({

        });
    }
);
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Wirecard Central Eastern Europe GmbH
 * (abbreviated to Wirecard CEE) and are explicitly not part of the Wirecard CEE range of
 * products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License Version 2 (GPLv2) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Wirecard CEE does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Wirecard CEE does not guarantee their full
 * functionality neither does Wirecard CEE assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Wirecard CEE does not guarantee the full functionality
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
        'Wirecard_CheckoutSeamless/js/view/payment/method-renderer/standard',
        'Wirecard_CheckoutSeamless/js/action/set-payment-method',
        'Wirecard_CheckoutSeamless/js/model/min-age-validator',
        'mage/url',
        'jquery',
        'mage/translate',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (Component, setPaymentMethodAction, minAgeValidator, url, $, $t, additionalValidators) {
        return Component.extend({

            customerData: {},
            customerDob: null,
            defaults: {
                template: 'Wirecard_CheckoutSeamless/payment/method-invoiceinstallment'
            },
            initObservable: function () {
                this._super().observe('customerDob');
                return this;
            },
            initialize: function () {
                this._super();
                this.customerData = window.customerData;
                this.customerDob(this.customerData.dob);
                return this;
            },

            getData: function () {
                var parent = this._super(),
                    additionalData = {};

                additionalData.customerDob = this.customerDob();
                console.log(additionalData);

                return $.extend(true, parent, {'additional_data': additionalData});
            },
            isB2B: function() {
                return this.getCode() == 'wirecard_checkoutseamless_invoiceb2b';
            },
            validate: function () {
                if (!this.isB2B() && !minAgeValidator.validate(this.customerDob())) {
                    var errorPane = $('#' + this.getCode() + '-dob-error');
                    errorPane.html($t('You have to be 18 years or older to use this payment.'));
                    errorPane.css('display', 'block');
                    return false;
                }
                // show consent error if not checked
                var ccErrorPane = $('#' + this.getCode() + '-consent-checkbox-error');
                if (ccErrorPane.length > 0 && !$('#' + this.getCode() + '-consent-checkbox').is(':checked')) {
                    ccErrorPane.html($t('This is a required field.'));
                    ccErrorPane.css('display','block');
                    return false;
                }

                var form = $('#' + this.getCode() + '-form');
                return $(form).validation() && $(form).validation('isValid');
            },

            getConsentText: function () {
                return window.checkoutConfig.payment[this.getCode()].consenttxt;
            },

            placeWirecardOrder: function() {
                if (this.validate() && additionalValidators.validate()) {
                    this.selectPaymentMethod();

                    setPaymentMethodAction(this.messageContainer, this.getDisplayMode());
                }
                return false;
            }
        });
    }
);

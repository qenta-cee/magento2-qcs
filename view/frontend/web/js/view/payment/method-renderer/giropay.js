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
        'underscore',
        'Wirecard_CheckoutSeamless/js/view/payment/method-renderer/standard',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Wirecard_CheckoutSeamless/js/action/set-payment-method',
        'Wirecard_CheckoutSeamless/js/action/store-paymentdata',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/url',
        'mage/translate',
        'jquery'
    ],
    function (_, Component, ccardValidator, additionalValidators, setPaymentMethodAction,
              storePaymentData, fullScreenLoader, url, $t, $) {
        return Component.extend({
            defaults: {
                template: 'Wirecard_CheckoutSeamless/payment/method-giropay',
                accountOwner: '',
                bankAccount: '',
                bankNumber: ''
            },
            initObservable: function () {
                this._super().observe([
                    'accountOwner',
                    'bankAccount',
                    'bankNumber'
                ]);
                return this;
            },
            initialize: function() {
                this._super();

            },
            validate: function () {
                var frm = $('#' + this.getCode() + '-form');
                return frm.validation() && frm.validation('isValid');
            },
            getData: function () {
                var parent = this._super(),
                    additionalData = {};

                return $.extend(true, parent, {'additional_data': additionalData});
            },
            placeWirecardOrder: function () {
                if (this.validate() && additionalValidators.validate()) {
                    var ccData = {
                        'paymentType': this.getPaymentMethod(),
                        'accountOwner': this.accountOwner(),
                        'bankAccount': this.bankAccount(),
                        'bankNumber': this.bankNumber()
                    };

                    var self = this;
                    fullScreenLoader.startLoader();
                    storePaymentData.store(ccData, this.messageContainer, function (response) {
                        fullScreenLoader.stopLoader();

                        self.selectPaymentMethod(); // save selected payment method in Quote

                        setPaymentMethodAction(self.messageContainer, self.getDisplayMode());
                    }, function (response) {
                        fullScreenLoader.stopLoader();
                    });

                }
                return false;
            }
        });
    }
);
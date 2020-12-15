/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Qenta Payment CEE GmbH
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
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Qenta_CheckoutSeamless/js/action/set-payment-method',
        'mage/storage',
        'jquery'
    ],
    function (Component, additionalValidators, setPaymentMethodAction, storage, $) {
        return Component.extend({
            defaults: {
                template: 'Qenta_CheckoutSeamless/payment/method-standard'
            },
            config: {

            },
            initialize: function() {
                this._super();
                this.config = window.checkoutConfig.payment[this.getCode()];
            },
            validate: function () {
                return true;
            },
            getInstructions: function () {
                return this.config.instructions;
            },
            getDisplayMode: function() {
                return this.config.displaymode;
            },
            getLogoUrl: function() {
                return this.config.logo_url
            },
            getPaymentMethod: function() {
                return this.config.paymentMethod;
            },
            clearPaymentData: function() {
                storage.post('qentacheckoutseamless/storage/delete').done(function()
                {
                });
            },
            placeQentaOrder: function () {

                if (this.validate() && additionalValidators.validate())
                {
                    this.selectPaymentMethod(); // save selected payment method in Quote

                    setPaymentMethodAction(this.messageContainer, this.getDisplayMode());
                }
                return false;
            }
        });
    }
);

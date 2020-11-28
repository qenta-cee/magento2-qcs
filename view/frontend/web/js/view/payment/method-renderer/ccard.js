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
        'underscore',
        'Qenta_CheckoutSeamless/js/view/payment/method-renderer/standard',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Qenta_CheckoutSeamless/js/action/set-payment-method',
        'Qenta_CheckoutSeamless/js/action/store-paymentdata',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/url',
        'mage/translate',
        'jquery'
    ],
    function (_, Component, ccardValidator, additionalValidators, setPaymentMethodAction,
              storePaymentData, fullScreenLoader, url, $t, $) {
        return Component.extend({
            defaults: {
                template: 'Qenta_CheckoutSeamless/payment/method-ccard',
                cardholdername: '',
                pan: '',
                expirationMonth: '',
                expirationYear: '',
                cardverifycode: '',
                issueNumber: '',
                issueMonth: '',
                issueYear: ''
            },
            initObservable: function () {
                this._super().observe([
                    'cardholdername',
                    'pan',
                    'expirationMonth',
                    'expirationYear',
                    'cardverifycode',
                    'issueNumber',
                    'issueMonth',
                    'issueYear'
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
            getCcMonths: function () {
                return window.checkoutConfig.payment.ccform.months[this.getCode()];
            },
            getCcYears: function () {
                return window.checkoutConfig.payment.ccform.years[this.getCode()];
            },
            getCvvImageUrl: function () {
                return window.checkoutConfig.payment.ccform.cvvImageUrl[this.getCode()];
            },
            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl()
                    + '" alt="' + $t('Card Verification Number Visual Reference')
                    + '" title="' + $t('Card Verification Number Visual Reference')
                    + '" />';
            },
            hasCvc: function () {
                return this.config.displaycvc;
            },
            hasIssueDate: function () {
                return this.config.displayissuedate;
            },
            hasIssueNumber: function () {
                return this.config.displayissuenumber;
            },
            hasCardholder: function () {
                return this.config.displaycardholder;
            },
            hasPci3DssSaq: function() {
                return this.config.pci3_dss_saq_a_enable;
            },
            initPci3DssSaqIframe: function (container) {
                var paymentMethod = this.getPaymentMethod(),
                    wdcee = new WirecardCEE_DataStorage();
                if (paymentMethod=='CCARD')
                    wdcee.buildIframeCreditCard($(container).attr('id'), '100%', '400px');
                else if (paymentMethod=='MAESTRO')
                    wdcee.buildIframeMaestro($(container).attr('id'), '100%', '400px');
                else if (paymentMethod=='CCARD-MOTO')
                    wdcee.buildIframeCreditCardMoto($(container).attr('id'), '100%', '400px');
            },
            getCardholderPlaceholder: function() {
                return this.config.cardholder_placeholder;
            },
            getPanPlaceholder: function() {
                return this.config.pan_placeholder;
            },
            getCvcPlaceholder: function() {
                return this.config.cvc_placeholder;
            },
            getIssueNumberPlaceholder: function() {
                return this.config.issuenumber_placeholder;
            },
            getSsStartYears: function () {
                return window.checkoutConfig.payment.ccform.ssStartYears[this.getCode()];
            },
            getCcAvailableTypes: function () {
                return window.checkoutConfig.payment.ccform.availableTypes[this.getCode()];
            },
            getCcAvailableTypesValues: function () {
                return _.map(this.getCcAvailableTypes(), function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    }
                });
            },
            getCcMonthsValues: function () {
                return _.map(this.getCcMonths(), function (value, key) {
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },
            getCcYearsValues: function () {
                return _.map(this.getCcYears(), function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },
            getSsStartYearsValues: function () {
                return _.map(this.getSsStartYears(), function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },
            getData: function () {
                var parent = this._super(),
                    additionalData = {};

                return $.extend(true, parent, {'additional_data': additionalData});
            },
            placeQentaOrder: function () {
                if (this.validate() && additionalValidators.validate()) {
                    var paymentMethod = this.getPaymentMethod(),
                        ccData = {},
                        failure = function (errors, messageContainer) {
                            for (var e in errors) {
                                messageContainer.addErrorMessage({'message': storePaymentData.htmlEntityDecode(errors[e].consumerMessage)});
                            }
                        };

                    if (!this.hasPci3DssSaq()) {
                        ccData = {
                            'pan': this.pan(),
                            'expirationMonth': this.expirationMonth(),
                            'expirationYear': this.expirationYear(),
                            'cardholdername': this.cardholdername(),
                            'cardverifycode': this.cardverifycode(),
                            'issueMonth': this.issueMonth(),
                            'issueYear': this.issueYear(),
                            'issueNumber': this.issueNumber()
                        };
                    }

                    var self = this;
                    fullScreenLoader.startLoader();

                    if (paymentMethod == 'MAESTRO') {
                        var mDataStorage = new WirecardCEE_DataStorage();
                        mDataStorage.storeMaestroInformation(ccData, function (response) {
                            fullScreenLoader.stopLoader();
                            if (response.getStatus() == 0) {
                                self.selectPaymentMethod();
                                setPaymentMethodAction(self.messageContainer, self.getDisplayMode());
                            } else {
                                failure(response.getErrors(), self.messageContainer);
                            }
                        });
                    } else if (paymentMethod == 'CCARD') {
                        var ccDataStorage = new WirecardCEE_DataStorage();
                        ccDataStorage.storeCreditCardInformation(ccData, function (response) {
                            fullScreenLoader.stopLoader();
                            if (response.getStatus() == 0) {
                                self.selectPaymentMethod();
                                setPaymentMethodAction(self.messageContainer, self.getDisplayMode());
                            } else {
                                failure(response.getErrors(), self.messageContainer);
                            }
                        });
                    } else if (paymentMethod == 'CCARD-MOTO') {
                        var cmDataStorage = new WirecardCEE_DataStorage();
                        cmDataStorage.storeCreditCardMotoInformation(ccData, function (response) {
                            fullScreenLoader.stopLoader();
                            if (response.getStatus() == 0) {
                                self.selectPaymentMethod();
                                setPaymentMethodAction(self.messageContainer, self.getDisplayMode());
                            } else {
                                failure(response.getErrors(), self.messageContainer);
                            }
                        });
                    }
                }
                return false;
            }
        });
    }
);

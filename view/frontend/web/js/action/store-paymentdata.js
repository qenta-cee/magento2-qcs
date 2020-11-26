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
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'jquery/ui',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, Component, quote, urlBuilder, url, storage, errorProcessor) {
        'use strict';

        return {

            store: function (data, messageContainer, onSuccess, onError) {
                var self = this;
                var qentaCee = new QentaCEE_DataStorage;
                var request = qentaCee.storePaymentInformation(data, function (response) {

                    if (response.getErrors()) {
                        var errors = response.response.error;
                        for (var i = 0; i <= response.response.errors; i++) {
                            if (typeof errors[i] === 'undefined') {
                                continue;
                            }
                            messageContainer.addErrorMessage({'message': self.htmlEntityDecode(errors[i].consumerMessage)});
                        }

                        onError(response);
                    } else {
                        onSuccess(response);
                    }
                });

                // no postMessage support, make read request to datastore to check for stored data
                if (request === null) {
                    storage.post('qentacheckoutseamless/storage/read', JSON.stringify({'paymentType': data.paymentType}))
                        .done(
                            function (response) {
                                if (response === false) {
                                    messageContainer.addErrorMessage({'message': 'no stored paymentinformation found!'});
                                    onError(response);
                                } else {
                                    var ret = new QentaCEE_Response(response);
                                    onSuccess(ret);
                                }
                            })
                        .fail(
                            function (response) {
                                errorProcessor.process(response, messageContainer);
                                onError(response);
                            }
                        );
                }
            },
            htmlEntityDecode: function (str) {
                var tarea = document.createElement('textarea');
                tarea.innerHTML = str;
                return tarea.value;
            }
        };
    }
);

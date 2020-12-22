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

define([
    'jquery'
], function ($) {

    $('.transferfund-fieldset').each(function (index, fieldset) {
        $(fieldset).css('display', 'none');
    });

    $('#transferType').on('change', function (evt) {

        var fieldsetId = 'fields-' + $(this).val().toLowerCase();
        $('.transferfund-fieldset').each(function (index, fieldset) {
            if ($(fieldset).attr('id') == fieldsetId) {
                $(fieldset).css('display', 'block');
                $(fieldset).find('.fundtransfer-required').each(function (index, field) {
                    $(field).addClass('required-entry');
                    $('.field-' + $(field).attr('id')).addClass('required').addClass('_required');
                });
            } else {
                $(fieldset).css('display', 'none');
                $(fieldset).find('.fundtransfer-required').each(function (index, field) {
                    $(field).removeClass('required-entry');
                    $('.field-' + $(field).attr('id')).removeClass('required').removeClass('_required');
                });
            }

            if (fieldsetId == 'fields-existingorder' || fieldsetId == 'fields-sepa-ct') {
                $('#customerStatement').removeClass('required-entry');
                $('.field-customerStatement').removeClass('required').removeClass('_required');
            } else {
                $('#customerStatement').addClass('required-entry');
                $('.field-customerStatement').addClass('required').addClass('_required');
            }
        });
    }).trigger('change');


});

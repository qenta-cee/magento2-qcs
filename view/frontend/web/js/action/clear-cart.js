/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Qenta AG and are explicitly not part
 * of the Qenta AG range of products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License Version 3 (GPLv3) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Qenta AG does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Qenta AG does not guarantee their full
 * functionality neither does Qenta AG assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Qenta AG does not guarantee the full functionality
 * for customized shop systems or installed plugins of other vendors of plugins within the same
 * shop system.
 *
 * Customers are responsible for testing the plugin's functionality before starting productive
 * operation.
 *
 * By installing the plugin into the shop system the customer agrees to these terms of use.
 * Please do not use the plugin if you do not agree to these terms of use!
 */

require([
    'Magento_Customer/js/customer-data'
], function (customerData) {
    var sections = ['cart'];
    customerData.invalidate(sections);
});
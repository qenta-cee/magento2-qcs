<?php
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

namespace Qenta\CheckoutSeamless\Block\Checkout;

use Magento\Framework\View\Element\Template;

class Script extends Template
{

    /**
     * @var \Qenta\CheckoutSeamless\Helper\DataStorage
     */
    protected $_dataStorageHelper;

    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_checkoutSession;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Qenta\CheckoutSeamless\Helper\DataStorage $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Qenta\CheckoutSeamless\Helper\DataStorage $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Qenta\CheckoutSeamless\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataStorageHelper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_dataHelper = $dataHelper;
    }

    public function getDataStorageUrl()
    {
        return $this->_dataStorageHelper->init();
    }

    public function getConsumerDeviceId()
    {
        if (!strlen($this->_checkoutSession->getData('consumerDeviceId'))) {
            $customerId = $this->_dataHelper->getConfigArray()['CUSTOMER_ID'];
            $consumerDeviceId = md5($customerId . "_" . microtime());

            $this->_checkoutSession->setData('consumerDeviceId', $consumerDeviceId);
        }

        return $this->_checkoutSession->getData('consumerDeviceId');
    }
}

<?php
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

namespace Wirecard\CheckoutSeamless\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CcardConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Wirecard\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Wirecard\CheckoutSeamless\Helper\Data $helper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
        \Wirecard\CheckoutSeamless\Helper\Data $helper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_dataHelper = $helper;
        $this->escaper     = $escaper;
        $this->assetRepo   = $assetRepo;
        $this->_cart       = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];

        foreach ($this->_dataHelper->getConfigData('ccard') as $field => $value) {
            if (is_numeric($value)) {
                $value = (int) $value;
            }
            $config['payment'][Payment\Ccard::CODE][$field]     = $value;
            $config['payment'][Payment\Ccardmoto::CODE][$field] = $value;
            $config['payment'][Payment\Maestro::CODE][$field]   = $value;
        }

        return $config;
    }

}


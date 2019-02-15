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

namespace Wirecard\CheckoutSeamless\Controller\Checkout;

use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\Exception\InputException;
use Magento\Framework\App\CsrfAwareActionInterface;
use Wirecard\ElasticEngine\Controller\Frontend\NoCsrfTrait;

class Confirm extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    use NoCsrfTrait;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $_request;

    /**
     * @var \Wirecard\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $_quoteManagement;

    /**
     * @var \Wirecard\CheckoutSeamless\Model\OrderManagement
     */
    protected $_orderManagement;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Wirecard\CheckoutSeamless\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Wirecard\CheckoutSeamless\Model\OrderManagement $orderManagement
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Wirecard\CheckoutSeamless\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Wirecard\CheckoutSeamless\Model\OrderManagement $orderManagement
    ) {
        parent::__construct($context);
        $this->_dataHelper      = $dataHelper;
        $this->_url             = $context->getUrl();
        $this->_logger          = $logger;
        $this->_quoteManagement = $quoteManagement;
        $this->_orderManagement = $orderManagement;
    }

    public function execute()
    {
        $this->_logger->debug(__METHOD__ . ':' . print_r($this->_request->getPost()->toArray(), true));

        try {
            $this->_logger->debug(__METHOD__ . $this->_request->getContent());
            $return = \WirecardCEE_QMore_ReturnFactory::getInstance($this->_request->getPost()->toArray(),
                $this->_dataHelper->getConfigData('basicdata/secret'));

            $error = "";
            if (!$return->validate()) {
                $error = 'Validation error: invalid response';
            }

            if (!strlen($return->mage_orderId)) {
                $error = 'Magento OrderId is missing';
            }

            if (!strlen($return->mage_quoteId)) {
                $error = 'Magento QuoteId is missing';
            }

            if (strlen($error)) {
                die(\WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString($error));
            }

            $this->_orderManagement->processOrder($return);

            die(\WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString());
        } catch (\Exception $e) {
            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());
            $this->_logger->debug(__METHOD__ . ':' . $e->getTraceAsString());

            die(\WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString($e->getMessage()));
        }
    }
}

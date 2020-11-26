<?php
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

namespace Qenta\CheckoutSeamless\Controller\Storage;

class Read extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Qenta\CheckoutSeamless\Helper\DataStorage
     */
    protected $_dataStorageHelper;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $_request;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Qenta\CheckoutSeamless\Helper\Data $dataHelper
     * @param \Qenta\CheckoutSeamless\Helper\DataStorage $dataStorageHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Qenta\CheckoutSeamless\Helper\Data $dataHelper,
        \Qenta\CheckoutSeamless\Helper\DataStorage $dataStorageHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart
    ) {
        parent::__construct($context);
        $this->_url               = $context->getUrl();
        $this->_dataHelper        = $dataHelper;
        $this->_logger            = $logger;
        $this->_cart              = $cart;
        $this->_dataStorageHelper = $dataStorageHelper;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = $this->_resultJsonFactory->create();

        $data = new \Magento\Framework\DataObject();
        $data->setData(json_decode($this->_request->getContent(), true));

        $this->_logger->debug(__METHOD__ . ':' . print_r($data->getData(), true));

        $paymentType = $data['paymentType'];
        if ($paymentType === null) {
            return $result->setData(false);
        }

        $ret = $this->_dataStorageHelper->read();
        if ($ret === false) {
            return $result->setData(false);
        }

        $this->_logger->debug(__METHOD__ . ':' . print_r($ret->getPaymentInformation(), true));

        /* return data in the same format as it were posted with a js store operation */
        return $result->setData(['storageId'          => $ret->getStorageId(),
                                 'paymentInformation' => $ret->getPaymentInformation($paymentType)
        ]);
    }

}
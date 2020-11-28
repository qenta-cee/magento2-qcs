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

namespace Qenta\CheckoutSeamless\Helper;

class DataStorage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Cart $cart
     * @param Data $dataHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Qenta\CheckoutSeamless\Helper\Data $dataHelper,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        parent::__construct($context);

        $this->_url = $context->getUrlBuilder();
        $this->_dataHelper = $dataHelper;
        $this->_cart = $cart;
        $this->_assetRepo = $assetRepo;
    }

    public function init()
    {
        $dataStorageInit = new \QentaCEE\QMore\DataStorageClient($this->_dataHelper->getConfigArray());

        $returnUrl = $this->_url->getUrl('qentacheckoutseamless/storage/returnfallback', ['_secure' => true]);

        $dataStorageInit->setReturnUrl($returnUrl);
        $dataStorageInit->setOrderIdent($this->_cart->getQuote()->getId());

        $response = null;
        if ($this->_dataHelper->getConfigData('ccard/pci3_dss_saq_a_enable')) {
            $dataStorageInit->setJavascriptScriptVersion('pci3');

            if (strlen(trim($this->_dataHelper->getConfigData('ccard/iframe_css_url')))) {
                $url = $this->_assetRepo->getUrlWithParams('Qenta_CheckoutSeamless::css/' . trim($this->_dataHelper->getConfigData('ccard/iframe_css_url')), ['_secure' => true]);
                $dataStorageInit->setIframeCssUrl($url);
            }

            $dataStorageInit->setCreditCardPanPlaceholder($this->_dataHelper->__($this->_dataHelper->getConfigData('ccard/pan_placeholder')));
            $dataStorageInit->setCreditCardShowExpirationDatePlaceholder($this->_dataHelper->getConfigData('ccard/displayexpirationdate_placeholder'));
            $dataStorageInit->setCreditCardCardholderNamePlaceholder($this->_dataHelper->__($this->_dataHelper->getConfigData('ccard/cardholder_placeholder')));
            $dataStorageInit->setCreditCardCvcPlaceholder($this->_dataHelper->__($this->_dataHelper->getConfigData('ccard/cvc_placeholder')));
            $dataStorageInit->setCreditCardShowIssueDatePlaceholder($this->_dataHelper->getConfigData('ccard/displayissuedate_placeholder'));
            $dataStorageInit->setCreditCardCardIssueNumberPlaceholder($this->_dataHelper->__($this->_dataHelper->getConfigData('ccard/issuenumber_placeholder')));

            $dataStorageInit->setCreditCardShowCardholderNameField($this->_dataHelper->getConfigData('ccard/displaycardholder'));
            $dataStorageInit->setCreditCardShowCvcField($this->_dataHelper->getConfigData('ccard/displaycvc'));
            $dataStorageInit->setCreditCardShowIssueDateField($this->_dataHelper->getConfigData('ccard/displayissuedate'));
            $dataStorageInit->setCreditCardShowIssueNumberField($this->_dataHelper->getConfigData('ccard/displayissuenumber'));
        }

        $this->_logger->debug(__METHOD__ . ':' . print_r($dataStorageInit->getRequestData(), true));

        try {
            $response = $dataStorageInit->initiate();

            if (!$response->hasFailed()) {

                $this->_cart->getCheckoutSession()->setQentaCheckoutSeamlessStorageId($response->getStorageId());
                $this->_logger->debug(__METHOD__ . ':storageid:' . $response->getStorageId());

                return $response->getJavascriptUrl();

            } else {

                $dsErrors = $response->getErrors();

                $this->_logger->debug(__METHOD__ . ':storage init failed:' . array_map(function ($e) {
                        return $e->getMessage();
                    }, $dsErrors));

                return false;
            }
        } catch (\Exception $e) {

            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());

            return false;
        }
    }

    /**
     * @return bool|\QentaCEE\QMore\DataStorage\Response\Read
     */
    public function read()
    {
        $dataStorageRead = new \QentaCEE\QMore\DataStorageClient($this->_dataHelper->getConfigArray());
        $dataStorageRead->setStorageId($this->getStorageId());
        $dataStorageRead->read();

        try {

            $response = $dataStorageRead->read();

            if ($response->getStatus() != \QentaCEE\QMore\DataStorage\Response\Read::STATE_FAILURE) {

                return $response;

            } else {

                $dsErrors = $response->getErrors();

                foreach ($dsErrors as $error) {
                    $this->_logger->debug(__METHOD__ . ':' . $error->getMessage());
                }

                return false;
            }
        } catch (\Exception $e) {

            //communication with dataStorage failed. we choose a none dataStorage fallback
            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());

            return false;
        }
    }

    public function getStorageId()
    {
        return $this->_cart->getCheckoutSession()->getQentaCheckoutSeamlessStorageId();
    }
}

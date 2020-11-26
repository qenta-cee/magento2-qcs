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

namespace Qenta\CheckoutSeamless\Helper;

/**
 * Qenta CheckoutSeamless helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_pluginVersion = '1.0.14';
    protected $_pluginName = 'Qenta/CheckoutSeamless';

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;


    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $_productMetadata;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * predefined test/demo accounts
     *
     * @var array
     */
    protected $_presets = array(
        'demo'      => array(
            'basicdata/customer_id' => 'D200001',
            'basicdata/shop_id'     => 'seamless',
            'basicdata/secret'      => 'B8AKTPWBRMNBV455FG6M2DANE99WU2',
            'basicdata/backendpw'   => 'jcv45z'
        ),
        'test_no3d' => array(
            'basicdata/customer_id' => 'D200411',
            'basicdata/shop_id'     => 'seamless',
            'basicdata/secret'      => 'CHCSH7UGHVVX2P7EHDHSY4T2S4CGYK4QBE4M5YUUG2ND5BEZWNRZW5EJYVJQ',
            'basicdata/backendpw'   => '2g4f9q2m'
        ),
        'test_3d'   => array(
            'basicdata/customer_id' => 'D200411',
            'basicdata/shop_id'     => 'seamless3D',
            'basicdata/secret'      => 'DP4TMTPQQWFJW34647RM798E9A5X7E8ATP462Z4VGZK53YEJ3JWXS98B9P4F',
            'basicdata/backendpw'   => '2g4f9q2m'
        )
    );

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->_localeResolver  = $localeResolver;
        $this->_productMetadata = $productMetadata;
        $this->_request         = $context->getRequest();
        parent::__construct($context);
    }


    /**
     * return qenta related config data
     *
     * @param null $field
     *
     * @return mixed
     */
    public function getConfigData($field = null)
    {
        $type = $this->scopeConfig->getValue('qenta_checkoutseamless/basicdata/configuration',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (isset( $this->_presets[$type] ) && isset( $this->_presets[$type][$field] )) {
            return $this->_presets[$type][$field];
        }

        $path = 'qenta_checkoutseamless';
        if ($field !== null) {
            $path .= '/' . $field;
        }

        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * return store related config data
     *
     * @param $field
     *
     * @return mixed
     */
    public function getStoreConfigData($field)
    {
        return $this->scopeConfig->getValue($field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * return config data as needed by the client library
     *
     * @return array
     */
    public function getConfigArray()
    {
        $cfg                = Array('LANGUAGE' => $this->getLanguage());
        $cfg['CUSTOMER_ID'] = $this->getConfigData('basicdata/customer_id');
        $cfg['SHOP_ID']     = $this->getConfigData('basicdata/shop_id');
        $cfg['SECRET']      = $this->getConfigData('basicdata/secret');

        return $cfg;
    }

    /**
     * return config data for backend client
     *
     * @return array
     */
    protected function getBackendConfigArray()
    {
        $cfg             = $this->getConfigArray();
        $cfg['PASSWORD'] = $this->getConfigData('basicdata/backendpw');

        return $cfg;
    }

    /**
     * returns config preformated as string, used in support email
     * without security sensitive data
     *
     * @return string
     */
    public function getConfigString()
    {
        $ret     = '';
        $exclude = array('secret', 'backendpw');
        foreach ($this->getConfigData() as $group => $fields) {
            foreach ($fields as $field => $value) {
                if (in_array($field, $exclude)) {
                    continue;
                }
                if (strlen($ret)) {
                    $ret .= "\n";
                }
                $ret .= sprintf("%s: %s", $field, $value);
            }
        }

        return $ret;
    }

    /**
     * check if toolkit is available for backend operations
     *
     * @return bool
     */
    public function isBackendAvailable()
    {
        return strlen($this->getConfigData('basicdata/backendpw')) > 0;
    }

    /**
     * return client for sending backend operations
     *
     * @return \QentaCEE\QMore\BackendClient
     */
    public function getBackendClient()
    {
        return new \QentaCEE\QMore\BackendClient($this->getBackendConfigArray());
    }

    /**
     * return plugin information
     *
     * @return string
     */
    public function getPluginVersion()
    {
        $versionInfo = $this->getVersionInfo();

        return \QentaCEE\QMore\FrontendClient::generatePluginVersion($versionInfo['product'],
            $versionInfo['productVersion'], $versionInfo['pluginName'], $versionInfo['pluginVersion']);
    }

    /**
     * version information
     *
     * @return array
     */
    public function getVersionInfo()
    {
        return [
            'product'        => 'Magento2',
            'productVersion' => $this->_productMetadata->getVersion(),
            'pluginName'     => $this->_pluginName,
            'pluginVersion'  => $this->_pluginVersion
        ];
    }

    /**
     * get current language
     *
     * @return string
     */
    public function getLanguage()
    {
        $locale = explode('_', $this->_localeResolver->getLocale());
        if (is_array($locale) && !empty( $locale )) {
            $locale = $locale[0];
        } else {
            $locale = 'en';
        }

        return $locale;
    }

    /**
     * @return bool|\Zend\Http\Header\HeaderInterface
     */
    public function getUserAgent()
    {
        return $this->_request->getHeader('USER_AGENT');
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and $_SERVER['HTTP_X_FORWARDED_FOR']) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * translate strings
     *
     * @param $txt
     *
     * @return \Magento\Framework\Phrase
     */
    public function __($txt)
    {
        return __($txt);
    }

    /**
     * return link to payolution privacy consent
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getPayolutionLink($mId)
    {
        $mId = urlencode(base64_encode($mId));

        if (strlen($mId)) {
            return sprintf('<a href="https://payment.payolution.com/payolution-payment/infoport/dataprivacyconsent?mId=%s" target="_blank">%s</a>',
                $mId, $this->__('consent'));
        } else {
            return $this->__('consent');
        }
    }

    /**
     * calculate quote checksum, it's verified after the return from the payment page
     * detect fraud attempts (cart modifications during checkout)
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return string
     */
    public function calculateQuoteChecksum($quote)
    {
        $data = round($quote->getGrandTotal(), 2) .
            $quote->getBaseCurrencyCode() .
            $quote->getCustomerEmail();

        foreach ($quote->getAllVisibleItems() as $item) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $data .= $item->getSku();
            $data .= round($item->getRowTotal(), 2);
            $data .= round($item->getTaxAmount(), 2);
        }

        $address = $quote->getBillingAddress();
        $data .= $address->getName() .
                 $address->getCompany() .
                 $address->getCity() .
                 $address->getPostcode() .
                 $address->getCountryId() .
                 $address->getCountry() .
                 $address->getRegion() .
                 $address->getStreetLine(1) .
                 $address->getStreetLine(2);

        $address = $quote->getShippingAddress();
        $data .= $address->getName() .
                 $address->getCompany() .
                 $address->getCity() .
                 $address->getPostcode() .
                 $address->getCountryId() .
                 $address->getCountry() .
                 $address->getRegion() .
                 $address->getStreetLine(1) .
                 $address->getStreetLine(2);

        return hash_hmac('sha512', $data, $this->getConfigData('basicdata/secret'));
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param string $his
     *
     * @return bool
     */
    public function compareQuoteChecksum($quote, $his)
    {
        $mine = $this->calculateQuoteChecksum($quote);
        if ($mine != $his) {
            $this->_logger->debug(__METHOD__ . ':quote checksum mismatch');

            return false;
        }

        return true;
    }


    /**
     * return financial institutions from qenta (backend operation)
     *
     * @param string $paymentType
     *
     * @return array
     */
    public function getFinancialInstitutions($paymentType)
    {
        if (!$this->isBackendAvailable())
            return [];
        
        $backendClient = $this->getBackendClient();

        try  {
            $response = $backendClient->getFinancialInstitutions($paymentType);
        } catch (\Exception $e) {
            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());
            return [];
        }

        if (!$response->hasFailed()) {
            $ret = $response->getFinancialInstitutions();
            $c   = null;
            if (class_exists('Collator')) {
                $c = new \Collator('root');
            }

            uasort($ret, function ($a, $b) use ($c) {
                if ($c === null) {
                    return strcmp($a['id'], $b['id']);
                } else {
                    return $c->compare($a['name'], $b['name']);
                }
            });

            return $ret;
        } else {
            $this->_logger->debug(__METHOD__ . ':' . print_r($response->getErrors(), true));
            return [];
        }
    }
}

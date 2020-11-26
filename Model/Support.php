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

namespace Qenta\CheckoutSeamless\Model;

class Support
{

    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Module\ModuleList\Loader
     */
    protected $_moduleLoader;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * @var \Qenta\CheckoutSeamless\Model\App\Config\ScopePool
     */
    protected $_scopePool;

    protected $_moduleBlacklist = [
        'Magento_Store',
        'Magento_AdvancedPricingImportExport',
        'Magento_Directory',
        'Magento_Theme',
        'Magento_Backend',
        'Magento_Backup',
        'Magento_Eav',
        'Magento_Customer',
        'Magento_BundleImportExport',
        'Magento_AdminNotification',
        'Magento_CacheInvalidate',
        'Magento_Indexer',
        'Magento_Cms',
        'Magento_CatalogImportExport',
        'Magento_Catalog',
        'Magento_Rule',
        'Magento_Msrp',
        'Magento_Search',
        'Magento_Bundle',
        'Magento_Quote',
        'Magento_CatalogUrlRewrite',
        'Magento_Widget',
        'Magento_SalesSequence',
        'Magento_CheckoutAgreements',
        'Magento_Payment',
        'Magento_Downloadable',
        'Magento_CmsUrlRewrite',
        'Magento_Config',
        'Magento_ConfigurableImportExport',
        'Magento_CatalogInventory',
        'Magento_SampleData',
        'Magento_Contact',
        'Magento_Cookie',
        'Magento_Cron',
        'Magento_CurrencySymbol',
        'Magento_CatalogSearch',
        'Magento_CustomerImportExport',
        'Magento_CustomerSampleData',
        'Magento_Deploy',
        'Magento_Developer',
        'Magento_Dhl',
        'Magento_Authorization',
        'Magento_User',
        'Magento_ImportExport',
        'Magento_Sales',
        'Magento_CatalogRule',
        'Magento_Email',
        'Magento_EncryptionKey',
        'Magento_Fedex',
        'Magento_GiftMessage',
        'Magento_Checkout',
        'Magento_GoogleAnalytics',
        'Magento_GoogleOptimizer',
        'Magento_GroupedImportExport',
        'Magento_GroupedProduct',
        'Magento_Tax',
        'Magento_DownloadableImportExport',
        'Magento_Integration',
        'Magento_LayeredNavigation',
        'Magento_Marketplace',
        'Magento_MediaStorage',
        'Magento_ConfigurableProduct',
        'Magento_MsrpSampleData',
        'Magento_Multishipping',
        'Magento_NewRelicReporting',
        'Magento_Newsletter',
        'Magento_OfflinePayments',
        'Magento_SalesRule',
        'Magento_OfflineShipping',
        'Magento_PageCache',
        'Magento_Captcha',
        'Magento_Persistent',
        'Magento_ProductAlert',
        'Magento_Weee',
        'Magento_ProductVideo',
        'Magento_CatalogSampleData',
        'Magento_Reports',
        'Magento_RequireJs',
        'Magento_Review',
        'Magento_BundleSampleData',
        'Magento_Rss',
        'Magento_DownloadableSampleData',
        'Magento_OfflineShippingSampleData',
        'Magento_ConfigurableSampleData',
        'Magento_SalesSampleData',
        'Magento_ProductLinksSampleData',
        'Magento_ThemeSampleData',
        'Magento_ReviewSampleData',
        'Magento_SendFriend',
        'Magento_Ui',
        'Magento_Sitemap',
        'Magento_CatalogRuleConfigurable',
        'Magento_Swagger',
        'Magento_Swatches',
        'Magento_SwatchesSampleData',
        'Magento_GroupedProductSampleData',
        'Magento_TaxImportExport',
        'Magento_TaxSampleData',
        'Magento_GoogleAdwords',
        'Magento_CmsSampleData',
        'Magento_Translation',
        'Magento_Shipping',
        'Magento_Ups',
        'Magento_UrlRewrite',
        'Magento_CatalogRuleSampleData',
        'Magento_Usps',
        'Magento_Variable',
        'Magento_Version',
        'Magento_Webapi',
        'Magento_SalesRuleSampleData',
        'Magento_CatalogWidget',
        'Magento_WidgetSampleData',
        'Magento_Wishlist',
        'Magento_WishlistSampleData'
    ];

    /**
     * @param \Qenta\CheckoutSeamless\Helper\Data $dataHelper
     * @param \Qenta\CheckoutSeamless\Model\App\Config\ScopePool $scopePool
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Module\ModuleList\Loader $moduleLoader
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Qenta\CheckoutSeamless\Helper\Data $dataHelper,
        \Qenta\CheckoutSeamless\Model\App\Config\ScopePool $scopePool,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Module\ModuleList\Loader $moduleLoader,
        \Magento\Payment\Model\Config $paymentConfig
    ) {
        $this->_dataHelper       = $dataHelper;
        $this->_scopePool        = $scopePool;
        $this->_transportBuilder = $transportBuilder;
        $this->_moduleLoader     = $moduleLoader;
        $this->_paymentConfig    = $paymentConfig;
    }

    /**
     * @param \Magento\Framework\DataObject $postObject
     *
     * @return bool
     * @throws \Exception
     */
    public function sendrequest($postObject)
    {
        if (!filter_var($postObject->getData('to'), FILTER_VALIDATE_EMAIL)) {
            throw new \Exception($this->_dataHelper->__('Please enter a valid e-mail address.'));
        }

        if (strlen(trim($postObject->getData('replyto')))) {
            if (!filter_var($postObject->getData('replyto'), FILTER_VALIDATE_EMAIL)) {
                throw new \Exception($this->_dataHelper->__('Please enter a valid e-mail address (reply to).'));
            }
            $this->_transportBuilder->setReplyTo(trim($postObject->getData('replyto')));
        }

        $sender = [
            'name'  => $this->_dataHelper->getStoreConfigData('trans_email/ident_general/name'),
            'email' => $this->_dataHelper->getStoreConfigData('trans_email/ident_general/email'),
        ];

        if (!strlen($sender['email'])) {
            throw new \Exception('Please set your shop e-mail address!');
        }

        $modules = [];
        foreach ($this->_moduleLoader->load() as $module) {
            if (!in_array($module['name'], $this->_moduleBlacklist))
                $modules[] = $module['name'];
        }
        natsort($modules);

        $payments = $this->_paymentConfig->getActiveMethods();
        /** @var \Magento\Framework\App\Config\Data $cfg */
        $cfg = $this->_scopePool->getScope(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $foreign = array();
        $mine    = array();
        foreach ($payments as $paymentCode => $paymentModel) {

            /** @var AbstractPayment $paymentModel */
            $method = array(
                'label'  => $paymentModel->getTitle(),
                'value'  => $paymentCode,
                'config' => []
            );

            if (preg_match('/^qenta_/i', $paymentCode)) {
                $method['config']   = $cfg->getValue('payment/' . $paymentCode);
                $mine[$paymentCode] = $method;
            } else {
                $foreign[$paymentCode] = $method;
            }
        }

        $versioninfo = new \Magento\Framework\DataObject();
        $versioninfo->setData($this->_dataHelper->getVersionInfo());

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('contact_support_email')
            ->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'data'        => $postObject,
                'modules'     => $modules,
                'foreign'     => $foreign,
                'mine'        => $mine,
                'configstr'   => $this->_dataHelper->getConfigString(),
                'versioninfo' => $versioninfo
            ])
            ->setFrom($sender)
            ->addTo($postObject->getData('to'))
            ->getTransport();

        $transport->sendMessage();

        return true;
    }
}

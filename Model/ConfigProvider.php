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

namespace Qenta\CheckoutSeamless\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var string[]
     */
    protected $methodCodes = [
        Payment\Ccard::CODE,
        Payment\Ccardmoto::CODE,
        Payment\Maestro::CODE,
        Payment\Eps::CODE,
        Payment\Ideal::CODE,
        Payment\Giropay::CODE,
        Payment\Tatrapay::CODE,
        Payment\Skrillwallet::CODE,
        Payment\Bmc::CODE,
        Payment\P24::CODE,
        Payment\Poli::CODE,
        Payment\Moneta::CODE,
        Payment\Ekonto::CODE,
        Payment\Trustly::CODE,
        Payment\Paybox::CODE,
        Payment\Paysafecard::CODE,
        Payment\Paypal::CODE,
        Payment\Epaybg::CODE,
        Payment\Sepa::CODE,
        Payment\Invoice::CODE,
        Payment\Invoiceb2b::CODE,
        Payment\Installment::CODE,
        Payment\Voucher::CODE,
        Payment\Trustpay::CODE,
        Payment\Sofortbanking::CODE,
    ];

    /**
     * @var \Qenta\CheckoutSeamless\Model\AbstractPayment[]
     */
    protected $methods = [];

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

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
     * @param \Qenta\CheckoutSeamless\Helper\Data $helper
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     *
     */
    public function __construct(
        \Qenta\CheckoutSeamless\Helper\Data $helper,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->_dataHelper   = $helper;
        $this->paymentHelper = $paymentHelper;
        $this->escaper       = $escaper;
        $this->assetRepo     = $assetRepo;

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $this->paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];

        /*
         * common config data
         */

        foreach ($this->methodCodes as $code) {
            $config['payment'][$code]['paymentMethod'] = $this->getPaymentMethod($code);
            $config['payment'][$code]['instructions']  = $this->getInstructions($code);
            $config['payment'][$code]['displaymode']   = 'popup'; // always popup for seamless
            $config['payment'][$code]['logo_url']      = $this->getLogoUrl($code);
        }

        /*
         * EPS financial institutions
         */
        $fis = \QentaCEE\QMore\PaymentType::getFinancialInstitutions(\QentaCEE\QMore\PaymentType::EPS);

        $epsFinancialInstitutions = [];
        foreach ($fis as $k => $v) {
            $epsFinancialInstitutions[] = ['value' => $k, 'label' => html_entity_decode($v)];
        }
        $c = new \Collator('root');
        usort($epsFinancialInstitutions, function ($a, $b) use ($c) {
            return $c->compare($a['label'], $b['label']);
        });
        array_unshift($epsFinancialInstitutions,
            ['value' => '', 'label' => $this->_dataHelper->__('Choose your bank...')]);

        $config['payment'][Payment\Eps::CODE]['financialinstitutions'] = $epsFinancialInstitutions;

        /*
         * IDEAL financial institutions
         */

        $fis = \QentaCEE\QMore\PaymentType::getFinancialInstitutions(\QentaCEE\QMore\PaymentType::IDL);

        $idealFinancialInstitutions = [];
        foreach ($fis as $k => $v) {
            $idealFinancialInstitutions[] = ['value' => $k, 'label' => htmlspecialchars_decode($v)];
        }
        array_unshift($idealFinancialInstitutions,
            ['value' => '', 'label' => $this->_dataHelper->__('Choose your bank...')]);

        $config['payment'][Payment\Ideal::CODE]['financialinstitutions'] = $idealFinancialInstitutions;

        /*
         * Trustpay financial institutions
         */

        $fis = $this->_dataHelper->getFinancialInstitutions(\QentaCEE\QMore\PaymentType::TRUSTPAY);

        $trustpayFinancialInstitutions = [];
        foreach ($fis as $fi) {
            $trustpayFinancialInstitutions[] = ['value' => $fi['id'], 'label' => $fi['name']];
        }
        array_unshift($trustpayFinancialInstitutions,
            ['value' => '', 'label' => $this->_dataHelper->__('Choose your bank...')]);

        $config['payment'][Payment\Trustpay::CODE]['financialinstitutions'] = $trustpayFinancialInstitutions;

        /*
         * Invoice/installment
         */

        $config['payment'][Payment\Invoice::CODE]['provider']     = $this->methods[Payment\Invoice::CODE]->getProvider();
        $config['payment'][Payment\Invoiceb2b::CODE]['provider']  = $this->methods[Payment\Invoiceb2b::CODE]->getProvider();
        $config['payment'][Payment\Installment::CODE]['provider'] = $this->methods[Payment\Installment::CODE]->getProvider();

        $txt =
            $this->_dataHelper->__('I agree that the data which are necessary for the liquidation of purchase on account and which are used to complete the identy and credit check are transmitted to payolution. My %s can be revoked at any time with effect for the future.');

        $payolutionLink = $this->_dataHelper->getPayolutionLink($this->methods[Payment\Invoice::CODE]->getConfigData('payolution_mid'));
        if ($this->methods[Payment\Invoice::CODE]->getProvider() == 'payolution' && $this->methods[Payment\Invoice::CODE]->getConfigData('payolution_terms')) {
            $config['payment'][Payment\Invoice::CODE]['consenttxt']    = sprintf($txt, $payolutionLink);
            $config['payment'][Payment\Invoiceb2b::CODE]['consenttxt'] = sprintf($txt, $payolutionLink);
        }

        $payolutionLink = $this->_dataHelper->getPayolutionLink($this->methods[Payment\Installment::CODE]->getConfigData('payolution_mid'));
        if ($this->methods[Payment\Installment::CODE]->getProvider() == 'payolution' && $this->methods[Payment\Installment::CODE]->getConfigData('payolution_terms')) {
            $config['payment'][Payment\Installment::CODE]['consenttxt'] = sprintf($txt, $payolutionLink);
        }

        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     *
     * @return string
     */
    protected function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }


    /**
     * Get qenta payment type
     *
     * @param string $code
     *
     * @return string
     */
    protected function getPaymentMethod($code)
    {
        return $this->methods[$code]->getMethod();
    }

    protected function getLogoUrl($code)
    {
        //$params = array_merge(['_secure' => $this->getRequest()->isSecure()], $params);
        $logo = $this->methods[$code]->getLogo();
        if ($logo === false) {
            return false;
        }

        return $this->assetRepo->getUrlWithParams('Qenta_CheckoutSeamless::images/' . $logo, ['_secure' => true]);
    }
}


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

class Test
{

    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @param \Qenta\CheckoutSeamless\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Qenta\CheckoutSeamless\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {

        $this->_dataHelper = $dataHelper;
        $this->_logger     = $logger;
    }

    public function config($urls)
    {
        $returnUrl = $urls['return'];

        $init = new \QentaCEE\QMore\FrontendClient($this->_dataHelper->getConfigArray());
        $init->setPluginVersion($this->_dataHelper->getPluginVersion());

        $init->setOrderReference('Configtest #' . uniqid());

        if ($this->_dataHelper->getConfigData('options/sendconfirmemail')) {
            $init->setConfirmMail($this->_dataHelper->getStoreConfigData('trans_email/ident_general/email'));
        }

        $consumerData = new \QentaCEE\Stdlib\ConsumerData();
        $consumerData->setIpAddress($this->_dataHelper->getClientIp());
        $consumerData->setUserAgent($this->_dataHelper->getUserAgent());

        $init->setAmount(10)
             ->setCurrency('EUR')
             ->setPaymentType(\QentaCEE\QMore\PaymentType::CCARD)
             ->setOrderDescription('Configtest #' . uniqid())
             ->setSuccessUrl($returnUrl)
             ->setPendingUrl($returnUrl)
             ->setCancelUrl($returnUrl)
             ->setFailureUrl($returnUrl)
             ->setConfirmUrl($urls['confirm'])
             ->setServiceUrl($this->_dataHelper->getConfigData('options/service_url'))
             ->setConsumerData($consumerData);

        $initResponse = $init->initiate();

        if ($initResponse->getStatus() == \QentaCEE\QMore\Response\Initiation::STATE_FAILURE) {

            $msg = implode(',', array_map(function ($e) {
                /** @var \QentaCEE\QMore\Error $e */
                return $e->getConsumerMessage();
            }, $initResponse->getErrors()));
            if (!strlen($msg)) {
                $msg = $initResponse = implode(',', array_map(function ($e) {
                    /** @var \QentaCEE\QMore\Error $e */
                    return $e->getPaySysMessage();
                }, $initResponse->getErrors()));;
            }

            throw new \Exception($msg);
        }

        return true;
    }
}

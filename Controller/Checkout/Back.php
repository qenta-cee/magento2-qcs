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

namespace Qenta\CheckoutSeamless\Controller\Checkout;

use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class Back extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $_request;

    /**
     * @var \Qenta\CheckoutSeamless\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var CheckoutCart
     */
    protected $_cart;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $_quoteManagement;

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	protected $_quoteRepository;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Qenta\CheckoutSeamless\Model\OrderManagement
     */
    protected $_orderManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Qenta\CheckoutSeamless\Helper\Data $helper
     * @param CheckoutCart $cart
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Qenta\CheckoutSeamless\Model\OrderManagement $orderManagement
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Qenta\CheckoutSeamless\Helper\Data $helper,
        CheckoutCart $cart,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
	    \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Qenta\CheckoutSeamless\Model\OrderManagement $orderManagement
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_dataHelper        = $helper;
        $this->_cart              = $cart;
        $this->_url               = $context->getUrl();
        $this->_orderSender       = $orderSender;
        $this->_logger            = $logger;
        $this->_checkoutSession   = $checkoutSession;
        $this->_quoteManagement   = $quoteManagement;
	    $this->_quoteRepository   = $quoteRepository;
        $this->_orderManagement   = $orderManagement;
        $this->_order             = $order;
    }

    public function execute()
    {
        $redirectTo = 'checkout/cart';
        $defaultErrorMessage = $this->_dataHelper->__('An error occurred during the payment process');

        try {

            if (!$this->_request->isGet()) {
                throw new \Exception('Not a GET request');
            }

            $orderId = (string) $this->_request->getQuery('mage_orderId');

            if (!strlen($this->_request->getQuery('mage_orderId'))) {
                throw new \Exception('Magento OrderId is missing');
            }

            $this->_order->loadByIncrementId($orderId);
            $orderExists = (bool) $this->_order->getId();

            $paymentInfo = new \Magento\Framework\DataObject();
            /* if we have no order fetch info from cart */
            if ($orderExists) {
                $paymentInfo->setData($this->_order->getPayment()->getAdditionalInformation());
            } else {
                $paymentInfo->setData($this->_cart->getQuote()->getPayment()->getAdditionalInformation());
            }

            $this->_logger->debug(__METHOD__ . ':' . print_r($paymentInfo->getData(), true));

            $orderCreation = $paymentInfo['mage_orderCreation'];
            $quoteId = $paymentInfo['mage_quoteId'];

            if (!strlen($paymentInfo['mage_orderCreation'])) {
                throw new \Exception('Magento orderCreation is missing');
            }

            if (!strlen($paymentInfo['mage_quoteId'])) {
                throw new \Exception('Magento QuoteId is missing');
            }

            if ($orderCreation == 'before' && !$orderExists) {
                throw new \Exception('Order not found');
            }

            if ($paymentInfo['paymentState'] === null)
            {
                $this->_logger->debug(__METHOD__ . ':order not processed via confirm server2server request, check your packetfilter!');
                $this->messageManager->addErrorMessage($this->_dataHelper->__('An internal error occurred during the payment process!'));
            } else {

                switch ($paymentInfo['paymentState']) {
                    case \QentaCEE\QMore\ReturnFactory::STATE_SUCCESS:
                    case \QentaCEE\QMore\ReturnFactory::STATE_PENDING:
                        if ($paymentInfo['paymentState'] == \QentaCEE\QMore\ReturnFactory::STATE_PENDING) {
                            $this->messageManager->addNoticeMessage($this->_dataHelper->__('Your order will be processed as soon as we receive the payment confirmation from your bank.'));
                        }

                        /* needed for success page otherwise magento redirects to cart */
                        $this->_checkoutSession->setLastQuoteId($this->_order->getQuoteId());
                        $this->_checkoutSession->setLastSuccessQuoteId($this->_order->getQuoteId());
                        $this->_checkoutSession->setLastOrderId($this->_order->getId());
                        $this->_checkoutSession->setLastRealOrderId($this->_order->getIncrementId());
                        $this->_checkoutSession->setLastOrderStatus($this->_order->getStatus());

                        $redirectTo = 'checkout/onepage/success';
                        break;

                    case \QentaCEE\QMore\ReturnFactory::STATE_CANCEL:
                        $this->messageManager->addNoticeMessage($this->_dataHelper->__('You have canceled the payment process!'));
                        if ($orderCreation == 'before') {
                            $quote = $this->_orderManagement->reOrder($quoteId);
                            $this->_checkoutSession->replaceQuote($quote)->unsLastRealOrderId();
                        }
                        break;

                    case \QentaCEE\QMore\ReturnFactory::STATE_FAILURE:
                        $returnedData = $paymentInfo->getData();
                        $consumerMessage = "";
                        for ($i = 0; $i < $returnedData['errors']; $i++) {
                            if ($returnedData['errors'] != 1) {
                                $consumerMessage .= $i + 1;
                            }
                            $consumerMessage .= htmlspecialchars_decode($returnedData['error_' . ($i + 1) . '_consumerMessage']);
                            if ($returnedData['errors'] != 1) {
                                $consumerMessage .= "<br>";
                            }
                        }

                        if (!strlen($consumerMessage)) {
                            $consumerMessage = $this->_dataHelper->__('An error occurred during the payment process');
                        }

                        $this->messageManager->addErrorMessage($consumerMessage);
                        if ($orderCreation == 'before') {
                            $quote = $this->_orderManagement->reOrder($quoteId);
                            $this->_checkoutSession->replaceQuote($quote)->unsLastRealOrderId();
                        } else {
	                        $quote = $this->_quoteRepository->get($quoteId);
	                        // remove errorinformation from active quote
	                        $quote->getPayment()->unsAdditionalInformation();
	                        $quote->save();
	                        $paymentInfo->unsetData(null);
                        }

                        break;

                    default:
                        throw new \Exception('Unhandled Qenta Checkout Seamless payment state:' . $paymentInfo['consumerMessage']);
                }
            }

            if ($this->_request->getQuery('iframeUsed')) {
                $redirectUrl = $this->_url->getUrl($redirectTo);
                $page = $this->_resultPageFactory->create();
                $page->getLayout()->getBlock('checkout.back')->addData(['redirectUrl' => $redirectUrl]);

                return $page;

            } else {

                $this->_redirect($redirectTo);
            }
        } catch (\Exception $e) {
            if (!$this->messageManager->getMessages()->getCount()) {
                $this->messageManager->addErrorMessage($defaultErrorMessage);
            }
            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());
            $this->_redirect($redirectTo);
        }
    }


}
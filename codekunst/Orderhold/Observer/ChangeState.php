<?php
namespace codekunst\Orderhold\Observer;
use Magento\Framework\ObjectManager\ObjectManager;

use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Store\Model\ScopeInterface;
use codekunst\Orderhold\Observer\Sender;
class ChangeState implements \Magento\Framework\Event\ObserverInterface {
    /** @var \Magento\Framework\Logger\Monolog */
    protected $_logger;
    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $_objectManager;
    protected $_orderFactory;
    protected $_checkoutSession;

    protected $scopeConfig;
    public function __construct(

        ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager,
        Sender $sender
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->sender = $sender;
    }
    /**
     *
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer ) {
        $order = $observer->getEvent()->getOrder();
        $order->hold()->save();
        $email = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
        $name = $this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);

        $this->sender->mailsend($order, $email, $name);


    }
}
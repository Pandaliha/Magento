<?php

namespace codekunst\Orderhold;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use codekunst\Orderhold\Model\MailSender;

class ChangeState {
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;


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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Customer $customer,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        ScopeConfigInterface $scopeConfig,
        MailSender $mailSender,



        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        ObjectManager $objectManager
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_orderRepositoryInterface = $orderRepositoryInterface;
        $this->logger = $logger;
        $this->customer =$customer;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;





        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->mailSender = $mailSender;
    }

    /**
     *
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer ) {

        $order = $observer->getEvent()->getOrder();
        $this->mailSender->execute();

        $order->hold()->save();



    }
}

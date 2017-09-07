<?php

namespace codekunst\Orderhold\Observer;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Sender{
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
    }

public function mailsend($order, $email, $name)
{
    if($order instanceof \Magento\Sales\Model\Order) {

        $emailCust = $order->getCustomerEmail();

        $sender = [
            'name' => $email,
            'email' => $name,
        ];
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('myemail_template')
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->setTemplateVars(
                [
                    'store' => $this->_storeManager->getStore(),
                ]
            )
            ->setFrom($sender)
            ->addTo($emailCust)
            ->getTransport();
        $transport->sendMessage();
    }
}
}

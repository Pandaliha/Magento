<?php
namespace codekunst\Orderhold\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\App\Action\Context;

class SendMail extends \Magento\Backend\App\Action {
    protected $_orderRepositoryInterface;
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
    protected $request;
    protected $scopeConfig;
    public function __construct(
        Context $context,
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
        \Magento\Framework\ObjectManager\ObjectManager $objectManager
    ) {
        parent::__construct($context);
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_orderRepositoryInterface = $orderRepositoryInterface;
        $this->logger = $logger;
        $this->customer =$customer;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;
    }
    /**
     *
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute() {
        if ($this instanceof \Magento\Sales\Model\Order) {
            $order = $this;
            $emailCust = $order->getCustomerEmail();
            $email = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
            $name = $this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);
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
         return $this;
    }
}
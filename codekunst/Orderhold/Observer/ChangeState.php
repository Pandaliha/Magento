<?php
namespace codekunst\Orderhold\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
class ChangeState implements \Magento\Framework\Event\ObserverInterface {
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
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
    protected $_checkoutSession;
    protected $scopeConfig;

    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context,
        ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager
    ) {
        $this->customer =$customer;
        $this->storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;
        $this->_checkoutSession = $checkoutSession;
    }
    public function sendEmail($name, $email, $custEmail, $data)
    {
        $sender = [
            'name' => $email,
            'email' => $name,
        ];
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('myemail_email_template')
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($sender)
            ->addTo($custEmail)
            ->getTransport();
        $transport->sendMessage();
        return $this;
    }
    /**
     *
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer ) {
        $order = $observer->getEvent()->getOrder();
        $orderID = $order->getIncrementId();
        $custEmail = $order->getCustomerEmail();
        $custName = $order->getCustomerName();
        $email = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
        $name = $this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);

        $baseURL = $this->storeManager->getStore()->getBaseUrl();
        $confirmURL = $baseURL . "confirm/Order/OrderConfirm/id/" . $orderID;
        $data = [
            'report_date' => date("j F Y", strtotime('-1 day')),
            'name' => $custName,
            'ordernum' => $orderID,
            'confirm' => $confirmURL,
            'shopowner' => $name
        ];
        $this->sendEmail($email, $name, $custEmail, $data);
        $order->hold()->save();
        //exit();
    }
}
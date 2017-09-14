<?php
namespace codekunst\Orderhold\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\ObjectManager\ObjectManager;
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
    //protected $_logger;
    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $_objectManager;
    //protected $_checkoutSession;
    protected $scopeConfig;

    public function __construct(
        Customer $customer,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        Context $context,
        ScopeConfigInterface $scopeConfig,
        ObjectManager $objectManager
    ) {
        $this->customer =$customer;
        $this->storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
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
        $emailCustomer = $order->getCustomerEmail();
        $nameCustomer = $order->getBillingAddress()->getFirstName();

        $emailSender = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
        $nameSender = $this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);


        $baseURL = $this->storeManager->getStore()->getBaseUrl();
        $confirmURL = $baseURL . "accept/index/index?id=" . $orderID;
        $data = [
            'report_date' => date("j F Y", strtotime('-1 day')),
            'name' => $nameCustomer,
            'ordernum' => $orderID,
            'confirm' => $confirmURL,
            'shopowner' => $nameSender
        ];
        if("shippingmethod_shippingmethod" == $order->getShippingMethod()) {
            $this->sendEmail($emailSender, $nameSender, $emailCustomer, $data);
            $order->hold()->save();
        }
        //exit();
    }
}
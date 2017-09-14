<?php
namespace codekunst\Orderhold\Controller\Index;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\ObjectManager\ObjectManager;
use \Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $objectManager;
    protected $orderRepository;
    protected $orderFactory;
    public function __construct(
        Context $context,
        OrderInterface $order,
        OrderFactory $orderFactory,
        OrderRepositoryInterface $orderRepository,
        ObjectManager $objectManager,
        PageFactory $resultPageFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->_orderFactory = $orderFactory;
        $this->order = $order;
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = $this->order->loadByIncrementId($orderId);
        $order->setState("new")->setStatus("pending");
        $order->save();
        return $this->resultPageFactory->create();
    }
}
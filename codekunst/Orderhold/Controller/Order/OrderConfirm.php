<?php
namespace codekunst\Orderhold\Controller\Order;

use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
class OrderConfirm extends \Magento\Framework\App\Action\Action
{
    protected $_orderFactory;
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $orderr,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_orderFactory = $orderFactory;
        $this->order = $orderr;
    }
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = $this->order->loadByIncrementId($orderId);


        $order->unhold()->save();

        //echo "Order " . $orderId . " is confirmed!";
        exit();
    }
}
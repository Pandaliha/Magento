<?php

namespace codekunst\Orderhold\Observer;

use Magento\Framework\ObjectManager\ObjectManager;

class ChangeState implements \Magento\Framework\Event\ObserverInterface {

    /** @var \Magento\Framework\Logger\Monolog */
    protected $_logger;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $_objectManager;

    protected $_orderFactory;
    protected $_checkoutSession;

    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager
    ) {
        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     *
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer ) {

        $order = $observer->getEvent()->getOrder();
        $order_id = $order->getID();
        //$order = $observer->getOrder();

        //order can't be placed at all
		//try {
			//$this->orderHelper->hold($order);
		// }
		// catch (\Exception $e) {
		// 	$this->logger->debug($e->__toString());
		// }

    //Version 1
            // $order->setState(\Magento\Sales\Model\Order::STATE_HOLDED);
            // $order->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_HOLDED));


          //$order->setCustomerIsGuest(false)->setCustomerId($customer->getId())->setCustomerEmail($customer->getEmail());
//Version 2
          // place order button causes infinite loading and order isn't placed
            // $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true);
            // $order->save();

            //redirects (wegen die) to cart ABER SETZT BESTELLUNG ON HOLD, sendet keine email
            //if($order->canHold()) {

            $order->hold()->save();

          //}


        //die();
    }
}

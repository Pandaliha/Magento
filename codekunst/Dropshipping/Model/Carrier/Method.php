<?php
namespace codekunst\Dropshipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Checkout\Helper\Cart;

/**
 * Extends AbstractCarrier
 */
class Method extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'shippingmethod';


    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    //  shipping information as parameter and returns all available rates
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }



        /** @var Result $result */
        $result = $this->_rateResultFactory->create();
        if ($this->getConfigData('type') == 'O') {
            // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') {
            // per item
            $count = $request->getPackageQty();
            $shippingPrice = $request->getPackageQty() * $this->getConfigData(
                'price'
            ) - $this->getFreeBoxes() * $this->getConfigData(
                'price'
            );
        } else {
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false && $count < 6) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('shippingmethod');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('shippingmethod');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);


                $result->append($method);
                //return false; verhindert alle Shipping Optionen


        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
      // TODO limit dropshipping for number of items
      //IF MORE THAN 5 ITEMS; SET ACTIVE TO 0

      // $cartItemsCount = Mage::helper('checkout/cart')->getCart()->getItemsCount();

      // $count = $this->helper('checkout/cart')->getSummaryCount();

      //$this -> getConfigData('active')->saveConfig('active', '0', 'default', 0);

    //  $counter = $this->helper('\Magento\Checkout\Helper\Cart');
      //Mage::helper('checkout/cart')->getSummaryCount()

      // $noOfItems = $counter->getSummaryCount();
      //     if($noOfItems > 5){
      //       $this->setConfigData('codekunst_Dropshipping/etc/config', 'A', 'stores', 1);

      // Mage::helper('checkout/cart')->getSummaryCount();

      // $count = $this->helper('checkout/cart')->getSummaryCount();


      // $count = $this->getSingleton('checkout/cart')->getSummaryCount();
      //
      //   if($count < 5){
        return ['shippingmethod' => $this->getConfigData('name')];
      //}
}
}

<?php
namespace codekunst\Dropshipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

/**
 * Extends AbstractCarrier
 */
class Method extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $cartHelper;


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
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->cartHelper = $cartHelper;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     *
     */
    //  shipping information as parameter and returns all available rates
    public function collectRates(RateRequest $request)
    {
        /*
         * Shipping only available if configflag set on active
         *
         * */
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** Calculating the price of shipping
         * @var Result $result
         */
        $result = $this->_rateResultFactory->create();
        if ($this->getConfigData('type') == 'O') {
            // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') {
            // per item
            $shippingPrice = $request->getPackageQty() * $this->getConfigData(
                'price'
            );
        } else {
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        /*
         * Only offer Dropshipping option if there are less than 6 Items bought
         * */
        if ($this->cartHelper->getSummaryCount() < 6) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('shippingmethod');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('shippingmethod');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            $result->append($method);


        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['shippingmethod' => $this->getConfigData('name')];
}
}

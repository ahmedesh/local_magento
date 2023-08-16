<?php declare(strict_types=1);
namespace CustomKaza\SalesOrder\Observer;

use DateTime;
use http\Client\Response;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use stdClass;
use Magento\Framework\App\ObjectManager;

class AfterPlaceOrder implements ObserverInterface
{
    protected $customerSession;

    protected $cookieManager;
    protected $logger;
    protected $order;
    protected $customerFactory;
    protected $customerRepositoryInterface;
    protected $_productRepository;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    public function __construct(
        \Magento\Customer\Model\Session                  $customerSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        LoggerInterface                                  $logger,
        \Magento\Sales\Model\Order                       $order,
        \Magento\Customer\Model\CustomerFactory          $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->logger = $logger;
        $this->order = $order;
        $this->customerFactory = $customerFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->_productRepository = $productRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
    }

    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    /**
     * @throws NoSuchEntityException
     * @throws \Zend_Log_Exception
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/last_test_of_houida.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $order = $observer->getEvent()->getOrder();

        $objectManager = ObjectManager::getInstance();
        $cookie = $objectManager->create('Magento\Framework\Stdlib\CookieManagerInterface');
        $locationName = $objectManager->create('MageWorx\Locations\Api\LocationRepositoryInterface');

        if ($order->getShippingMethod() == 'mageworxpickup_mageworxpickup') {

            $locationId = $cookie->getCookie('mageworx_location_id');

            $storeName = $locationName->getById($locationId)->getName();
            $storeCode = $locationName->getById($locationId)->getCode();

        }else{
            $storeName ="";
            $storeCode = "";
        }
        $customer_id = $order->getCustomerId();
        $increment_id = $order->getIncrementId();

                $logger->info('start');
        $logger->info($cookie->getCookie('mageworx_location_id'));
        $logger->info($locationName->getById($locationId)->getName());
        $logger->info($locationName->getById($locationId)->getCode());
        $logger->info("houidaaaaaa");
        $logger->info($increment_id);
        $logger->info('End');
//   Start Login
        $curl_token = curl_init();
        curl_setopt_array($curl_token, array(
            CURLOPT_URL => 'https://kazaeg.com/Kaza/identity/connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=password&client_id=DAF6FA91-8B44-82A2-DE40-15BBA72925D7@Kaza&client_secret=mbW1HvUD2-Cwv_WMjFbWiw&username=admin&password=Azxcvb@123&scope=api',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl_token);
        $httpCode = curl_getinfo($curl_token, CURLINFO_HTTP_CODE);

        curl_close($curl_token);
        $json = json_decode($response);


//==============================End Login=========================

// ======================= get Order Details =========================
//        if ($httpCode == 204) {



//arr
//for each item
//fill arr
// arr to json

// ====== GET acumatica_customer_id value to insert in customer id =============
        $customer =$this->customerRepositoryInterface->getById($customer_id);
        $acumatica_id = $customer->getCustomAttribute('Acumatica_Customer_id')->getValue();

        $curl_order = curl_init();

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $currentproduct = $objectManager->create('Magento\Catalog\Model\Product');


        ?>
        <?php
//        /**
//         * @var $block \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
//         */
        ?>
        <?php
//        $order = $block->getOrder();
//        $logger->info(print_r($order->getShippingDescription() , true));
//            $logger->info('getShippingDescription: ' . $order->getShippingDescription());
//        $logger->info(print_r($order , true));
//            $logger->info($block->escapeHtml($order->getShippingDescription()));

        foreach ($order->getAllItems() as $item) {

            $productId = $item->getProductId();
            $skus = $item->getSku();
            $productQty = $item->getQtyOrdered();

            $currentproduct->load($productId);
            $DcAttribute = $currentproduct->getCustomAttribute('dc');
            if ($DcAttribute){
                $DcAttribute = $DcAttribute->getValue();
            }
            $myarray = str_split($skus,2 );
            $skus = implode("-", $myarray);
            $array_data[] = ["InventoryID" => ["value" => "$skus"], "OrderQty" => ["value" => $productQty], "WarehouseID" => ["value" => $storeCode], "Location" => ["value" => "01"], "DC" => ["value" => "$DcAttribute"??'']];
        }
//        $array_data[] = ["InventoryID" => ["value" => "MP-47-7"], "OrderQty" => ["value" => 1.000000], "DC" => ["value" => "test"]];

        $arrayVar = [
            "CustomerID" => ["value" => "$acumatica_id"],
            "MagentoOrderNbr" => ["value" => "$increment_id"],
            "OrderType" => ["value" => "CS"],
            "Branch" => ["value" => "KAZA"],
            "IsPaied"=> ["value" => "True" ],
            "FromMagento"=> ["value" => "True"],
            "Details" =>
                $array_data,
        ];
        $data_string = json_encode($arrayVar);

        curl_setopt_array($curl_order, array(
            CURLOPT_URL => 'https://kazaeg.com/Kaza/entity/magento/20.200.001/SalesOrder',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $json->access_token,
                'Content-Type: application/json'
            ),
        ));
        $result[] = curl_exec($curl_order);
        $httpCode2 = curl_getinfo($curl_order, CURLINFO_HTTP_CODE);
        curl_close($curl_order);


    }
}


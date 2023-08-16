<?php declare(strict_types=1);
namespace CustomKaza\SalesOrder\Observer;

use DateTime;
use http\Client\Response;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use stdClass;

class AfterPlaceOrder implements ObserverInterface
{
    protected $customerSession;

    protected $cookieManager;
    protected $logger;
    protected $order;
    protected $customerFactory;
    protected $customerRepositoryInterface;
    protected $_productRepository;

    public function __construct(
        \Magento\Customer\Model\Session                  $customerSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        LoggerInterface                                  $logger,
        \Magento\Sales\Model\Order                       $order,
        \Magento\Customer\Model\CustomerFactory          $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->logger = $logger;
        $this->order = $order;
        $this->customerFactory = $customerFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->_productRepository = $productRepository;
    }

    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    public function execute(Observer $observer)
    {
//   Start Login
        $curl_token = curl_init();
        curl_setopt_array($curl_token, array(
            CURLOPT_URL => 'https://shantafactory.com/Kaza/identity/connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=password&client_id=DAF6FA91-8B44-82A2-DE40-15BBA72925D7@Gesr El-Suez&client_secret=mbW1HvUD2-Cwv_WMjFbWiw&username=admin&password=Azxcvb@123&scope=api

',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl_token);
        $httpCode = curl_getinfo($curl_token, CURLINFO_HTTP_CODE);

        curl_close($curl_token);
        $json = json_decode($response);

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/after_place_order.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($httpCode);
        $logger->info(print_r($response, true));
        $logger->info($json->access_token);
//==============================End Login=========================

// ======================= get Order Details =========================
//        if ($httpCode == 204) {
        $order = $observer->getEvent()->getOrder();
        $customer_id = $order->getCustomerId();
//arr
//for each item
//fill arr
// arr to json

// ====== GET acumatica_customer_id value to insert in customer id =============
        $customer =$this->customerRepositoryInterface->getById($customer_id);
        $acumatica_id = $customer->getCustomAttribute('Acumatica_Customer_id')->getValue();
        $curl_order = curl_init();
//        $productId = 2045;
//        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
//        $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
//        $name = $currentproduct->getName();
//        $Dc1Attribute = $currentproduct->getCustomAttribute('dc')->getValue();
//      $all_Attributes = $currentproduct->getCustomAttributes();

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $currentproduct = $objectManager->create('Magento\Catalog\Model\Product');

        foreach ($order->getAllItems() as $item) {

//            $logger->info(print_r($item->getData() , true));  // دي بترجعلي الداتا بتاعه الأيتم اللي هو المنتج مش الاوردر

            $productId = $item->getProductId();
             $skus = $item->getSku();
             $productQty = $item->getQtyOrdered();
//            $proName[] = $item->getName();
//            $proDc[] = $item->getDc();

            $currentproduct->load($productId);
            $DcAttribute = $currentproduct->getCustomAttribute('dc');
            if ($DcAttribute){
                $DcAttribute = $DcAttribute->getValue();
            }
//                $DcAttribute = $currentproduct->getCustomAttribute('dc')->getValue();
//            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/after_place_order.log');
//            $logger = new \Zend_Log();
//            $logger->addWriter($writer);
//            $logger->info('productId: ' . $productId);
//            if (isset($DcAttribute) && $DcAttribute != '') {
//                $logger->info('DcAttribute: ' . $DcAttribute);
//                }
//                elseif ($DcAttribute === null){
//                    $logger->info('DcAttribute: is null');
//                }
            $myarray = str_split($skus, );
            $skus = implode("-", $myarray);
            $array_data[] = ["InventoryID" => ["value" => "$skus"], "OrderQty" => ["value" => $productQty], "DC" => ["value" => "$DcAttribute"??'']];

//            $logger->info('sku1: ' . $skus);

        }

        $arrayVar = [
                "CustomerID" => ["value" => "$acumatica_id"],
                "OrderType" => ["value" => "CS"],
                "Branch" => ["value" => "KAZA"],
                "IsPaied"=> ["value" => "True" ],
                "FromMagento"=> ["value" => "True"],
                "Details" =>
                    $array_data,
            ];
            $data_string = json_encode($arrayVar);
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/after_place_order.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer);
//        $logger->info('productId: '.$productId);
//        $logger->info('sku2: '.$skus);
//        $logger->info('qty2: '.$productQty);
//        $logger->info('DcAttribute: '. $DcAttribute);
//        $logger->info('Dc1Attribute: '. $Dc1Attribute);
//        $logger->info('Dc2Attribute: '. $Dc2Attribute);
//        $logger->info(print_r($all_Attributes , true));
        $logger->info(print_r($arrayVar , true));
        $logger->info(print_r($data_string , true));

            curl_setopt_array($curl_order, array(
                CURLOPT_URL => 'https://shantafactory.com/Kaza/entity/magento/20.200.001/SalesOrder',
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
        curl_close($curl_order);
        $logger->info(print_r($result, true));
        $logger->info('acumatica id: ' . $acumatica_id);
        $logger->info('customer id: ' .$customer_id);
    }
}

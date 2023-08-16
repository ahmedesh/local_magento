<?php declare(strict_types=1);

namespace CustomKaza\SalesOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;

class AfterRegister implements ObserverInterface
{
    protected $logger;
    protected $request;
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_customerRepositoryInterface;

    protected $customerRepository;
    public function __construct(
        LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\App\Request\Http $request,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory
    )  {
        $this->customerRepository = $customerRepository;
        $this->logger  = $logger;
        $this->request = $request;
        $this->_customerFactory = $customerFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }


    public function execute(Observer $observer)
    {
        $data = $observer['account_controller'];
        $paramData = $data->getRequest()->getParams();
        $customer = $observer->getCustomer();
        if(!empty($paramData['mobile_number'])){
            $customerModel = $this->_customerFactory->create()->load($customer->getId());
            $customerModel->setMobileNumber($paramData['mobile_number'])->save();
        }
//   Start Token
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

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/after_register.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($httpCode);
        $logger->info(print_r($response, true));
        $logger->info($json->access_token);
//==============================End Token=========================
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $customerFName = $customer->getFirstName();
        $customerLName = $customer->getLastName();
        $customerId =  "" . substr($paramData['mobile_number'], 1); // بشيل الصفر من الرقم
        $phone =  $paramData['mobile_number'];
        $getPostData = $this->request->getPost(); // get request data

//========= send data of customer ==========
    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://shantafactory.com/Kaza/entity/Default/20.200.001/Customer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS =>"{  'CustomerID': {  'value': \"$customerId\" },
                                'CustomerClass': {  'value': '05' },
                                'CustomerName':{  'value': \"$customerFName $customerLName\" },
                                'Maincontact':  {
                                         'phone1': {  'value': \"$phone\" }
                                            }
                                         }",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $json->access_token,
                'Content-Type: application/json',
            ),
        ));
        $response_customer = curl_exec($curl);
//  convert from json to array to get value of customer_id
        $data = json_decode($response_customer, true);

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/after_register.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('success register');
        $logger->info(print_r($response_customer , true));
//        get value of customer id to set in acumatica id
        $acumatcaid = $data['CustomerID']['value'];
        $logger->info('acumatica_id: ' . $acumatcaid);

        $customer = $observer->getEvent()->getCustomer();
        $customer->setCustomAttribute('Acumatica_Customer_id', $acumatcaid);
        $this->customerRepository->save($customer);
//
        curl_close($curl);

    }
}

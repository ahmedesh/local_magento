<?php

declare(strict_types=1);

namespace Macademy\Blog\Controller\Post;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;

class myData implements HttpGetActionInterface
{

//    private $session;
//    private $request;
//
//    public function __construct(
//        Session $session,
//        RequestInterface $request
//    ) {
//        $this->session = $session;
//        $this->request = $request;
//    }

    public function execute()
    {
// using Object Manager

        $om = ObjectManager::getInstance();
//   ======== using Block Class ============      الطريقة الأولي
//        $session = $om->get(session::class);
//        $customerRequest = $om->get(RequestInterface::class);
//        echo '<pre>';
//         var_dump($session->getData());
//        var_dump($customerRequest->getParams());
//         die();

//  ======== using Block Class ============      الطريقة التانيه بس مش شغاله مع الفيرجن دا
//        echo '<pre>';
//        var_dump($this->session->getData());
//        var_dump($this->request->getParams());
//        die();

// ======== using Object Manager also ========       الطريقة التالته
        $customerSession = $om->get(Session::class);
        $customerRequest = $om->get(RequestInterface::class);
        $sessionData = $customerSession->getData();
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerId = $customerSession->getCustomer()->getId();//get id of customer

        $requestData = $customerRequest->getParams();

        var_dump( $sessionData );
        echo "===================== 1 ==========================\r\n";
        var_dump( $customerData );
        echo "===================== 2 =========================\r\n";
        var_dump( $customerId );
        echo "===================== 3 =========================\r\n";
        var_dump( $requestData );
        echo "===================== 4 =========================\r\n";
    }
}

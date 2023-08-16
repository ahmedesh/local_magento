<?php

namespace CronJob\HelloWorld\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;

class Test
{

    /**
     * @throws NoSuchEntityException
     * @throws \Zend_Log_Exception
     * @throws LocalizedException
     */
//    public function execute()
//    {
//
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/cron.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer);
//        $logger->info(__METHOD__);
//
//        return $this;
//
//    }


    protected $storeManager;
    protected $emulation;
    protected $productCollectionFactory;
    protected $productStatus;
    protected $productVisibility;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    public function execute()
    {

        $this->emulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true);


        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');


        $objectManager = ObjectManager::getInstance();
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION');
        $sql2 = "SELECT  price FROM catalog_product_index_price where price >= 100"; // catalog_product_entity
        $store = $connection->prepare($sql2);
        $store->execute();
        $store=$store->fetchAll();

    if ($store) {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/cron.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
    //        $logger->info(print_r($store->price,true));
        $logger->info(print_r($store, true));
    //        $logger->info(__METHOD__);
    //        $logger->info(print_r($collection , true));

        $objectManager = ObjectManager::getInstance();
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION');
        $sql2 = "UPDATE catalog_product_index_price SET price=50 WHERE price>=100";
        $product = $connection->prepare($sql2);
        $product->execute();
        $product=$product->fetchAll();

        $logger->info(print_r($product,true));
    }
    else{
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/cron.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('no store');

    }



        return $collection;


//        return $this;
    }

}

<?php

namespace CustomKaza\OrderCron\Cron;

use Magento\Sales\Model\OrderFactory;

class UpdateOrderStatus
{
    protected $orderFactory;

    public function __construct(OrderFactory $orderFactory)
    {
        $this->orderFactory = $orderFactory;
    }

    public function execute()
    {
        $currentTime = strtotime('-24 hours');

        // Get orders with specified statuses (new, hold, processing, pending)
        $orderCollection = $this->orderFactory->create()->getCollection()
            ->addFieldToFilter('status', ['in' => ['new', 'hold', 'processing', 'pending']])
            ->addFieldToFilter('updated_at', ['lteq' => date('Y-m-d H:i:s', $currentTime)])
            // gteq => stands for "greater than or equal to"
            ->addFieldToFilter('state', ['neq' => \Magento\Sales\Model\Order::STATE_CANCELED])
            // neq stands for "not equal"
            ->addFieldToFilter('state', ['nin' => [\Magento\Sales\Model\Order::STATE_COMPLETE, \Magento\Sales\Model\Order::STATE_CANCELED,
                \Magento\Sales\Model\Order::STATE_CLOSED ]])
            // nin stands for "not in"
            ->addFieldToFilter('status', ['nin' => ['out_for_delivery', 'completed', 'closed', 'canceled']])
            ->addAttributeToSelect('*');

        foreach ($orderCollection as $order) {
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED)
                ->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED)
                ->save();

            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/ordercron.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info('CANCELED SUCCESS');
        }
    }
}



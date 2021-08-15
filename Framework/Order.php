<?php

namespace SmartPack\Framework;

class Order
{
    private $_orderCollection;

    function __construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_orderCollection = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
    }

    function getOrders(array $statuses = ['processing'])
    {
        $collection = $this->_orderCollection->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter(
                'status',
                ['in' => $statuses]
            )
            ->setOrder(
                'created_at',
                'asc'
            );

        return $collection;
    }
}

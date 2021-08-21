<?php

namespace SmartPack\Framework;

class Product
{
    function __construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->productCollection = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
    }

    function getProducts(int $current_page = 1, int $page_size = 50)
    {
        $products = $this->productCollection->create()
            ->addFieldToSelect('*')
            // ->addFieldToFilter('wms_state', [
            //     'nin' => ['synced']
            // ])
            ->addAttributeToFilter([
                [
                    'attribute' => 'status',
                    'eq' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                ],
            ])
            ->setPageSize($page_size)
            ->setCurPage($current_page);


        return $products;
    }

    function updateEntity($table_name, $data, $where)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceConnection = $objectManager->get('Magento\Framework\App\ResourceConnection');

        $connection  = $resourceConnection->getConnection();

        $tableName = $connection->getTableName($table_name);
        $updatedRows = $connection->update($tableName, $data, $where);
    }
}

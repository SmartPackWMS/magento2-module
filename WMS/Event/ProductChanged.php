<?php

namespace SmartPack\WMS\Event;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResourceConnection;

class ProductChanged implements ObserverInterface
{
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $id = $product->getId();

        $connection  = $this->resourceConnection->getConnection();
        $data = [
            "wms_state" => 'changed',
            # "wms_state_updated_at" => new \DateTime() // Missing to update the datetime
        ];

        $where = ['entity_id = ?' => (int)$id];

        $tableName = $connection->getTableName('catalog_product_entity');
        $updatedRows = $connection->update($tableName, $data, $where);
    }
}

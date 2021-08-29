<?php

namespace SmartPack\WMS\Api;

use SmartPack\Framework\Product;

class StockChanged
{
    function __construct(
        \Magento\Framework\Webapi\Rest\Request $request
    ) {
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    function productStockChanged()
    {
        $body = json_decode($this->request->getContent());

        $products = new Product();

        $changed_data = [];
        foreach ($body as $key => $val) {
            $changed_data[] = [
                'sku' => $val->sku,
                'stock' => $val->totalCombined
            ];

            $products->updateEntity('catalog_product_entity', [
                "wms_state" => 'pending',
            ], ['sku = ?' => $val->sku]);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $stockRegistry = $objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');

            $qty = $val->totalCombined;

            $stockItem = $stockRegistry->getStockItemBySku($val->sku);
            $stockItem->setQty($qty);
            $stockItem->setIsInStock((bool)$qty); // this line
            $stockRegistry->updateStockItemBySku($val->sku, $stockItem);
        }

        return $changed_data;
    }
}

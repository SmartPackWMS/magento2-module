<?php

namespace SmartPack\Framework;

class Product
{
    function getProducts()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->productCollection = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

        $products = $this->productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            )
            ->getData();

        return $products;
    }
}

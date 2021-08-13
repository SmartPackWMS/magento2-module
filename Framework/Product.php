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
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            )
            ->setPageSize($page_size)
            ->setCurPage($current_page);


        return $products;
    }
}

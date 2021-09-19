<?php

namespace SmartPack\WMS\Api;

use SmartPack\Framework\Product;

use Magento\Framework\App\{
    RequestInterface,
    ResponseInterface
};

class HookChanged
{
    function __construct(
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    function stockChanged()
    {

        $body = json_decode($this->request->getContent());

        $beartoken = $this->request->getHeader('Authorization');

        # Token access check
        if (!$beartoken) {
            $response = $this->response;
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_403);
            $response->setContent(json_encode([
                'msg' => 'Beartoken access key is not valid'
            ]));
            $response->send();
            die;
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $scopeConfig = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

            $beartoken = str_replace('Bearer ', '', $beartoken);

            if ($scopeConfig->getValue("smartpack_wms/wms_api/wms_api_webhook_token", $storeScope) !== $beartoken) {
                $response = $this->response;
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_403);
                $response->setContent(json_encode([
                    'msg' => 'Beartoken access key is not valid'
                ]));
                $response->send();
                die;
            }
        }


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

    /**
     * @inheritdoc
     */
    function orderChanged()
    {
        return [
            ['msg' => 'demo']
        ];
    }
}

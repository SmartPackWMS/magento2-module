<?php

namespace SmartPack\WMS\Api;

use Exception;
use SmartPack\Framework\Product;

use Magento\Sales\Model\Order;
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

        $changed_data = [];
        foreach ($body as $key => $val) {
            try {
                $state = null;
                switch ($val->state) {
                    case 0:
                        // None
                        break;
                    case 1:
                        // Ready For Packing
                        break;
                    case 2:
                        // Items Missing
                        break;
                    case 3:
                        // Error
                        break;
                    case 4:
                        // Packing
                        break;
                    case 5:
                        // Packed
                        $state = Order::STATE_COMPLETE;
                        break;
                    case 6:
                        // Canceled
                        break;
                }

                if ($state) {
                    $orderId = $val->id;
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
                    $orderState = $state;
                    $order->setState($orderState)->setStatus($state);
                    $order->save();

                    $changed_data[$val->id] = $val->state;
                } else {
                    $changed_data[$val->id] = 'n/a';
                }
            } catch (Exception $e) {
                $changed_data[$val->id] = null;
            }
        }

        return [
            ['data' => $changed_data]
        ];
    }
}

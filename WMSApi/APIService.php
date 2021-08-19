<?php

namespace SmartPack\WMSApi;

use GuzzleHttp\Client;

abstract class APIService
{
    protected $client = null;

    public function __construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        $this->client = new Client([
            'base_uri' => $scopeConfig->getValue("smartpack_wms/wms_api/wms_api_url", $storeScope) ?? 'https://smartpack.dk/api/v1/',
            'timeout'  => 2.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'auth' => [
                $scopeConfig->getValue("smartpack_wms/wms_api/wms_api_username", $storeScope),
                $scopeConfig->getValue("smartpack_wms/wms_api/wms_api_password", $storeScope)
            ]
        ]);
    }
}

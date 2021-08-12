<?php

namespace SmartPack\WMSApi;

use SmartPack\WMSApi\APIService;

class Items extends APIService
{
    function list()
    {
        $data = $this->client->request('GET', 'item/list/');
        echo 'getStatusCode: ' . $data->getStatusCode();

        return $data->getBody();
    }

    function import()
    {
        $data = $this->client->request('POST', 'item/import', [
            'body' => json_encode([[
                "sku" => "test-prod-1",
                "description" => ".",
            ]])
        ]);

        return $data->getBody();
    }
}

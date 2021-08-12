<?php

namespace SmartPack\WMSApi;

use SmartPack\WMSApi\APIService;

class Items extends APIService
{
    function getList()
    {
        $data = $this->client->request('GET', 'item/list/');
        echo 'getStatusCode: ' . $data->getStatusCode();

        return $data->getBody();
    }
}

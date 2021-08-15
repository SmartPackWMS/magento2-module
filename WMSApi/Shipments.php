<?php

namespace SmartPack\WMSApi;

use SmartPack\WMSApi\APIService;

class Shipments extends APIService
{
    /**
     * Documention: https://smartpack.dk/api/v1/#16-Shipment-Create
     */
    function create($attr)
    {
        $data = $this->client->request('POST', 'shipment/create', [
            'body' => json_encode(
                $attr
            )
        ]);
        return $data;
    }
}

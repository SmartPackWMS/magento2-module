<?php

namespace SmartPack\WMS\Api;

class StockChanged
{
    /**
     * @inheritdoc
     */
    function productStockChanged()
    {
        return [
            'message' => 'hook ready to new code!'
        ];
    }
}

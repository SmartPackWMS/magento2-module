<?php

namespace SmartPack\WMS\Api;

interface HookChangedInterface
{
    /**
     * POST for Post api
     * @return string
     */
    function stockChanged();

    /**
     * POST for Post api
     * @return string
     */
    function orderChanged();
}

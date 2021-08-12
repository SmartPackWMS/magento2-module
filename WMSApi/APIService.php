<?php

namespace SmartPack\WMSApi;

use GuzzleHttp\Client;

abstract class APIService
{
    const API_REQUEST_URI = 'https://smartpack.dk/api/v1/';

    protected $client = null;
    private $username = '';
    private $password = '';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_REQUEST_URI,
            'timeout'  => 2.0,
            'headers' => ['Content-Type' => 'application/json'],
            'auth' => [
                $this->username,
                $this->password
            ]
        ]);
    }
}

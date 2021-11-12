<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpHelper
{
    /**
     * @var Client
     */
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Make request by url.
     *
     * @param string $url
     * @return object
     * @throws GuzzleException
     */
    public function makeRequest(string $url): object
    {
        return json_decode($this->httpClient->get($url)->getBody());
    }
}

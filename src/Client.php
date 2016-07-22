<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2016. All rights reserved.
 */

namespace MaxContact;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use MaxContact\Commands\Command;

class Client
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * @var RequestInterface
     */
    private $lastRequest;

    /**
     * @var ResponseInterface
     */
    private $lastResponse;

    /**
     * Client constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $endpoint - Should look something like https://{accountName}api.maxcontact.com/{accountName}api
     */
    public function __construct($username, $password, $endpoint)
    {
        $this->username = $username;
        $this->password = $password;
        $this->endpoint = $endpoint;
        $this->hostname = parse_url($this->endpoint)['host'];
    }

    /**
     * Get the client
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        if (! $this->client) {
            $this->client = new GuzzleClient();
        }

        return $this->client;
    }

    /**
     * Set the Client
     *
     * @param ClientInterface $client
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the authentication token for web service calls
     *
     * @return string
     */
    public function getToken()
    {
        if (! $this->token) {
            $url = $this->endpoint . '/services/apitoken/login/'
                . urlencode($this->username) . '/' . urlencode($this->password);

            $response = $this->getClient()
                ->get($url, [
                    'headers' => [
                        'content-type' => 'application/json'
                    ]
                ]);

            $json = json_decode($response->getBody()->getContents());
            $this->token = $json->TokenKey;
        }

        return $this->token;
    }

    /**
     * Execute the command
     *
     * @param Command $command
     * @return bool
     */
    public function execute(Command $command)
    {
        $cookies = CookieJar::fromArray(['TokenKey' => $this->getToken()], $this->hostname);

        $client = $this->getClient();

        // Get the request method
        $method = $command->getMethod();

        // get the url
        $url = $command->getUrl($this->endpoint);

        // create the request object with the cookie
        $this->lastRequest = $client->createRequest($method, $url, $command->getPayload([
            'cookies' => $cookies
        ]));

        $this->lastResponse = $client->send($this->lastRequest);
        $xml = $this->lastResponse->xml();

        return (isset($xml->Success) && $xml->Success);
    }

    /**
     * Get the last request object
     *
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Get the last response object
     *
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}

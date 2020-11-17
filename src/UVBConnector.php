<?php

namespace webmenedzser\UVBConnector;

use GuzzleHttp\Client;

/**
 * Class UVBConnector
 *
 * @author Ottó Radics <otto@webmenedzser.hu>
 * @package webmenedzser\UVBConnector
 */
class UVBConnector
{
    /**
     * The hash produced by sha256 hashing the e-mail.
     *
     * @var string
     */
    public $hash;

    /**
     * Public API Key.
     *
     * @var string
     */
    public $publicApiKey;

    /**
     * Private API Key.
     *
     * @var string
     */
    public $privateApiKey;

    /**
     * Base URL for API
     *
     * @var string string
     */
    public $baseUrl = 'https://utanvet-ellenor.hu/api/v1/signals/';

    /**
     * @var
     */
    public $response;

    /**
     * @var float $threshold
     */
    public $threshold;

    /**
     * UVBConnector constructor.
     *
     * @param $email
     * @param $publicApiKey
     * @param $privateApiKey
     */
    function __construct($email, $publicApiKey, $privateApiKey)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Remove the string after + before @
            $email = preg_replace('/(.+)\+.*(@.+)/', '$1$2', $email);

            // Hash the string with sha256sum
            $email = hash('sha256', $email);

            $this->hash = $email;
            $this->publicApiKey = $publicApiKey;
            $this->privateApiKey = $privateApiKey;
        }
    }

    /**
     * Check e-mail reputation
     *
     * @return mixed
     */
    public function get()
    {
        $this->_checkUVBService();

        return $this->response;
    }

    public function post($outcome)
    {
        $this->_submitToUVBService($outcome);

        return $this->response;
    }

    /**
     * Send request to UVB API
     */
    private function _checkUVBService() : void
    {
        $payload = [
            'threshold' => $this->threshold
        ];

        $client = new Client();

        try {
            $this->response = $client->request('POST', $this->baseUrl . $this->hash, [
                'auth' => [$this->publicApiKey, $this->privateApiKey],
                'json' => $payload
            ]);

            $this->response = $this->response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            //
        }
    }

    /**
     * Submit payload to UVB Signals API endpoint
     *
     * @param $outcome
     */
    private function _submitToUVBService($outcome) : void
    {
        $payload = [
            'emailHash' => $this->hash,
            'outcome' => $outcome
        ];

        $client = new Client();
        try {
            $this->response = $client->request('POST', $this->baseUrl, [
                'auth' => [$this->publicApiKey, $this->privateApiKey],
                'json' => $payload
            ]);

            $this->response = $this->response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            //
        }
    }
}

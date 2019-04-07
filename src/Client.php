<?php


namespace Zarinpal;

use GuzzleHttp\Client as HttpClient;

class Client
{
    private $http;

    /**
     * Client constructor.
     *
     * @param bool|null $sandbox_mode
     * @param HttpClient|null $httpClient
     */
    public function __construct(
        bool $sandbox_mode = null,
        HttpClient $httpClient = null
    )
    {
        if ($sandbox_mode)
            $base_uri = 'https://sandbox.zarinpal.com/pg/rest/WebGate/';
        else
            $base_uri = 'https://www.zarinpal.com/pg/rest/WebGate/';

        if ($httpClient)
            $this->http = $httpClient;
        else
            $this->http = new HttpClient([ 'base_uri' => $base_uri ]);
    }

    /**
     * Payment Request
     *
     * @param array $data
     * @return mixed
     */
    public function paymentRequest (array $data)
    {
        $response =
            $this->http->post('PaymentRequest.json', [
                'json' => $data
            ]);

        return json_decode($response->getBody());
    }

    /**
     * Payment Verification
     *
     * @param array $data
     * @return mixed
     */
    public function paymentVerification (array $data)
    {
        $response =
            $this->http->post('PaymentVerification.json', [
                'json' => $data
            ]);

        return json_decode($response->getBody());
    }
}

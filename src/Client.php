<?php


namespace Zarinpal;

use GuzzleHttp\Client as HttpClient;

class Client
{
    private $http;

    /**
     * Client constructor.
     *
     * @param bool $sandbox_mode
     */
    public function __construct(bool $sandbox_mode = null)
    {
        if ($sandbox_mode)
            $base_uri = 'https://sandbox.zarinpal.com/pg/rest/WebGate/';
        else
            $base_uri = 'https://www.zarinpal.com/pg/rest/WebGate/';

        $this->http = new HttpClient([ 'base_uri' => $base_uri ]);
    }

    /**
     * Payment Request
     *
     * @param string $MerchantID
     * @param int $Amount
     * @param string $Description
     * @param string $CallbackURL
     * @param string|null $Email
     * @param string|null $Mobile
     *
     * @return mixed
     */
    public function paymentRequest (string $MerchantID, int $Amount, string $Description,
                                    string $CallbackURL, string $Email = null, string $Mobile = null)
    {
        $response =
            $this->http->post('PaymentRequest.json', [
                'json' => func_get_args()
            ]);

        return json_decode($response->getBody());
    }

    /**
     * Payment Verification
     *
     * @param string $MerchantID
     * @param string $Authority
     * @param int $Amount
     *
     * @return mixed
     */
    public function paymentVerification (string $MerchantID, string $Authority, int $Amount)
    {
        $response =
            $this->http->post('PaymentVerification.json', [
                'json' => func_get_args()
            ]);

        return json_decode($response->getBody());
    }
}

<?php


namespace Zarinpal;



use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Zarinpal\Exceptions\FailedTransactionException;
use Zarinpal\Exceptions\InvalidResponseException;
use Zarinpal\Exceptions\NoMerchantIDProvidedException;

class Zarinpal
{
    /**
     * MerchantID
     *
     * @var string
     */
    protected $merchant_id;

    /**
     * Sandbox mode flag
     *
     * @var boolean
     */
    protected $sandbox_mode;

    /**
     * @var Payment
     */

    private $payment;

    /**
     * create guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    private function client()
    {
        if ($this->sandbox_mode)
            $base_uri = 'https://sandbox.zarinpal.com/pg/rest/WebGate/';
        else
            $base_uri = 'https://www.zarinpal.com/pg/rest/WebGate/';

        return new Client([
            'base_uri' => $base_uri
        ]);
    }

    /**
     * return redirect response.
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        if ($this->sandbox_mode)
            return Redirect::away('https://sandbox.zarinpal.com/pg/StartPay/' . $this->payment->authority);

        return Redirect::away('https://www.zarinpal.com/pg/StartPay/' . $this->payment->authority);
    }

    /**
     * Request payment.
     * @param int $amount in Tuman
     * @param string $callback
     * @param array $payload
     *
     * @return Zarinpal|void
     *
     * @throws NoMerchantIDProvidedException
     */
    public function payment(int $amount, string $callback, array $payload = [])
    {

        if (!Config::has('services.zarinpal.merchant_id') && !$this->merchant_id)
            throw new NoMerchantIDProvidedException;

        if (Config::has('services.zarinpal.merchant_id'))
            $this->merchant_id = Config::get('services.zarinpal.merchant_id‬‬');

        //TODO validate given amount and callbackURL

        if (Arr::has($payload, 'description'))
            $payment = new Payment($amount, $payload['description']);
        else
            $payment = new Payment($amount);


        $response =
            $this->client()->post('PaymentRequest.json', [
                'json' => [
                    'MerchantID' => $this->merchant_id,
                    'Amount' => $payment->amount,
                    'Description' => $payment->description,
                    'CallbackURL' => $callback
                ]
            ]);

        if ($response->getBody()) {
            $result = json_decode($response->getBody());
            if ($result->Status == 100) {
                $payment->authority = $result->Authority;
                $this->payment = $payment;
                return $this;
            }
        }

    }

    /**
     * Payment verification.
     * @param Request $request
     * @param int $amount
     *
     * @return Payment
     *
     * @throws FailedTransactionException
     */

    public function verify(Request $request, int $amount)
    {
        if (!$request->has('Status') || !$request->has('Authority'))
            throw new InvalidResponseException('Invalid response from Zarinpal. Status and Authority parameters expected.');

        if ($request->input('Status') !== 'OK')
            throw new FailedTransactionException($code = -1);

        $payment = new Payment($amount);
        $payment->authority = $request->input('Authority');

        $response =
            $this->client()->post('PaymentVerification.json', [
                'json' => [
                    'MerchantID' => $this->merchant_id,
                    'Amount' => $payment->amount,
                    'Authority' => $payment->authority
                ]
            ]);

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody());
            $payment->status = $result->Status;
            $payment->RefID = $result->RefID;
        }

        return $payment;

    }

    /**
     * Dynamically set merchantId for each payment.
     *
     * @param string $id
     * @return Zarinpal
     */
    public function setMerchantID (string $id)
    {
        $this->merchant_id = $id;
        return $this;
    }

    /**
     * Enable Zarinpal's sandbox mode.
     *
     * @return Zarinpal
     */

    public function enableSandbox()
    {
        $this->sandbox_mode = true;
        return $this;
    }

}

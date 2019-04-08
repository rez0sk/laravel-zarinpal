<?php


namespace Zarinpal;


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
    private $sandbox_mode;

    /**
     * Client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * @var Payment
     */

    public $payment;


    /**
     * Zarinpal constructor.
     * @param string|null $merchant_id
     * @param bool|null $sandbox_mode
     * @param Client|null $client
     */
    public function __construct(
        string $merchant_id = null,
        bool $sandbox_mode = null,
        $client = null
    )
    {
        $this->merchant_id = $merchant_id;
        $this->sandbox_mode = $sandbox_mode;
        $client ? $this->client = $client : $this->client = new Client;
    }

    /**
     * Request payment.
     * @param int $amount in Tuman
     * @param string $callback
     * @param array $options
     *
     * @return Zarinpal|void
     *
     * @throws NoMerchantIDProvidedException
     * @throws Exceptions\InvalidDataException
     */
    public function pay(int $amount, string $callback, array $options = [])
    {
        if (!$this->merchant_id)
            throw new NoMerchantIDProvidedException;
        //TODO validate given amount and callbackURL

        if (Arr::has($options, 'description'))
            $payment = new Payment($amount, $options['description']);
        else
            $payment = new Payment($amount);

        $result = $this->client->request('PaymentRequest.json', [
            'MerchantID' => $this->merchant_id,
            'Amount' => $payment->amount,
            'Description' => $payment->description,
            'Callback' => $callback,
            'Email' => Arr::get($options, 'email'),
            'Phone' => Arr::get($options, 'phone')
        ]);

        if ($result->Status == 100) {
            $payment->authority = $result->Authority;
            $this->payment = $payment;
            return $this;
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
     * @throws NoMerchantIDProvidedException
     * @throws Exceptions\InvalidDataException
     */
    public function verify(Request $request, int $amount)
    {
        if (!$this->merchant_id)
            throw new NoMerchantIDProvidedException;

        if (!$request->has('Status') || !$request->has('Authority'))
            throw new InvalidResponseException('Invalid response from Zarinpal. Status and Authority parameters expected.');

        if ($request->input('Status') !== 'OK')
            throw new FailedTransactionException($code = -1);

        $payment = new Payment($amount);
        $payment->authority = $request->input('Authority');

        $result = $this->client->request('PaymentVerification.json', [
            'MerchantID' => $this->merchant_id,
            'Authority' => $payment->authority,
            'Amount' => $payment->amount
        ]);

        $payment->status = $result->Status;
        $payment->RefID = $result->RefID;


        return $payment;

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

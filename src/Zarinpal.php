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
    protected $sandbox_mode;

    /**
     * @var Payment
     */

    private $payment;

    /**
     * create guzzle client.
     *
     * @return Client
     */
    private function client()
    {
        return new Client($this->sandbox_mode);
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
     * @param array $options
     * @return Zarinpal|void
     *
     * @throws NoMerchantIDProvidedException
     */
    public function payment(int $amount, string $callback, array $options = [])
    {

        if (!Config::has('services.zarinpal.merchant_id') && !$this->merchant_id)
            throw new NoMerchantIDProvidedException;

        if (Config::has('services.zarinpal.merchant_id'))
            $this->merchant_id = Config::get('services.zarinpal.merchant_id‬‬');

        //TODO validate given amount and callbackURL

        if (Arr::has($options, 'description'))
            $payment = new Payment($amount, $options['description']);
        else
            $payment = new Payment($amount);

        $result = $this->client()->paymentRequest(
            $this->merchant_id,
            $this->payment->amount,
            $this->payment->description,
            $callback,
            Arr::get($options, 'email'),
            Arr::get($options, 'phone')
        );

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
     */

    public function verify(Request $request, int $amount)
    {
        if (!$request->has('Status') || !$request->has('Authority'))
            throw new InvalidResponseException('Invalid response from Zarinpal. Status and Authority parameters expected.');

        if ($request->input('Status') !== 'OK')
            throw new FailedTransactionException($code = -1);

        $payment = new Payment($amount);
        $payment->authority = $request->input('Authority');

        $result = $this->client()->paymentVerification(
            $this->merchant_id,
            $this->payment->authority,
            $this->payment->amount
        );

        $payment->status = $result->Status;
        $payment->RefID = $result->RefID;


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

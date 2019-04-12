# laravel-zarinpal

[![Build Status](https://travis-ci.com/rez0sk/laravel-zarinpal.svg?branch=master)](https://travis-ci.com/rez0sk/laravel-zarinpal)
[![codecov](https://codecov.io/gh/rez0sk/laravel-zarinpal/branch/master/graph/badge.svg)](https://codecov.io/gh/rez0sk/laravel-zarinpal)
[![StyleCI](https://github.styleci.io/repos/179698413/shield?branch=master)](https://github.styleci.io/repos/179698413)


Laravel package for Zarinpal payment gateway.

This package provides an interface (a laravel facade) that you can easily use and mock!


## Features

* Mockability
* Dynamic MerchantID
* Supporting zarinpal's sandbox


## Installation 

```
composer require rez0sk/laravel-zarinpal
```

## Configuration

In your `config/services.php` file add this config:

```php
'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'description' => 'Default description' // optional
]
```
then add `ZARINPAL_MERCHANT_ID` to your `.env` file.

#### Sandbox mode (Optional)
It's recomanded to enable sandbox mode in `local` environment. 


If you wan't to do so, add these lines to your `AppServiceProvider` class's `boot` function. 
```php
public function boot()
{
    if ($this->app->isLocal()) {
        Zarinpal::enableSandbox();
    }
}
```
## Usage
Simply issue a payment-request and redirect user to Zarinpal with one shot!
```php
use Zarinpal\Facades\Zarinpal;
...

public function someControllerFunction ()
{
    return Zarinpal::pay(2000, route('paymnet.verify', $order->id))->redirect();
}
```
Or retrieve Authority code and redirect manually:
```php
Zarinpal::pay(2000, 'http://callback.url/id')->payment->authority
```

There are some additional information you can provide for payment:
```php
Zarinpal::pay(2000, 'http://callback.url/id', [
        'description' => 'This is such a dummy order!',
        'email' => 'User's email address',
        'phone' => 'User's phone number'
]);
```

#### Dynamic MerchantID
You can dynamically set MerchantID before each payment:
```php
Zarinpal::setMerchantID('xxxxx-xxxx-xxx');
Zarinpal::pay(...)
```

### Payment Verification
```php
public function verifyOrderPayment (Order $order, Request $request)
{
    $result = Zarinpal::verify($request, $order->TotalPrice);
    
    $result->status //Status code. 100 means success :)
    $result->RefID //Payment's unique ReferenceID
    $result->amount // Payment's amount in Tuman. (Always use Toman with Zarinpal)
    $result->description //Payment's description.
}


```

### TODO 
- [x] CI setup.
- [x] Adding examples.
- [ ] Improve tests.
- [ ] Support wages.

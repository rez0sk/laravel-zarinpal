# laravel-zarinpal

[![Build Status](https://travis-ci.com/rez0sk/laravel-zarinpal.svg?branch=master)](https://travis-ci.com/rez0sk/laravel-zarinpal)

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


### TODO 
- [x] CI setup.
- [ ] Adding examples.
- [ ] Improve tests.
- [ ] Suppert wages.

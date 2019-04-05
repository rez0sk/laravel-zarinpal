<?php


namespace Zarinpal\Facades;

/**
 * @method static \Illuminate\Http\RedirectResponse payment(int $amount, string $callback, array $options = [])
 * @method static \Illuminate\Http\RedirectResponse setMerchantID(string $MerchantId)
 *
 * @see \Zarinpal\Zarinpal
 */

use Illuminate\Support\Facades\Facade;

class Zarinpal extends Facade
{
    protected static function getFacadeAccessor() { return 'zarinpal'; }

}

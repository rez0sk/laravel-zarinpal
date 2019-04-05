<?php


namespace Zarinpal\Facades;

/**
 * @method static \Illuminate\Http\RedirectResponse pay(int $amount, string $callback, array $options = [])
 * @method static void setMerchantID(string $MerchantId)
 * @method static \Zarinpal\Payment verify(\Illuminate\Http\Request $request, int $amount)
 *
 * @see \Zarinpal\Zarinpal
 */

use Illuminate\Support\Facades\Facade;

class Zarinpal extends Facade
{
    protected static function getFacadeAccessor() { return 'zarinpal'; }

}

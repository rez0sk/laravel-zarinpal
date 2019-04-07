<?php


namespace Zarinpal;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ZarinpalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('zarinpal', function() {
            return new Zarinpal(Config::get('services.zarinpal.merchant_id'));
        });
    }

}

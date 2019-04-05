<?php


namespace Zarinpal;


use Illuminate\Support\ServiceProvider;
use SoapClient;

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
            return new Zarinpal;
        });
    }

}

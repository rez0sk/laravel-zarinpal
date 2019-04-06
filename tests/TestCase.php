<?php


namespace Zarinpal\Tests;


use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return 'Zarinpal\ZarinpalServiceProvider';
    }

    protected function getPackageAliases($app)
    {
        return [
            'Zarinpal' => 'Zarinpal\Facades\Zarinpal'
        ];
    }

}

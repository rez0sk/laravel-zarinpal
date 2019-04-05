<?php


namespace Zarinpal\Tests;


use GuzzleHttp\Client;
use Aeris\GuzzleHttpMock\Expect;
use Illuminate\Support\Facades\Config;
use Zarinpal\Exceptions\NoMerchantIDProvidedException;
use Zarinpal\Zarinpal;

class ZarinpalTest extends TestCase
{
    /**
     * @var Zarinpal
     */
    private $zarinpal;

    /**
     * Mocking client.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $mock_client = new Client([
            'base_uri' => 'https://example.com',
            'handler' => $this->httpMock->getHandlerStackWithMiddleware()

        ]);

        $this->zarinpal = new Zarinpal();
        $this->zarinpal->client = $mock_client;
    }

    /**
     * @test if phpunit works correctly.
     *
     * @return void
     */
    public function self_check()
    {
        $this->assertTrue(true);
    }


    /**
     * @test if NoMerchantIDProvided thrown correctly.
     *
     * @return void
     * @throws NoMerchantIDProvidedException
     */
    public function payment_with_amount_and_callback()
    {
        $this->expectException(NoMerchantIDProvidedException::class);

        Config::shouldReceive('has')->twice();
        Config::shouldReceive('get')->once();

        $zarinpal = new Zarinpal();
        $zarinpal->payment('200', 'http://example.com');

    }

    /**
     * @test setting merchantID.
     *
     * @return void
     * @throws NoMerchantIDProvidedException
     */
    public function getting_merchant_from_config()
    {
        $this->httpMock
            ->shouldReceiveRequest()
            ->withMethod('POST')
            ->withUrl('https://example.com/PaymentRequest.json')
            ->withBodyParams(new Expect\Any())
            ->andRespondWithJson([
                'Status' => 100,
                'Authority' => '000212121'
            ], $statusCode = 200);

        $this->zarinpal->setMerchantID('xxxx-xxx-xxx');
        $result = $this->zarinpal->payment('200', 'http://example.com');

        $this->assertNull($this->httpMock->verify());
        //$this->assertInstanceOf(Request::class, $result);
    }
}

<?php


namespace Zarinpal\Tests;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use stdClass;
use Zarinpal\Client;
use Zarinpal\Exceptions\NoMerchantIDProvidedException;
use Zarinpal\Facades\Zarinpal;
use Mockery;
use Zarinpal\Payment;

class ZarinpalTest extends TestCase
{
    /**
     * Mocking client.
     */
    protected function setUp(): void
    {
        parent::setUp();
        //
    }

    /**
     * @test if phpunit works correctly.
     * @coversNothing
     *
     * @return void
     */
    public function self_check()
    {
        $this->assertTrue(true);
    }

    /**
     * @test if exceptions throws when there is no merchant id provided.
     * Given: no merchant provided
     * @covers \Zarinpal\Zarinpal::pay
     *
     * @return void
     *
     */
    public function test_merchant_id_exception()
    {
        $this->expectException(NoMerchantIDProvidedException::class);

        Zarinpal::pay(200, 'http://exmaple.com');
    }

    /**
     * @test if it accepts dynamic MerchantID
     * @covers \Zarinpal\Zarinpal::setMerchantID
     * @return void
     */
    public function providing_merchant_dynamically()
    {
        $mock_response = new stdClass();
        $mock_response->Status = 100;
        $mock_response->Authority = '0000001234';

        $this->mockClient('paymentRequest', $mock_response);

        Zarinpal::setMerchantID('xxxx-xxxx-xxxx');
        $result = Zarinpal::pay(200, 'http://exmaple.com');

        $this->assertIsObject($result);
        $this->assertEquals(200, $result->payment->amount);
    }

    /**
     * @test if it accepts MerchantID from config
     * Given: MerchantID stored in config/services.php
     * @covers \Zarinpal\Zarinpal::pay
     *
     * @return void
     */
    public function getting_merchantId_from_config()
    {
        Config::shouldReceive('has')->twice()->andReturnTrue();
        Config::shouldReceive('get')->times(3)->andReturn('xxxxx-xxxx-xxx');

        $mock_response = new stdClass();
        $mock_response->Status = 100;
        $mock_response->Authority = '0000001234';

        $this->mockClient('paymentRequest', $mock_response);

        $result = Zarinpal::pay(200, 'http://exmaple.com');

        $this->assertIsObject($result);
        $this->assertEquals(200, $result->payment->amount);
    }

    /**
     * @test if simple verification is possible
     * @covers \Zarinpal\Zarinpal::verify
     *
     * @return void
     */
    public function verifing_simple_transaction()
    {
        $response = new stdClass;
        $response->Status = 100;
        $response->RefID = 123456789;

        $this->mockClient('paymentVerification', $response);

        $request = new Request;
        $request->replace([
            'Status' => 'OK',
            'Authority' => '000001233'
        ]);

        Zarinpal::setMerchantID('xxxx-xxxx-xxxx');
        $result = Zarinpal::verify($request, 200);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals(200, $result->amount);
        $this->assertEquals($response->RefID, $result->RefID);
    }


    /**
     * Helper function for mocking Zarinpal\Client.
     *
     * @param string $endpoint
     * @param stdClass $response
     *
     * @return void
     */
    private function mockClient(string $endpoint, stdClass $response)
    {
        $mock_client = Mockery::mock(Client::class);
        $mock_client->shouldReceive($endpoint)
            ->once()
            ->andReturn($response);

        Zarinpal::shouldReceive('client')->andReturn($mock_client);
        Zarinpal::getFacadeRoot()->makePartial();
    }
}

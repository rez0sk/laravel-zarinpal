<?php


namespace Zarinpal\Tests;

use Zarinpal\Zarinpal;
use Illuminate\Http\Request;
use stdClass;
use Mockery;
use Zarinpal\Client;
use Zarinpal\Payment;
use Zarinpal\Exceptions\NoMerchantIDProvidedException;

class ZarinpalTest extends TestCase
{
    /**
     * Mocked client.
     * @var Client
     */
    private $client;

    /**
     * Mocking client.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(Client::class);
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
     * @throws NoMerchantIDProvidedException
     * @throws \Zarinpal\Exceptions\InvalidDataException
     */
    public function test_merchant_id_exception()
    {
        $this->expectException(NoMerchantIDProvidedException::class);
        $client = Mockery::spy(Client::class);
        $zarinpal = new Zarinpal(null , true, $client);
        $client->shouldNotHaveBeenCalled(['paymentRequest']);
        $zarinpal->pay(200, 'http://exmaple.com');
    }

    /**
     * @test if it accepts dynamic MerchantID
     * @covers \Zarinpal\Zarinpal::setMerchantID
     *
     * @return void
     *
     * @throws NoMerchantIDProvidedException
     * @throws \Zarinpal\Exceptions\InvalidDataException
     */
    public function providing_merchant_dynamically()
    {
        $mock_response = new stdClass();
        $mock_response->Status = 100;
        $mock_response->Authority = '0000001234';

        $client = $this->client
            ->shouldReceive('request')
            ->with('PaymentRequest.json', Mockery::hasValue('xxxx-xxxx-xxxx'))
            ->andReturns($mock_response)
            ->getMock();


        $zarinpal = new Zarinpal(null, 1, $client);

        $zarinpal->setMerchantID('xxxx-xxxx-xxxx');
        $result = $zarinpal->pay(200, 'http://exmaple.com');

        $this->assertIsObject($result);
        $this->assertEquals(200, $result->payment->amount);
    }

    /**
     * @test if it accepts MerchantID from config
     * Given: MerchantID stored in config/services.php
     * @covers \Zarinpal\Zarinpal::pay
     *
     * @return void
     * @throws NoMerchantIDProvidedException
     * @throws \Zarinpal\Exceptions\InvalidDataException
     */
    public function getting_merchantId_from_config()
    {
        $mock_response = new stdClass();
        $mock_response->Status = 100;
        $mock_response->Authority = '0000001234';

        $client = $this->client
            ->shouldReceive('request')
            ->with('PaymentRequest.json', Mockery::hasValue('xxxx-xxxx-xxxx'))
            ->andReturns($mock_response)
            ->getMock();

        $zarinpal = new Zarinpal('xxxx-xxxx-xxxx', 1, $client);

        $result = $zarinpal->pay(200, 'http://exmaple.com');

        $this->assertIsObject($result);
        $this->assertEquals(200, $result->payment->amount);
    }

    /**
     * @test if simple verification is possible
     * @covers \Zarinpal\Zarinpal::verify
     *
     * @return void
     * @throws NoMerchantIDProvidedException
     * @throws \Zarinpal\Exceptions\FailedTransactionException
     * @throws \Zarinpal\Exceptions\InvalidDataException
     */
    public function verifing_simple_transaction()
    {
        $response = new stdClass;
        $response->Status = 100;
        $response->RefID = 123456789;

        $client = $this->client
            ->shouldReceive('request')
            ->with('PaymentVerification.json', Mockery::subset([
                'MerchantID' => 'xxxx-xxxx-xxxx',
                'Amount' => 200,
                'Authority' => '000001233'
            ]))
            ->andReturns($response)
            ->getMock();

        $request = new Request;
        $request->replace([
            'Status' => 'OK',
            'Authority' => '000001233'
        ]);

        $zarinpal = new Zarinpal('xxxx-xxxx-xxxx', 1, $client);

        $result = $zarinpal->verify($request, 200);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals(200, $result->amount);
        $this->assertEquals($response->RefID, $result->RefID);
    }

    /**
     * @test if it passes sandbox flag to Client
     *
     * @return void
     */
    public function sandbox_flag_passes()
    {
        $zarinpal = new Zarinpal('xxx-xxx-xxx', true);
        $this->assertTrue($zarinpal->getClient()->isSandbox());

    }

    /**
     * @test if it's passable to enable sandbox mode dynamically
     *
     * @return void
     */
    public function dynamic_sandbox()
    {
        $zarinpal = new Zarinpal('xxx-xxx-xxx', false);
        $zarinpal->enableSandbox();
        $this->assertTrue($zarinpal->getClient()->isSandbox());
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

<?php


namespace Zarinpal\Tests;


use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use stdClass;
use Zarinpal\Client;

class ClientTest extends TestCase
{

    private $guzzle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guzzle = new Guzzle([ 'base_uri' => 'http://example.com' ]);

    }

    /**
     * @test if it returns response on ok status.
     * @covers \Zarinpal\Client::paymentRequest
     *
     * Given: Client receives status 200
     *
     * @return void
     */
    public function best_case_for_paymentRequest()
    {
        $mock = new MockHandler([
            new Response(200)
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Guzzle(['base_uri' => 'http://example.com', 'handler' => $handler]);

        $client = new Client(true, $guzzle);
        $result = $client->paymentRequest(array());
        $this->assertNull($result);
    }

}

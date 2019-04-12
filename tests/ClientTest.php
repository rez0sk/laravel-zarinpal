<?php


namespace Zarinpal\Tests;


use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Zarinpal\Client;
use Zarinpal\Exceptions\InvalidDataException;

class ClientTest extends TestCase
{

    /**
     * @test if it returns response on ok status.
     * @covers \Zarinpal\Client::request
     *
     * Given: Client receives status 200
     * Expect: No exceptions thrown
     *
     * @return void
     * @throws InvalidDataException
     */
    public function best_case_for_paymentRequest()
    {
        $mock = new MockHandler([
            new Response(200)
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Guzzle(['base_uri' => 'http://example.com', 'handler' => $handler]);

        $client = new Client(true, $guzzle);
        $result = $client->request('PaymentRequest.json', array());
        $this->assertNull($result);
    }

    /**
     * @test if it throws exception on 404 response.
     * @covers \Zarinpal\Client::request
     *
     * @return void
     * @throws InvalidDataException
     */
    public function not_found_error_on_paymentRequest()
    {
        $this->expectException(InvalidDataException::class);
        $stub_response = file_get_contents(__DIR__.'/responses/validation_errors.json');
        $mock = new MockHandler([
            new Response(404, [], $stub_response)
        ]);
        $handler = HandlerStack::create($mock);
        $guzzle = new Guzzle(['base_uri' => 'http://example.com', 'handler' => $handler]);

        $client = new Client(true, $guzzle);
        $result = $client->request('PaymentRequest.json', array());
        $this->assertNull($result);

    }

}

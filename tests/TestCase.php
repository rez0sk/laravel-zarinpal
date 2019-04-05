<?php


namespace Zarinpal\Tests;


use Aeris\GuzzleHttpMock\Mock as GuzzleMock;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    private $mockHttp;


    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->httpMock = new GuzzleMock();
    }


}

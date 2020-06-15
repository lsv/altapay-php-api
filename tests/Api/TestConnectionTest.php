<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Test\TestConnection;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class TestConnectionTest extends AbstractApiTest
{
    public function test_connection_on()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200)
        ]));

        $api = (new TestConnection())
            ->setClient($client)
        ;

        $this->assertTrue($api->call());
        $this->assertEquals($this->getExceptedUri('testConnection'), $api->getRawRequest()->getUri()->getPath());
    }

    public function test_connection_off()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(400)
        ]));

        $api = (new TestConnection())
            ->setClient($client)
        ;

        $this->assertFalse($api->call());
        $this->assertEquals($this->getExceptedUri('testConnection'), $api->getRawRequest()->getUri()->getPath());
    }

    public function test_connection_302()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(302)
        ]));

        $api = (new TestConnection())
            ->setClient($client)
        ;

        $this->assertTrue($api->call());
        $this->assertEquals($this->getExceptedUri('testConnection'), $api->getRawRequest()->getUri()->getPath());
    }
}

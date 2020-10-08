<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Test\TestConnection;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Altapay\Exceptions\ClientException;

class TestConnectionTest extends AbstractApiTest
{
    public function test_connection_on(): void
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200)
        ]));

        $api = (new TestConnection())
            ->setClient($client);

        $this->assertSame('ok', $api->call());
        $this->assertEquals($this->getExceptedUri('testConnection'), $api->getRawRequest()->getUri()->getPath());
    }

    public function test_connection_off(): void
    {
        $this->expectException(ClientException::class);

        $client = $this->getClient($mock = new MockHandler([
            new Response(400)
        ]));

        $api = (new TestConnection())
            ->setClient($client);

        try {
            $api->call();
        } catch (ClientException $e) {
            $this->assertEquals($this->getExceptedUri('testConnection'), $api->getRawRequest()->getUri()->getPath());
            throw $e;
        }
    }

    public function test_connection_302(): void
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(302)
        ]));

        $api = (new TestConnection())
            ->setClient($client);

        $this->assertSame('ok', $api->call());
        $this->assertEquals($this->getExceptedUri('testConnection'), $api->getRawRequest()->getUri()->getPath());
    }
}

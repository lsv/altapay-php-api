<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Test\TestAuthentication;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Altapay\Exceptions\ClientException;

class TestAuthenticationTest extends AbstractApiTest
{
    public function test_auth_ok(): void
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200)
        ]));

        $api = (new TestAuthentication($this->getAuth()))
            ->setClient($client)
        ;

        $this->assertSame('ok', $api->call());
        $this->assertEquals($this->getExceptedUri('login'), $api->getRawRequest()->getUri()->getPath());
    }

    public function test_auth_fail(): void
    {
        $this->expectException(ClientException::class);

        $client = $this->getClient($mock = new MockHandler([
            new Response(400)
        ]));

        $api = (new TestAuthentication($this->getAuth()))
            ->setClient($client)
        ;

        try {
            $api->call();
        } catch (ClientException $e) {
            $this->assertEquals($this->getExceptedUri('login'), $api->getRawRequest()->getUri()->getPath());
            throw $e;
        }
    }
}

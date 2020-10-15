<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Api\Test\TestConnection;

class TestConnectionTest extends AbstractFunctionalTest
{
    public function test_connection(): void
    {
        $response = (new TestConnection())
            ->call();

        $this->assertSame('ok', $response);
    }

    public function test_connection_fails(): void
    {
        $response = (new TestConnection('http//idonotexists.mecom'))
            ->call();

        $this->assertSame('ok', $response);
    }
}

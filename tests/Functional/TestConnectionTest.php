<?php

namespace Valitor\ApiTest\Functional;

use Valitor\Api\Test\TestConnection;

class TestConnectionTest extends AbstractFunctionalTest
{

    public function test_connection()
    {
        $response = (new TestConnection())
            ->call()
        ;

        $this->assertTrue($response);
    }

    public function test_connection_fails()
    {
        $response = (new TestConnection('http//idonotexists.mecom'))
            ->call()
        ;

        $this->assertFalse($response);
    }

}

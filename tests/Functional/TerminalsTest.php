<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Api\Others\Terminals;
use Altapay\Response\TerminalsResponse;

class TerminalsTest extends AbstractFunctionalTest
{

    public function test_terminals()
    {
        $response = (new Terminals($this->getAuth()))->call();
        $this->assertInstanceOf(TerminalsResponse::class, $response);
        $this->assertCount($_ENV['NUMBER_OF_TERMINALS'], $response->Terminals);
    }

}

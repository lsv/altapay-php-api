<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Api\Others\Terminals;
use Altapay\Response\TerminalsResponse;

class TerminalsTest extends AbstractFunctionalTest
{

    public function test_terminals()
    {
        /** @var TerminalsResponse $response */
        $response = (new Terminals($this->getAuth()))->call();
        $this->assertCount($_ENV['NUMBER_OF_TERMINALS'], $response->Terminals);
    }

}

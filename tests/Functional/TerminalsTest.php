<?php

namespace Valitor\ApiTest\Functional;

use Valitor\Api\Others\Terminals;
use Valitor\Response\TerminalsResponse;

class TerminalsTest extends AbstractFunctionalTest
{

    public function test_terminals()
    {
        /** @var TerminalsResponse $response */
        $response = (new Terminals($this->getAuth()))->call();
        $this->assertCount($_ENV['NUMBER_OF_TERMINALS'], $response->Terminals);
    }

}

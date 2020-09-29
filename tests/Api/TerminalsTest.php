<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\Terminals;
use Altapay\Response\Embeds\Terminal;
use Altapay\Response\TerminalsResponse as TerminalsDocument;
use Altapay\Response\TerminalsResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class TerminalsTest extends AbstractApiTest
{

    /**
     * @return Terminals
     */
    protected function getTerminals()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/terminals.xml'))
        ]));

        return (new Terminals($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_url()
    {
        $api = $this->getTerminals();
        $api->call();
        $this->assertEquals($this->getExceptedUri('getTerminals'), $api->getRawRequest()->getUri()->getPath());
    }

    public function test_header()
    {
        $api = $this->getTerminals();
        /** @var TerminalsDocument $response */
        $response = $api->call();
        $this->assertInstanceOf(\DateTime::class, $response->Header->Date);
        $this->assertEquals('07-01-2016', $response->Header->Date->format('d-m-Y'));
        $this->assertEquals('API/getTerminals', $response->Header->Path);
        $this->assertEquals('0', $response->Header->ErrorCode);
        $this->assertEquals('', $response->Header->ErrorMessage);
    }

    public function test_response()
    {
        $api = $this->getTerminals();
        /** @var TerminalsDocument $response */
        $response = $api->call();

        $this->assertCount(2, $response->Terminals);
        $this->assertEquals('Success', $response->Result);
    }

    /**
     * @depends test_response
     */
    public function test_response_terminal()
    {
        $api = $this->getTerminals();
        /** @var Terminal $terminal */
        /** @var TerminalsResponse $response */
        $response = $api->call();
        $this->assertCount(2, $response->Terminals);

        $terminal = $response->Terminals[0];
        $this->assertEquals('AltaPay Multi-Nature Terminal', $terminal->Title);
        $this->assertEquals('DK', $terminal->Country);
        $this->assertCount(4, $terminal->Natures);
    }

    public function test_attr_fail()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'The attribute "iddonotexists" on element "Title" does not have a setter or a property in class "Altapay\Response\Embeds\Terminal"'
        );

        (new Terminals($this->getAuth()))
            ->setClient($this->getClient($mock = new MockHandler([
                new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/terminals-fails.xml'))
            ])))
            ->call()
        ;
    }
}

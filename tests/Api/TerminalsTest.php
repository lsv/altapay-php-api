<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\Terminals;
use Altapay\Response\Embeds\Terminal;
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
        $client = $this->getXmlClient(__DIR__ . '/Results/terminals.xml');

        return (new Terminals($this->getAuth()))
            ->setClient($client);
    }

    public function test_url(): void
    {
        $api = $this->getTerminals();
        $api->call();
        $this->assertSame($this->getExceptedUri('getTerminals'), $api->getRawRequest()->getUri()->getPath());
    }

    public function test_header(): void
    {
        $api      = $this->getTerminals();
        $response = $api->call();
        $this->assertInstanceOf(TerminalsResponse::class, $response);
        $this->assertInstanceOf(\DateTime::class, $response->Header->Date);
        $this->assertSame('07-01-2016', $response->Header->Date->format('d-m-Y'));
        $this->assertSame('API/getTerminals', $response->Header->Path);
        $this->assertSame('0', $response->Header->ErrorCode);
        $this->assertSame('', $response->Header->ErrorMessage);
    }

    public function test_response(): void
    {
        $api      = $this->getTerminals();
        $response = $api->call();
        $this->assertInstanceOf(TerminalsResponse::class, $response);

        $this->assertCount(2, $response->Terminals);
        $this->assertSame('Success', $response->Result);
    }

    /**
     * @depends test_response
     */
    public function test_response_terminal(): void
    {
        $api      = $this->getTerminals();
        $response = $api->call();
        $this->assertInstanceOf(TerminalsResponse::class, $response);
        $this->assertCount(2, $response->Terminals);

        $terminal = $response->Terminals[0];
        $this->assertInstanceOf(Terminal::class, $terminal);
        $this->assertSame('AltaPay Multi-Nature Terminal', $terminal->Title);
        $this->assertSame('DK', $terminal->Country);
        $this->assertCount(4, $terminal->Natures);
    }

    public function test_attr_fail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The attribute "iddonotexists" on element "Title" does not have a setter or a property in class "Altapay\Response\Embeds\Terminal"'
        );

        $client = $this->getXmlClient(__DIR__ . '/Results/terminals-fails.xml');

        (new Terminals($this->getAuth()))
            ->setClient($client)
            ->call();
    }
}

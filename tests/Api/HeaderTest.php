<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\FundingList;
use Altapay\Exceptions\ResponseHeaderException;
use Altapay\Response\Embeds\Header;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class HeaderTest extends AbstractApiTest
{

    /**
     * @return FundingList
     */
    protected function getapi()
    {
        $client = $this->getClient(new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/header_error.xml'))
        ]));

        return (new FundingList($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_get_header_error()
    {
        $this->setExpectedException(ResponseHeaderException::class);
        $api = $this->getapi();
        $api->call();
    }

    public function test_get_header_error_data()
    {
        try {
            $api = $this->getapi();
            $api->call();
        } catch (ResponseHeaderException $e) {
            $this->assertInstanceOf(Header::class, $e->getHeader());
            $this->assertEquals('200', $e->getHeader()->ErrorCode);
            $this->assertEquals('This request has error', $e->getHeader()->ErrorMessage);
        }
    }
}

<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\CustomReport;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class CustomReportTest extends AbstractApiTest
{

    /**
     * @return CustomReport
     */
    protected function getCustomReport()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/customreport.txt'))
        ]));

        return (new CustomReport($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_custom_download_with_id()
    {
        $api = $this->getCustomReport();
        $api->setCustomReportId('0c55e643-49c2-492c-ab61-1014426dce5d');
        $api->call();

        $this->assertEquals($this->getExceptedUri('getCustomReport'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertEquals('0c55e643-49c2-492c-ab61-1014426dce5d', $parts['id']);
    }

    public function test_funding_download()
    {
        $api = $this->getCustomReport();
        $api->setCustomReportId('0c55e643-49c2-492c-ab61-1014426dce5d');
        $response = $api->call();
        $this->assertStringStartsWith('"Order ID";', $response);

        $csv = $api->__toArray(true);
        $this->assertCount(2, $csv);

        $csv = $api->__toArray(false);
        $this->assertCount(1, $csv);
    }
}

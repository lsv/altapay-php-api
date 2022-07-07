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
        $client = $this->getXmlClient(__DIR__ . '/Results/customreport.txt');

        return (new CustomReport($this->getAuth()))
            ->setClient($client);
    }

    public function test_custom_download_with_id(): void
    {
        $api = $this->getCustomReport();
        $api->setCustomReportId('0c55e643-49c2-492c-ab61-1014426dce5d');
        $api->call();

        $this->assertSame($this->getExceptedUri('getCustomReport'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertSame('0c55e643-49c2-492c-ab61-1014426dce5d', $parts['id']);
    }

    public function test_funding_download(): void
    {
        $api = $this->getCustomReport();
        $api->setCustomReportId('0c55e643-49c2-492c-ab61-1014426dce5d');
        $response = $api->call();
        $this->assertIsString($response);
        $this->assertStringStartsWith('"Order ID";', $response);

        $csv = $api->__toArray(true);
        $this->assertCount(2, $csv);

        $csv = $api->__toArray(false);
        $this->assertCount(1, $csv);
    }
}

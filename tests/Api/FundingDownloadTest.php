<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\FundingDownload;
use Altapay\Response\Embeds\Funding;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class FundingDownloadTest extends AbstractApiTest
{

    /**
     * @return FundingDownload
     */
    protected function getFundingDownload()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/fundingdownload.txt'))
        ]));

        return (new FundingDownload($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_funding_download_with_link()
    {
        $api = $this->getFundingDownload();
        $api->setFundingDownloadLink('https://myshop.altapaysecure.com/merchant/API/fundingDownload?id=32');
        $api->call();

        $this->assertEquals('myshop.altapaysecure.com', $api->getRawRequest()->getUri()->getHost());
        $this->assertEquals('/merchant/API/fundingDownload', $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertEquals(32, $parts['id']);
    }

    public function test_funding_download_with_object()
    {
        $funding = new Funding();
        $funding->DownloadLink = 'https://thisismyshop.altapaysecure.com/merchant/API/fundingDownload?id=99';

        $api = $this->getFundingDownload();
        $api->setFunding($funding);
        $api->call();

        $this->assertEquals('thisismyshop.altapaysecure.com', $api->getRawRequest()->getUri()->getHost());
        $this->assertEquals('/merchant/API/fundingDownload', $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertEquals(99, $parts['id']);
    }

    public function test_funding_download()
    {
        $api = $this->getFundingDownload();
        $api->setFundingDownloadLink('https://myshop.altapaysecure.com/merchant/API/fundingDownload?id=32');
        $response = $api->call();
        $this->assertStringStartsWith('Date;', $response);

        $csv = $api->__toArray(true);
        $this->assertCount(2, $csv);

        $csv = $api->__toArray(false);
        $this->assertCount(1, $csv);
    }
}

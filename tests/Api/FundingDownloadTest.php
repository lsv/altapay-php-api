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
        $client = $this->getXmlClient(__DIR__ . '/Results/fundingdownload.txt');

        return (new FundingDownload($this->getAuth()))
            ->setClient($client);
    }

    public function test_funding_download_with_link(): void
    {
        $api = $this->getFundingDownload();
        $api->setFundingDownloadLink('https://myshop.altapaysecure.com/merchant/API/fundingDownload?id=32');
        $api->call();

        $this->assertSame('myshop.altapaysecure.com', $api->getRawRequest()->getUri()->getHost());
        $this->assertSame('/merchant/API/fundingDownload', $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertSame('32', $parts['id']);
    }

    public function test_funding_download_with_object(): void
    {
        $funding               = new Funding();
        $funding->DownloadLink = 'https://thisismyshop.altapaysecure.com/merchant/API/fundingDownload?id=99';

        $api = $this->getFundingDownload();
        $api->setFunding($funding);
        $api->call();

        $this->assertSame('thisismyshop.altapaysecure.com', $api->getRawRequest()->getUri()->getHost());
        $this->assertSame('/merchant/API/fundingDownload', $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertSame('99', $parts['id']);
    }

    public function test_funding_download(): void
    {
        $api = $this->getFundingDownload();
        $api->setFundingDownloadLink('https://myshop.altapaysecure.com/merchant/API/fundingDownload?id=32');
        $response = $api->call();
        $this->assertIsString($response);
        $this->assertStringStartsWith('Date;', $response);

        $csv = $api->__toArray(true);
        $this->assertCount(2, $csv);

        $csv = $api->__toArray(false);
        $this->assertCount(1, $csv);
    }
}

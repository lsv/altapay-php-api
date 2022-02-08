<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\FundingList;
use Altapay\Response\Embeds\Funding;
use Altapay\Response\Embeds\Shop;
use Altapay\Response\FundingsResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class FundingListTest extends AbstractApiTest
{
    /**
     * @return FundingList
     */
    protected function getMultipleFundingsList()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/multiplefundinglist.xml');

        return (new FundingList($this->getAuth()))
            ->setClient($client);
    }

    /**
     * @return FundingList
     */
    protected function getSingleFundingsList()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/singlefundinglist.xml');

        return (new FundingList($this->getAuth()))
            ->setClient($client);
    }

    public function test_fundlinglist_routing(): void
    {
        $api = $this->getMultipleFundingsList();
        $api->call();

        $this->assertSame($this->getExceptedUri('fundingList/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertSame('0', $parts['page']);

        $api = $this->getMultipleFundingsList();
        $api->setPage(9);
        $api->call();

        $this->assertSame($this->getExceptedUri('fundingList/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertSame('9', $parts['page']);
    }

    public function test_fundlinglist_single(): void
    {
        $api      = $this->getSingleFundingsList();
        $response = $api->call();
        $this->assertInstanceOf(FundingsResponse::class, $response);
        $this->assertCount(1, $response->Fundings, 'num fundings');
    }

    public function test_fundlinglist_multiple(): void
    {
        $api      = $this->getMultipleFundingsList();
        $response = $api->call();
        $this->assertInstanceOf(FundingsResponse::class, $response);
        $this->assertCount(2, $response->Fundings, 'num fundings');
    }

    /**
     * @depends test_fundlinglist_multiple
     */
    public function test_funding_object(): void
    {
        $api      = $this->getMultipleFundingsList();
        $response = $api->call();
        $this->assertInstanceOf(FundingsResponse::class, $response);
        $funding = $response->Fundings[0];
        $this->assertInstanceOf(Funding::class, $funding);

        $this->assertSame('CreatedByTest', $funding->Filename);
        $this->assertSame('1234567890123456', $funding->ContractIdentifier);
        $this->assertCount(2, $funding->Shops);
        $this->assertSame('TestAcquirer', $funding->Acquirer);
        $this->assertInstanceOf(\DateTime::class, $funding->FundingDate);
        $this->assertSame('26-09-2010', $funding->FundingDate->format('d-m-Y'));
        $this->assertSame('50.00 EUR', $funding->Amount);
        $this->assertInstanceOf(\DateTime::class, $funding->CreatedDate);
        $this->assertSame('27-09-2010', $funding->CreatedDate->format('d-m-Y'));
        $this->assertSame('http://localhost/merchant.php/API/fundingDownload?id=1', $funding->DownloadLink);
    }
}

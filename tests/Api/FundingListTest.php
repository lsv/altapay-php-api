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
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/multiplefundinglist.xml'))
        ]));

        return (new FundingList($this->getAuth()))
            ->setClient($client)
        ;
    }

    /**
     * @return FundingList
     */
    protected function getSingleFundingsList()
    {
        $client = $this->getClient(new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/singlefundinglist.xml'))
        ]));

        return (new FundingList($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_fundlinglist_routing()
    {
        $api = $this->getMultipleFundingsList();
        $api->call();

        $this->assertEquals($this->getExceptedUri('fundingList/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertEquals(0, $parts['page']);

        $api = $this->getMultipleFundingsList();
        $api->setPage(9);
        $api->call();

        $this->assertEquals($this->getExceptedUri('fundingList/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);
        $this->assertEquals(9, $parts['page']);
    }

    public function test_fundlinglist_single()
    {
        $api = $this->getSingleFundingsList();
        /** @var FundingsResponse $response */
        $response = $api->call();
        $this->assertCount(1, $response->Fundings, 'num fundings');
    }

    public function test_fundlinglist_multiple()
    {
        $api = $this->getMultipleFundingsList();
        /** @var FundingsResponse $response */
        $response = $api->call();
        $this->assertCount(2, $response->Fundings, 'num fundings');
    }

    /**
     * @depends test_fundlinglist_multiple
     */
    public function test_funding_object()
    {
        $api = $this->getMultipleFundingsList();
        /** @var Funding $response */
        $response = $api->call()->Fundings[0];

        $this->assertEquals('CreatedByTest', $response->Filename);
        $this->assertEquals('1234567890123456', $response->ContractIdentifier);
        $this->assertCount(2, $response->Shops);
        $this->assertEquals('TestAcquirer', $response->Acquirer);
        $this->assertInstanceOf(\DateTime::class, $response->FundingDate);
        $this->assertEquals('26-09-2010', $response->FundingDate->format('d-m-Y'));
        $this->assertEquals('50.00 EUR', $response->Amount);
        $this->assertInstanceOf(\DateTime::class, $response->CreatedDate);
        $this->assertEquals('27-09-2010', $response->CreatedDate->format('d-m-Y'));
        $this->assertEquals('http://localhost/merchant.php/API/fundingDownload?id=1', $response->DownloadLink);
    }
}

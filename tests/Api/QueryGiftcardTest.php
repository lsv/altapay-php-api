<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\QueryGiftcard;
use Altapay\Request\Giftcard;
use Altapay\Response\GiftcardResponse as GiftcardResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class QueryGiftcardTest extends AbstractApiTest
{

    /**
     * @return QueryGiftcard
     */
    protected function getapi()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/querygiftcard.xml'))
        ]));

        return (new QueryGiftcard($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_route()
    {
        $card = new Giftcard('account', 'provider', '1234-1234');
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setGiftcard($card);
        $api->call();

        $this->assertEquals($this->getExceptedUri('queryGiftCard/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertEquals('my terminal', $parts['terminal']);
        $this->assertEquals('account', $parts['giftcard']['account_identifier']);
        $this->assertEquals('provider', $parts['giftcard']['provider']);
        $this->assertEquals('1234-1234', $parts['giftcard']['token']);
    }

    public function test_response()
    {
        $card = new Giftcard('account', 'provider', '1234-1234');
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setGiftcard($card);
        /** @var GiftcardResponse $response */
        $response = $api->call();

        $this->assertEquals('Success', $response->Result);
        $this->assertCount(2, $response->Accounts);
        $account = $response->Accounts[0];

        $this->assertEquals('EUR', $account->Currency);
        $this->assertEquals('50.00', $account->Balance);
    }
}

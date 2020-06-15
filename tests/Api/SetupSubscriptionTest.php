<?php

namespace Altapay\ApiTest\Api;

use Altapay\Exceptions\ResponseMessageException;
use Altapay\Response\SetupSubscriptionResponse as SetupSubscriptionResponse;
use Altapay\Api\Subscription\SetupSubscription;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class SetupSubscriptionTest extends AbstractApiTest
{

    /**
     * @return SetupSubscription
     */
    protected function getapi()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/reservationoffixedamount.xml'))
        ]));

        return (new SetupSubscription($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_charge_subscription_error()
    {
        $this->setExpectedException(
            ResponseMessageException::class,
            'TestAcquirer[pan=1466 or amount=14660]'
        );

        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/setupsubscription_fail.xml'))
        ]));

        $api = (new SetupSubscription($this->getAuth()))
            ->setClient($client)
        ;
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
    }

    public function test_url()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('setupSubscription/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('my terminal', $parts['terminal']);
        $this->assertEquals('order id', $parts['shop_orderid']);
        $this->assertEquals(200.50, $parts['amount']);
        $this->assertEquals(957, $parts['currency']);
        $this->assertEquals(155.23, $parts['surcharge']);
    }

    public function test_response()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        /** @var SetupSubscriptionResponse $response */
        $response = $api->call();

        $this->assertInstanceOf(SetupSubscriptionResponse::class, $response);
        $this->assertEquals('Success', $response->Result);
        $this->assertCount(1, $response->Transactions);
    }
}

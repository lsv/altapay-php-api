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
        $client = $this->getXmlClient(__DIR__ . '/Results/reservationoffixedamount.xml');

        return (new SetupSubscription($this->getAuth()))
            ->setClient($client);
    }

    public function test_charge_subscription_error(): void
    {
        $this->expectException(ResponseMessageException::class);
        $this->expectExceptionMessage(
            'TestAcquirer[pan=1466 or amount=14660]'
        );

        $client = $this->getXmlClient(__DIR__ . '/Results/setupsubscription_fail.xml');

        $api = (new SetupSubscription($this->getAuth()))
            ->setClient($client);
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
    }

    public function test_url(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('setupSubscription/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('my terminal', $parts['terminal']);
        $this->assertSame('order id', $parts['shop_orderid']);
        $this->assertSame('200.5', $parts['amount']);
        $this->assertSame('957', $parts['currency']);
        $this->assertSame('155.23', $parts['surcharge']);
    }

    public function test_response(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $response = $api->call();

        $this->assertInstanceOf(SetupSubscriptionResponse::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertCount(1, $response->Transactions);
    }
}

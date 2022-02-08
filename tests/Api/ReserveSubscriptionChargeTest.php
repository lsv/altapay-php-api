<?php

namespace Altapay\ApiTest\Api;

use Altapay\Response\Embeds\Transaction;
use Altapay\Response\ReserveSubscriptionResponse;
use Altapay\Exceptions\ClientException;
use Altapay\Api\Subscription\ReserveSubscriptionCharge;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ReserveSubscriptionChargeTest extends AbstractApiTest
{
    /**
     * @return ReserveSubscriptionCharge
     */
    protected function getReserveSubscriptionCharge()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/reservesubscription.xml');

        return (new ReserveSubscriptionCharge($this->getAuth()))
            ->setClient($client);
    }

    public function test_reserve_subscription_charge(): void
    {
        $api = $this->getReserveSubscriptionCharge();
        $api->setTransaction('123');
        $this->assertInstanceOf(ReserveSubscriptionResponse::class, $api->call());
    }

    /**
     * @depends test_reserve_subscription_charge
     */
    public function test_reserve_subscription_charge_data(): void
    {
        $api = $this->getReserveSubscriptionCharge();
        $api->setTransaction('123');
        $response = $api->call();
        $this->assertInstanceOf(ReserveSubscriptionResponse::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertCount(2, $response->Transactions);
    }

    public function test_reserve_subscription_charge_querypath(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getReserveSubscriptionCharge();
        $api->setTransaction($transaction);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('reserveSubscriptionCharge/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('456', $parts['transaction_id']);

        $api = $this->getReserveSubscriptionCharge();
        $api->setTransaction('helloworld');
        $api->setAmount(200.5);
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('helloworld', $parts['transaction_id']);
        $this->assertSame('200.5', $parts['amount']);

        $api = $this->getReserveSubscriptionCharge();
        $api->setTransaction('my trans id has spaces');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('my trans id has spaces', $parts['transaction_id']);
    }

    public function test_reserve_subscription_charge_querypath_handleexception(): void
    {
        $this->expectException(ClientException::class);

        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml'])
        ]));

        $api = (new ReserveSubscriptionCharge($this->getAuth()))
            ->setClient($client)
            ->setTransaction('123');
        $api->call();
    }
}

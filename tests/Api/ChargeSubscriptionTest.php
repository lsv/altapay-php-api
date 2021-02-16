<?php

namespace Altapay\ApiTest\Api;

use Altapay\Response\ChargeSubscriptionResponse as ChargeSubscriptionDocument;
use Altapay\Exceptions\ClientException;
use Altapay\Response\Embeds\Transaction;
use Altapay\Api\Subscription\ChargeSubscription;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ChargeSubscriptionTest extends AbstractApiTest
{

    /**
     * @return ChargeSubscription
     */
    protected function getChargeSubscription()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/setupsubscription.xml');

        return (new ChargeSubscription($this->getAuth()))
            ->setClient($client);
    }

    public function test_charge_subscription(): void
    {
        $api = $this->getChargeSubscription();
        $api->setTransaction('123');
        $this->assertInstanceOf(ChargeSubscriptionDocument::class, $api->call());
    }

    /**
     * @depends test_charge_subscription
     */
    public function test_charge_subscription_data(): void
    {
        $api = $this->getChargeSubscription();
        $api->setTransaction('123');
        $response = $api->call();
        $this->assertInstanceOf(ChargeSubscriptionDocument::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertCount(2, $response->Transactions);
    }

    public function test_charge_subscription_querypath(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getChargeSubscription();
        $api->setTransaction($transaction);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('chargeSubscription/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('456', $parts['transaction_id']);

        $api = $this->getChargeSubscription();
        $api->setTransaction('helloworld');
        $api->setAmount(200.5);
        $api->setReconciliationIdentifier('my identifier');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('helloworld', $parts['transaction_id']);
        $this->assertSame('200.5', $parts['amount']);
        $this->assertSame('my identifier', $parts['reconciliation_identifier']);

        $api = $this->getChargeSubscription();
        $api->setTransaction('my trans id has spaces');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('my trans id has spaces', $parts['transaction_id']);
    }

    public function test_charge_subscription_transaction_handleexception(): void
    {
        $this->expectException(ClientException::class);

        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml'])
        ]));

        $api = (new ChargeSubscription($this->getAuth()))
            ->setClient($client)
            ->setTransaction('123');
        $api->call();
    }
}

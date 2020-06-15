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
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/setupsubscription.xml'))
        ]));

        return (new ChargeSubscription($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_charge_subscription()
    {
        $api = $this->getChargeSubscription();
        $api->setTransaction(123);
        $this->assertInstanceOf(ChargeSubscriptionDocument::class, $api->call());
    }

    /**
     * @depends test_charge_subscription
     */
    public function test_charge_subscription_data()
    {
        $api = $this->getChargeSubscription();
        $api->setTransaction(123);
        /** @var ChargeSubscriptionDocument $response */
        $response = $api->call();
        $this->assertEquals('Success', $response->Result);
        $this->assertCount(2, $response->Transactions);
    }

    public function test_charge_subscription_querypath()
    {
        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $api = $this->getChargeSubscription();
        $api->setTransaction($transaction);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('chargeSubscription/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals(456, $parts['transaction_id']);

        $api = $this->getChargeSubscription();
        $api->setTransaction('helloworld');
        $api->setAmount(200.5);
        $api->setReconciliationIdentifier('my identifier');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('helloworld', $parts['transaction_id']);
        $this->assertEquals(200.5, $parts['amount']);
        $this->assertEquals('my identifier', $parts['reconciliation_identifier']);

        $api = $this->getChargeSubscription();
        $api->setTransaction('my trans id has spaces');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('my trans id has spaces', $parts['transaction_id']);
    }

    public function test_charge_subscription_transaction_handleexception()
    {
        $this->setExpectedException(ClientException::class);

        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml'])
        ]));

        $api = (new ChargeSubscription($this->getAuth()))
            ->setClient($client)
            ->setTransaction(123)
        ;
        $api->call();
    }
}

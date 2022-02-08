<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\ReleaseReservation;
use Altapay\Response\Embeds\Transaction;
use Altapay\Response\ReleaseReservationResponse as ReleaseReservationDocument;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ReleaseReservationTest extends AbstractApiTest
{
    /**
     * @return ReleaseReservation
     */
    protected function getReleaseReservation()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/release.xml');

        return (new ReleaseReservation($this->getAuth()))
            ->setClient($client);
    }

    public function test_release_reservation(): void
    {
        $api = $this->getReleaseReservation();
        $api->setTransaction('123');
        $this->assertInstanceOf(ReleaseReservationDocument::class, $api->call());
    }

    /**
     * @depends test_release_reservation
     */
    public function test_release_data(): void
    {
        $api = $this->getReleaseReservation();
        $api->setTransaction('123');
        $response = $api->call();
        $this->assertInstanceOf(ReleaseReservationDocument::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertCount(1, $response->Transactions);
    }

    public function test_release_reservation_querypath(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getReleaseReservation();
        $api->setTransaction($transaction);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('releaseReservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('456', $parts['transaction_id']);

        $api = $this->getReleaseReservation();
        $api->setTransaction('123');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('123', $parts['transaction_id']);

        $api = $this->getReleaseReservation();
        $api->setTransaction('152');
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('152', $parts['transaction_id']);
    }

    public function test_capture_reservation_transaction_handleexception(): void
    {
        $this->expectException(ClientException::class);

        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml'])
        ]));

        $api = (new ReleaseReservation($this->getAuth()))
            ->setClient($client)
            ->setTransaction('123');
        $api->call();
    }
}

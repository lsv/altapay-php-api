<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\CaptureReservation;
use Altapay\Request\OrderLine;
use Altapay\Response\CaptureReservationResponse;
use Altapay\Response\Embeds\Transaction;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class CaptureReservationTest extends AbstractApiTest
{

    /**
     * @return CaptureReservation
     */
    protected function getCaptureReservation()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/capture.xml');

        return (new CaptureReservation($this->getAuth()))
            ->setClient($client);
    }

    public function test_capture_reservation(): void
    {
        $api = $this->getCaptureReservation();
        $api->setTransaction('123');
        $this->assertInstanceOf(CaptureReservationResponse::class, $api->call());
    }

    /**
     * @depends test_capture_reservation
     */
    public function test_capture_reservation_data(): void
    {
        $api = $this->getCaptureReservation();
        $api->setTransaction('123');
        $response = $api->call();

        $this->assertInstanceOf(CaptureReservationResponse::class, $response);
        $this->assertSame(0.20, $response->CaptureAmount);
        $this->assertSame('978', $response->CaptureCurrency);
        $this->assertSame('Success', $response->Result);
        $this->assertSame('Success', $response->CaptureResult);
        $this->assertCount(1, $response->Transactions);
    }

    public function test_capture_reservation_transactions_data(): void
    {
        $api = $this->getCaptureReservation();
        $api->setTransaction('123');
        $response = $api->call();
        $this->assertInstanceOf(CaptureReservationResponse::class, $response);
        $transaction = $response->Transactions[0];
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertSame('1', $transaction->TransactionId);
        $this->assertSame('978', $transaction->MerchantCurrency);
        $this->assertSame(13.37, $transaction->FraudRiskScore);
        $this->assertSame(1.0, $transaction->ReservedAmount);
    }

    public function test_capture_reservation_transaction_request(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setAmount(158);
        $api->setReconciliationIdentifier('myidentifier');
        $api->setInvoiceNumber('number');
        $api->setSalesTax(5.00);
        $api->call();

        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('captureReservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('456', $parts['transaction_id']);
        $this->assertSame('158', $parts['amount']);
        $this->assertSame('myidentifier', $parts['reconciliation_identifier']);
        $this->assertSame('number', $parts['invoice_number']);
        $this->assertSame('5', $parts['sales_tax']);
    }

    public function test_capture_reservation_transaction_orderlines(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines($this->getOrderLines());
        $api->call();

        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('captureReservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertCount(2, $parts['orderLines']);
        $line = $parts['orderLines'][1];
        $this->assertSame('Brown sugar', $line['description']);
        $this->assertSame('productid2', $line['itemId']);
        $this->assertSame('2.5', $line['quantity']);
        $this->assertSame('8.75', $line['unitPrice']);
        $this->assertSame('20', $line['taxPercent']);
        $this->assertSame('kg', $line['unitCode']);
    }

    public function test_capture_reservation_transaction_orderlines_object(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines(new OrderLine('White sugar', 'productid', 1.5, 5.75));
        $api->call();

        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('captureReservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertCount(1, $parts['orderLines']);
    }

    public function test_capture_reservation_transaction_handleexception(): void
    {
        $this->expectException(ClientException::class);

        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml']),
        ]));

        $api = (new CaptureReservation($this->getAuth()))
            ->setClient($client)
            ->setTransaction('123');
        $api->call();
    }
}

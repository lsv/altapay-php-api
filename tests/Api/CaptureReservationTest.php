<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\CaptureReservation;
use Altapay\Response\CaptureReservationResponse;
use Altapay\Request\OrderLine;
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
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/capture.xml'))
        ]));

        return (new CaptureReservation($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_capture_reservation()
    {
        $api = $this->getCaptureReservation();
        $api->setTransaction(123);
        $this->assertInstanceOf(CaptureReservationResponse::class, $api->call());
    }

    /**
     * @depends test_capture_reservation
     */
    public function test_capture_reservation_data()
    {
        $api = $this->getCaptureReservation();
        $api->setTransaction(123);
        /** @var CaptureReservationResponse $response */
        $response = $api->call();

        $this->assertEquals(0.20, $response->CaptureAmount);
        $this->assertEquals('978', $response->CaptureCurrency);
        $this->assertEquals('Success', $response->Result);
        $this->assertEquals('Success', $response->CaptureResult);
        $this->assertCount(1, $response->Transactions);
    }

    public function test_capture_reservation_transactions_data()
    {
        $api = $this->getCaptureReservation();
        $api->setTransaction(123);
        /** @var CaptureReservationResponse $response */
        $response = $api->call();
        /** @var Transaction $transaction */
        $transaction = $response->Transactions[0];
        $this->assertEquals(1, $transaction->TransactionId);
        $this->assertEquals(978, $transaction->MerchantCurrency);
        $this->assertEquals(13.37, $transaction->FraudRiskScore);
        $this->assertEquals(1, $transaction->ReservedAmount);
    }

    public function test_capture_reservation_transaction_request()
    {
        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setAmount(158);
        $api->setReconciliationIdentifier('myidentifier');
        $api->setInvoiceNumber('number');
        $api->setSalesTax(5.00);
        $api->call();

        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('captureReservation/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals(456, $parts['transaction_id']);
        $this->assertEquals(158, $parts['amount']);
        $this->assertEquals('myidentifier', $parts['reconciliation_identifier']);
        $this->assertEquals('number', $parts['invoice_number']);
        $this->assertEquals('5.00', $parts['sales_tax']);
    }

    public function test_capture_reservation_transaction_orderlines()
    {
        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines($this->getOrderLines());
        $api->call();

        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('captureReservation/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);

        $this->assertCount(2, $parts['orderLines']);
        $line = $parts['orderLines'][1];

        $this->assertEquals('Brown sugar', $line['description']);
        $this->assertEquals('productid2', $line['itemId']);
        $this->assertEquals('2.5', $line['quantity']);
        $this->assertEquals('8.75', $line['unitPrice']);
        $this->assertEquals('20', $line['taxPercent']);
        $this->assertEquals('kg', $line['unitCode']);
    }

    public function test_capture_reservation_transaction_orderlines_object()
    {
        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines(new OrderLine('White sugar', 'productid', 1.5, 5.75));
        $api->call();

        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('captureReservation/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);

        $this->assertCount(1, $parts['orderLines']);
    }

    public function test_capture_reservation_transaction_orderlines_randomarray()
    {
        $this->setExpectedException(\InvalidArgumentException::class, sprintf(
            'orderLines should all be a instance of "%s"',
            OrderLine::class
        ));

        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $api = $this->getCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines(['myobject']);
        $api->call();
    }

    public function test_capture_reservation_transaction_handleexception()
    {
        $this->setExpectedException(ClientException::class);

        $transaction = new Transaction();
        $transaction->TransactionId = 456;

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml'])
        ]));

        $api = (new CaptureReservation($this->getAuth()))
            ->setClient($client)
            ->setTransaction(123)
        ;
        $api->call();
    }
}

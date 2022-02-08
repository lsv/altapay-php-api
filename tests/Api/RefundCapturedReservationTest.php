<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\RefundCapturedReservation;
use Altapay\Request\OrderLine;
use Altapay\Response\Embeds\Transaction;
use Altapay\Response\RefundResponse;
use Altapay\Exceptions\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class RefundCapturedReservationTest extends AbstractApiTest
{
    /**
     * @return RefundCapturedReservation
     */
    protected function getRefundCaptureReservation()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/refundcapture.xml');

        return (new RefundCapturedReservation($this->getAuth()))
            ->setClient($client);
    }

    public function test_refund_reservation(): void
    {
        $api = $this->getRefundCaptureReservation();
        $api->setTransaction('123');
        $this->assertInstanceOf(RefundResponse::class, $api->call());
    }

    /**
     * @depends test_refund_reservation
     */
    public function test_capture_refund_data(): void
    {
        $api = $this->getRefundCaptureReservation();
        $api->setTransaction('123');
        $response = $api->call();
        $this->assertInstanceOf(RefundResponse::class, $response);

        $this->assertSame(0.12, $response->getRefundAmount());
        $this->assertSame('978', $response->RefundCurrency);
        $this->assertSame('Success', $response->Result);
        $this->assertSame('Success', $response->RefundResult);
        $this->assertCount(1, $response->Transactions);
    }

    /**
     * @depends test_refund_reservation
     */
    public function test_capture_refund_transactions_data(): void
    {
        $api = $this->getRefundCaptureReservation();
        $api->setTransaction('123');
        $response = $api->call();
        $this->assertInstanceOf(RefundResponse::class, $response);
        $transaction = $response->Transactions[0];
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertSame('1', $transaction->TransactionId);
        $this->assertSame('978', $transaction->MerchantCurrency);
        $this->assertSame(13.37, $transaction->FraudRiskScore);
        $this->assertSame(1.0, $transaction->ReservedAmount);
    }

    /**
     * @depends test_refund_reservation
     */
    public function test_capture_refund_transaction_request(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getRefundCaptureReservation();
        $api->setTransaction($transaction);
        $api->setAmount(158);
        $api->setReconciliationIdentifier('myidentifier');
        $api->setInvoiceNumber('number');
        $api->setAllowOverRefund(true);
        $api->call();

        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('refundCapturedReservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('456', $parts['transaction_id']);
        $this->assertSame('158', $parts['amount']);
        $this->assertSame('myidentifier', $parts['reconciliation_identifier']);
        $this->assertSame('number', $parts['invoice_number']);
        $this->assertSame('1', $parts['allow_over_refund']);
    }

    /**
     * @depends test_refund_reservation
     */
    public function test_capture_refund_transaction_orderlines(): void
    {
        $orderlines            = [];
        $orderline             = new OrderLine('White sugar', 'productid', 1.5, 5.75);
        $orderline->taxPercent = 20;
        $orderline->unitCode   = 'kg';
        $orderlines[]          = $orderline;

        $orderline             = new OrderLine('Brown sugar', 'productid2', 2.5, 8.75);
        $orderline->unitCode   = 'kg';
        $orderline->taxPercent = 20;
        $orderlines[]          = $orderline;

        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getRefundCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines($orderlines);
        $api->call();

        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('refundCapturedReservation'), $request->getUri()->getPath());
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

    /**
     * @depends test_refund_reservation
     */
    public function test_capture_refund_transaction_orderlines_object(): void
    {
        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $api = $this->getRefundCaptureReservation();
        $api->setTransaction($transaction);
        $api->setOrderLines(new OrderLine('White sugar', 'productid', 1.5, 5.75));
        $api->call();

        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('refundCapturedReservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertCount(1, $parts['orderLines']);
    }

    public function test_capture_refund_transaction_handleexception(): void
    {
        $this->expectException(ClientException::class);

        $transaction                = new Transaction();
        $transaction->TransactionId = '456';

        $client = $this->getClient($mock = new MockHandler([
            new Response(400, ['text-content' => 'application/xml'])
        ]));

        $api = (new RefundCapturedReservation($this->getAuth()))
            ->setClient($client)
            ->setTransaction('123');
        $api->call();
    }
}

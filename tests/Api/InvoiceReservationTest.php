<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\InvoiceReservation;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class InvoiceReservationTest extends AbstractApiTest
{
    /**
     * @return InvoiceReservation
     */
    protected function getapi()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/invoicereservation.xml');

        return (new InvoiceReservation($this->getAuth()))
            ->setClient($client);
    }

    public function test_missing_all_options(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(
            'The required options "amount", "currency", "shop_orderid", "terminal" are missing.'
        );
        $this->getapi()->call();
    }

    public function test_url(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('createInvoiceReservation/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('my terminal', $parts['terminal']);
        $this->assertSame('order id', $parts['shop_orderid']);
        $this->assertSame('200.5', $parts['amount']);
        $this->assertSame('957', $parts['currency']);
    }

    public function test_options(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $api->setType('subscriptionAndCharge');
        $api->setAccountNumber('account');
        $api->setPaymentSource('mail_order');
        $api->setBankCode('code');
        $api->setFraudService('maxmind');

        $api->call();
        $request = $api->getRawRequest();

        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('subscriptionAndCharge', $parts['type']);
        $this->assertSame('account', $parts['accountNumber']);
        $this->assertSame('mail_order', $parts['payment_source']);
        $this->assertSame('code', $parts['bankCode']);
        $this->assertSame('maxmind', $parts['fraud_service']);
    }
}

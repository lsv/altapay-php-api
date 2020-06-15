<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\CalculateSurcharge;
use Altapay\Response\SurchargeResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class CalculateSurchargeTest extends AbstractApiTest
{

    /**
     * @return CalculateSurcharge
     */
    protected function getapi()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/calculatesurcharge.xml'))
        ]));

        return (new CalculateSurcharge($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_options_fields_not_allowed_when_payment_id_is_set()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'The fields "currency, terminal, credit_card_token" is not allowed when "payment_id" is set'
        );

        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(986);
        $api->setCreditCardToken('1234');
        $api->setTerminal('my terminal');
        $api->setPaymentId('123');
        $api->call();
    }

    public function test_options_fields_required_when_payment_not_set()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'The fields "terminal, credit_card_token, currency" is required'
        );

        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(986);
        $api->setTerminal('my terminal');
        $api->call();
    }

    public function test_options_payment_and_amount_is_ok()
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setPaymentId('123');
        $response = $api->call();
        $this->assertInstanceOf(SurchargeResponse::class, $response);
    }

    public function test_options_fields_and_amount_is_ok()
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(986);
        $api->setCreditCardToken('1234');
        $api->setTerminal('my terminal');
        $response = $api->call();
        $this->assertInstanceOf(SurchargeResponse::class, $response);
    }

    public function test_payment_id_route()
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setPaymentId('123');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('calculateSurcharge/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('200.50', $parts['amount']);
        $this->assertEquals('123', $parts['payment_id']);
    }

    public function test_fields_route()
    {
        $api = $this->getapi();
        $api->setAmount(200);
        $api->setCurrency('DKK');
        $api->setCreditCardToken('1234');
        $api->setTerminal('my terminal');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('calculateSurcharge/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('200', $parts['amount']);
        $this->assertEquals('DKK', $parts['currency']);
        $this->assertEquals('1234', $parts['credit_card_token']);
        $this->assertEquals('my terminal', $parts['terminal']);
    }

    public function test_object()
    {
        $api = $this->getapi();
        $api->setAmount(200);
        $api->setCurrency('dkk');
        $api->setCreditCardToken('1234');
        $api->setTerminal('my terminal');
        /** @var SurchargeResponse $response */
        $response = $api->call();

        $this->assertEquals('Success', $response->Result);
        $this->assertEquals('12.34', $response->SurchageAmount);
        $this->assertNull($response->ThreeDSecureResult);
    }
}

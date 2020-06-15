<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\Credit;
use Altapay\Request\Card;
use Altapay\Exceptions\CreditCardTokenAndCardUsedException;
use Altapay\Response\CreditResponse as CreditResponse;
use Altapay\Types\PaymentSources;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CreditTest extends AbstractApiTest
{

    /**
     * @return Credit
     */
    protected function getCredit()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/reservationoffixedamount.xml'))
        ]));

        return (new Credit($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_options()
    {
        $this->setExpectedException(CreditCardTokenAndCardUsedException::class);

        $card = new Card(1234, 12, 12, 122);
        $api = $this->getCredit();
        $api->setTerminal('123');
        $api->setShopOrderId(123);
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCard($card);
        $api->setCreditCardToken('12345');
        $api->call();
    }

    public function test_creditcard_options()
    {
        $card = new Card(1234567890, 5, 19, 122);
        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId(123);
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCard($card);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('credit'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('terminal', $parts['terminal']);
        $this->assertEquals(123, $parts['shop_orderid']);
        $this->assertEquals(20.44, $parts['amount']);
        $this->assertEquals(967, $parts['currency']);
        $this->assertEquals(1234567890, $parts['cardnum']);
        $this->assertEquals(5, $parts['emonth']);
        $this->assertEquals(19, $parts['eyear']);
        $this->assertEquals(122, $parts['cvc']);
    }

    public function test_creditcardtoken_options()
    {
        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId(123);
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCreditCardToken('token');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('credit'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('terminal', $parts['terminal']);
        $this->assertEquals(123, $parts['shop_orderid']);
        $this->assertEquals(20.44, $parts['amount']);
        $this->assertEquals(967, $parts['currency']);
        $this->assertEquals('token', $parts['credit_card_token']);
    }

    public function paymentSourceDataProvider()
    {
        return [
            PaymentSources::getAllowed()
        ];
    }

    /**
     * @dataProvider paymentSourceDataProvider
     * @param string $type
     */
    public function test_paymentsource_options($type)
    {
        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId(123);
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCreditCardToken('token');
        $api->setPaymentSource($type);
        $api->call();
    }

    public function test_paymentsource_invalid_options()
    {
        $this->setExpectedException(InvalidOptionsException::class);

        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId(123);
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCreditCardToken('token');
        $api->setPaymentSource('webshop');
        $api->call();
    }

    public function test_response()
    {
        $card = new Card(1234567890, 5, 19, 122);
        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId(123);
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCard($card);

        /** @var CreditResponse $response */
        $response = $api->call();

        $this->assertInstanceOf(CreditResponse::class, $response);
        $this->assertEquals('Success', $response->Result);
        $this->assertCount(1, $response->Transactions);
    }
}

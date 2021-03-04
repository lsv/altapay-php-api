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
        $client = $this->getXmlClient(__DIR__ . '/Results/reservationoffixedamount.xml');

        return (new Credit($this->getAuth()))
            ->setClient($client);
    }

    public function test_options(): void
    {
        $this->expectException(CreditCardTokenAndCardUsedException::class);

        $card = new Card('1234', '12', '12', '122');
        $api  = $this->getCredit();
        $api->setTerminal('123');
        $api->setShopOrderId('123');
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCard($card);
        $api->setCreditCardToken('12345');
        $api->call();
    }

    public function test_creditcard_options(): void
    {
        $card = new Card('1234567890', '05', '19', '122');
        $api  = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId('123');
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCard($card);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('credit'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('terminal', $parts['terminal']);
        $this->assertSame('123', $parts['shop_orderid']);
        $this->assertSame('20.44', $parts['amount']);
        $this->assertSame('967', $parts['currency']);
        $this->assertSame('1234567890', $parts['cardnum']);
        $this->assertSame('05', $parts['emonth']);
        $this->assertSame('19', $parts['eyear']);
        $this->assertSame('122', $parts['cvc']);
    }

    public function test_creditcardtoken_options(): void
    {
        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId('123');
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCreditCardToken('token');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('credit'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('terminal', $parts['terminal']);
        $this->assertSame('123', $parts['shop_orderid']);
        $this->assertSame('20.44', $parts['amount']);
        $this->assertSame('967', $parts['currency']);
        $this->assertSame('token', $parts['credit_card_token']);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function paymentSourceDataProvider()
    {
        return [
            PaymentSources::getAllowed()
        ];
    }

    /**
     * @dataProvider paymentSourceDataProvider
     *
     * @param string $type
     */
    public function test_paymentsource_options($type): void
    {
        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId('123');
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCreditCardToken('token');
        $api->setPaymentSource($type);
        $response = $api->call();

        $this->assertInstanceOf(CreditResponse::class, $response);
    }

    public function test_paymentsource_invalid_options(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $api = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId('123');
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCreditCardToken('token');
        $api->setPaymentSource('webshop');
        $api->call();
    }

    public function test_response(): void
    {
        $card = new Card('1234567890', '05', '19', '122');
        $api  = $this->getCredit();
        $api->setTerminal('terminal');
        $api->setShopOrderId('123');
        $api->setAmount(20.44);
        $api->setCurrency(967);
        $api->setCard($card);

        $response = $api->call();

        $this->assertInstanceOf(CreditResponse::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertCount(1, $response->Transactions);
    }
}

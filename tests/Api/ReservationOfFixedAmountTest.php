<?php

namespace Altapay\ApiTest\Api\Api;

use Altapay\Api\Payments\ReservationOfFixedAmount;
use Altapay\Request\Address;
use Altapay\Request\Card;
use Altapay\Request\Customer;
use Altapay\Response\Embeds\Terminal;
use Altapay\Response\ReservationOfFixedAmountResponse;
use Altapay\Types\FraudServices;
use Altapay\Types\PaymentSources;
use Altapay\Types\PaymentTypes;
use Altapay\Types\ShippingMethods;
use Altapay\Types\TypeInterface;
use Altapay\ApiTest\Api\AbstractApiTest;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ReservationOfFixedAmountTest extends AbstractApiTest
{

    /**
     * @return ReservationOfFixedAmount
     */
    protected function getapi()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/reservationoffixedamount.xml'))
        ]));

        return (new ReservationOfFixedAmount($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_missing_all_options()
    {
        $this->setExpectedException(
            MissingOptionsException::class,
            'The required options "amount", "currency", "shop_orderid", "terminal" are missing.'
        );
        $this->getapi()->call();
    }

    public function test_missing_terminal_options()
    {
        $this->setExpectedException(
            MissingOptionsException::class,
            'The required option "terminal" is missing.'
        );

        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->call();
    }

    public function test_url()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('reservationOfFixedAmount/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('my terminal', $parts['terminal']);
        $this->assertEquals('order id', $parts['shop_orderid']);
        $this->assertEquals(200.50, $parts['amount']);
        $this->assertEquals(957, $parts['currency']);
        $this->assertEquals(155.23, $parts['surcharge']);
    }

    public function test_terminal()
    {
        $terminal = new Terminal();
        $terminal->Title = 'terminal object';

        $api = $this->getapi();
        $api->setTerminal($terminal);
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('reservationOfFixedAmount/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('terminal object', $parts['terminal']);
    }

    public function test_wrong_currency()
    {
        $this->setExpectedException(
            InvalidOptionsException::class,
            'The option "currency" with value "danske kroner" is invalid.'
        );

        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency('danske kroner');
        $api->setShopOrderId('order id');
        $api->setSurcharge(155.23);
        $api->call();
    }

    public function test_creditcard_and_token()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'You can not set both a credit card and a credit card token'
        );

        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $api->setCard(new Card(1234, 10, 12, 100));
        $api->setCreditCardToken('token');
        $api->call();
    }

    public function test_token_and_creditcard()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'You can not set both a credit card token and a credit card'
        );

        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $api->setCreditCardToken('token');
        $api->setCard(new Card(1234, 10, 12, 100));
        $api->call();
    }

    public function test_creditcard_query()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $api->setCard(new Card(1234, 10, 12, 100));
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals(1234, $parts['cardnum']);
        $this->assertEquals(12, $parts['eyear']);
        $this->assertEquals(10, $parts['emonth']);
        $this->assertEquals(100, $parts['cvc']);
    }

    public function test_creditcardtoken_query()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $api->setCreditCardToken('credit card token', 200);
        $api->call();
        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('credit card token', $parts['credit_card_token']);
        $this->assertEquals(200, $parts['cvc']);
    }

    public function test_customer_query()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $api->setCustomerInfo($this->getCustomerInfo());
        $api->call();

        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('my address', $parts['customer_info']['billing_address']);
        $this->assertEquals('Last name', $parts['customer_info']['billing_lastname']);
        $this->assertEquals('2000', $parts['customer_info']['billing_postal']);
        $this->assertEquals('Somewhere', $parts['customer_info']['billing_city']);
        $this->assertEquals('0', $parts['customer_info']['billing_region']);
        $this->assertEquals('DK', $parts['customer_info']['billing_country']);
        $this->assertEquals('First name', $parts['customer_info']['billing_firstname']);

        $this->assertEquals('First name', $parts['customer_info']['shipping_firstname']);
        $this->assertEquals('Last name', $parts['customer_info']['shipping_lastname']);
        $this->assertEquals('my address', $parts['customer_info']['shipping_address']);
        $this->assertEquals('Somewhere', $parts['customer_info']['shipping_city']);
        $this->assertEquals('0', $parts['customer_info']['shipping_region']);
        $this->assertEquals('2000', $parts['customer_info']['shipping_postal']);
        $this->assertEquals('DK', $parts['customer_info']['shipping_country']);

        $this->assertEquals('2016-11-25', $parts['customer_created_date']);
    }

    public function test_customer_fullquery()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $billing = new Address();
        $billing->Firstname = 'First name';
        $billing->Lastname = 'Last name';
        $billing->Address = 'my address';
        $billing->City = 'Somewhere';
        $billing->PostalCode = '2000';
        $billing->Region = '0';
        $billing->Country = 'DK';

        $shipping = new Address();
        $shipping->Firstname = 'First name';
        $shipping->Lastname = 'Last name';
        $shipping->Address = 'my address';
        $shipping->City = 'Somewhere';
        $shipping->PostalCode = '2000';
        $shipping->Region = '0';
        $shipping->Country = 'DK';

        $customer = new Customer($billing, $shipping);
        $customer->setShipping($shipping);
        $customer->setBirthdate(new \DateTime('2001-11-28'));
        $customer->setEmail('my@mail.com');
        $customer->setUsername('username');
        $customer->setPhone('50607080');
        $customer->setBankName('bank name');
        $customer->setBankPhone('20304050');

        $api->setCustomerInfo($customer);
        $api->call();

        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('2001-11-28', $parts['customer_info']['birthdate']);
        $this->assertEquals('my@mail.com', $parts['customer_info']['email']);
        $this->assertEquals('username', $parts['customer_info']['username']);
        $this->assertEquals('50607080', $parts['customer_info']['customer_phone']);
        $this->assertEquals('bank name', $parts['customer_info']['bank_name']);
        $this->assertEquals('20304050', $parts['customer_info']['bank_phone']);
    }

    public function test_type()
    {
        $this->allowedTypes(
            PaymentTypes::class,
            'type',
            'setType'
        );
    }

    public function test_payment_source()
    {
        $this->allowedTypes(
            PaymentSources::class,
            'payment_source',
            'setPaymentSource'
        );
    }

    public function test_fraud_service()
    {
        $this->allowedTypes(
            FraudServices::class,
            'fraud_service',
            'setFraudService'
        );
    }

    public function test_shipping_method()
    {
        $this->allowedTypes(
            ShippingMethods::class,
            'shipping_method',
            'setShippingMethod'
        );
    }

    public function test_transaction_info()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        $transactionInfo[] = 'Trans 1';
        $transactionInfo[] = 'Trans 2';
        $api->setTransactionInfo($transactionInfo);
        $api->call();

        $request = $api->getRawRequest();
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertCount(2, $parts['transaction_info']);
        $this->assertEquals('Trans 2', $parts['transaction_info'][1]);
    }

    public function test_result()
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        /** @var ReservationOfFixedAmountResponse $response */
        $response = $api->call();

        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);
        $this->assertEquals('Success', $response->Result);
        $this->assertCount(1, $response->Transactions);
    }

    public function test_real_api_call_response()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/reservationoffixedamount_2.xml'))
        ]));

        $api = (new ReservationOfFixedAmount($this->getAuth()))
            ->setClient($client)
        ;

        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');

        /** @var ReservationOfFixedAmountResponse $response */
        $response = $api->call();
        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);
    }

    /**
     * @param string|TypeInterface $class
     * @param string $key
     * @param string $setter
     */
    private function allowedTypes($class, $key, $setter)
    {
        foreach ($class::getAllowed() as $type) {
            $api = $this->getapi();
            $api->setTerminal('my terminal');
            $api->setAmount(200.50);
            $api->setCurrency(957);
            $api->setShopOrderId('order id');
            $api->{$setter}($type);
            $api->call();
            $request = $api->getRawRequest();
            parse_str($request->getUri()->getQuery(), $parts);
            $this->assertEquals($type, $parts[$key]);

            $this->assertTrue($class::isAllowed($type));
        }

        $this->disallowedTypes($class, $key, $setter);
    }

    /**
     * @param string|TypeInterface $class
     * @param string $key
     * @param string $method
     */
    private function disallowedTypes($class, $key, $method)
    {
        $this->setExpectedException(
            InvalidOptionsException::class,
            sprintf(
                'The option "%s" with value "not allowed type" is invalid. Accepted values are: "%s".',
                $key,
                implode('", "', $class::getAllowed())
            )
        );

        $type = 'not allowed type';
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->{$method}($type);
        $api->call();
        $this->assertFalse($class::isAllowed($type));
    }
}

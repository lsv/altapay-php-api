<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\Payments;
use Altapay\Response\Embeds\Address;
use Altapay\Response\Embeds\Country;
use Altapay\Response\Embeds\CustomerInfo;
use Altapay\Response\Embeds\PaymentInfo;
use Altapay\Response\Embeds\PaymentNatureService;
use Altapay\Response\Embeds\ReconciliationIdentifier;
use Altapay\Response\Embeds\Transaction;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class PaymentsTest extends AbstractApiTest
{
    protected function getMultiplePaymentTransaction()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/payments.xml'))
        ]));

        $api = (new Payments($this->getAuth()))
            ->setClient($client)
        ;
        return $api->call();
    }

    protected function getSinglePaymentTransaction()
    {
        $client = $this->getClient(new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/payment.xml'))
        ]));

        $api = (new Payments($this->getAuth()))
            ->setClient($client)
        ;
        return $api->call();
    }

    public function test_payments_exception()
    {
        $this->setExpectedException(ClientException::class);

        $client = $this->getClient($mock = new MockHandler([
            new Response(400)
        ]));

        (new Payments($this->getAuth()))
            ->setClient($client)
            ->call()
        ;
    }

    public function test_payments_routing()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/payments.xml'))
        ]));

        $api = (new Payments($this->getAuth()))
            ->setClient($client)
            ->setTransaction('transactionid')
            ->setTerminal('terminalvalue')
            ->setShopOrderId('shoporderid')
            ->setShop('shopkey')
            ->setPaymentId('mypaymentid')
        ;
        $api->call();

        $this->assertInstanceOf(Request::class, $api->getRawRequest());
        $this->assertInstanceOf(Response::class, $api->getRawResponse());

        $this->assertEquals($this->getExceptedUri('payments/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertEquals('transactionid', $parts['transaction_id']);
        $this->assertEquals('terminalvalue', $parts['terminal']);
        $this->assertEquals('shoporderid', $parts['shop_orderid']);
        $this->assertEquals('shopkey', $parts['shop']);
        $this->assertEquals('mypaymentid', $parts['payment_id']);
    }

    public function test_payments_transaction_object()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/payments.xml'))
        ]));

        $trans = new Transaction();
        $trans->TransactionId = 'my trans number';

        $api = (new Payments($this->getAuth()))
            ->setClient($client)
            ->setTransaction($trans)
        ;
        $api->call();

        $this->assertEquals($this->getExceptedUri('payments/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertEquals('my trans number', $parts['transaction_id']);
    }

    public function test_single_payment_transaction_count()
    {
        $this->assertCount(1, $this->getSinglePaymentTransaction());
    }

    public function test_multiple_payment_transaction_count()
    {
        $this->assertCount(2, $this->getMultiplePaymentTransaction());
    }

    /**
     * @depends test_multiple_payment_transaction_count
     */
    public function test_multiple_payment_transaction_data()
    {
        /** @var Transaction $data */
        $data = $this->getMultiplePaymentTransaction()[0];

        $this->assertEquals(1, $data->TransactionId);
        $this->assertEquals('ccc1479c-37f9-4962-8d2c-662d75117e9d', $data->PaymentId);
        $this->assertEquals('Valid', $data->CardStatus);
        $this->assertEquals('93f534a2f5d66d6ab3f16c8a7bb7e852656d4bb2', $data->CreditCardToken);
        $this->assertEquals('411111******1111', $data->CreditCardMaskedPan);
        $this->assertEquals('false', $data->IsTokenized);
        $this->assertEquals('Not_Applicable', $data->ThreeDSecureResult);
        $this->assertEquals('Merchant', $data->LiableForChargeback);
        $this->assertEquals('4f244dec4907eba0f6432e53b17a60ebcf51365e', $data->BlacklistToken);
        $this->assertEquals('myorderid', $data->ShopOrderId);
        $this->assertEquals('AltaPay Shop', $data->Shop);
        $this->assertEquals('AltaPay Test Terminal', $data->Terminal);
        $this->assertEquals('captured', $data->TransactionStatus);
        $this->assertEquals('NONE', $data->ReasonCode);
        $this->assertEquals('978', $data->MerchantCurrency);
        $this->assertEquals('EUR', $data->MerchantCurrencyAlpha);
        $this->assertEquals('978', $data->CardHolderCurrency);
        $this->assertEquals('EUR', $data->CardHolderCurrencyAlpha);
        $this->assertEquals(1.00, $data->ReservedAmount);
        $this->assertEquals(1.00, $data->CapturedAmount);
        $this->assertEquals(0, $data->RefundedAmount);
        $this->assertEquals(0, $data->RecurringDefaultAmount);
        $this->assertInstanceOf(\DateTime::class, $data->CreatedDate);
        $this->assertInstanceOf(\DateTime::class, $data->UpdatedDate);
        $this->assertEquals('28-09-2010', $data->CreatedDate->format('d-m-Y'));
        $this->assertEquals('28-09-2010', $data->UpdatedDate->format('d-m-Y'));
        $this->assertEquals('CreditCard', $data->PaymentNature);
        $this->assertEquals('eCommerce', $data->PaymentSource);
        $this->assertEquals(13.37, $data->FraudRiskScore);
        $this->assertEquals('Fraud detection explanation', $data->FraudExplanation);

        // Payment nature service
        $this->assertInstanceOf(PaymentNatureService::class, $data->PaymentNatureService);

        // Payment Infos
        $this->assertCount(3, $data->PaymentInfos);

        // Customer info
        $this->assertInstanceOf(CustomerInfo::class, $data->CustomerInfo);

        // ReconciliationIdentifiers
        $this->assertCount(1, $data->ReconciliationIdentifiers);
    }

    /**
     * @depends test_multiple_payment_transaction_data
     */
    public function test_multiple_payment_paymentnatureservice_data()
    {
        /** @var PaymentNatureService $data */
        $data = $this->getMultiplePaymentTransaction()[0]->PaymentNatureService;

        $this->assertEquals('TestAcquirer', $data->name);
        $this->assertTrue($data->SupportsRefunds);
        $this->assertTrue($data->SupportsRelease);
        $this->assertTrue($data->SupportsMultipleCaptures);
        $this->assertFalse($data->SupportsMultipleRefunds);
    }

    public function paymentinfosDataprovider()
    {
        return [
                [
                    0, 'Form_Created_At', '2010-09-28 12:34:56'
                ],
                [
                    1, 'Form_Provider', 'AltaPay Test Form'
                ],
                [
                    2, 'Merchant_Provided_Info', 'Some info by merchant'
                ]
            ];
    }

    /**
     * @dataProvider paymentinfosDataprovider
     * @depends      test_multiple_payment_transaction_data
     * @param string $line
     * @param string $name
     * @param string $value
     */
    public function test_multiple_payment_paymentinfos_data($line, $name, $value)
    {
        /** @var PaymentInfo[] $datas */
        $datas = $this->getMultiplePaymentTransaction()[0]->PaymentInfos;

        $this->assertEquals($name, $datas[$line]->name, 'checking name');
        $this->assertEquals($value, $datas[$line]->PaymentInfo, 'checking value');
    }

    /**
     * @depends test_multiple_payment_transaction_data
     */
    public function test_multiple_payment_customerinfo_data()
    {
        /** @var CustomerInfo $data */
        $data = $this->getMultiplePaymentTransaction()[0]->CustomerInfo;

        $this->assertStringStartsWith('Mozilla/5.0', $data->UserAgent);
        $this->assertEquals('127.127.127.127', $data->IpAddress);
        $this->assertEquals('support@altapay.com', $data->Email);
        $this->assertEquals('support', $data->Username);
        $this->assertEquals('+45 7020 0056', $data->CustomerPhone);
        $this->assertEquals('12345678', $data->OrganisationNumber);
        $this->assertInstanceOf(Country::class, $data->CountryOfOrigin);

        /** @var Country $country */
        $country = $data->CountryOfOrigin;
        $this->assertEquals('DK', $country->Country);
        $this->assertEquals('BillingAddress', $country->Source);

        $this->assertInstanceOf(Address::class, $data->BillingAddress);

        /** @var Address $address */
        $address = $data->BillingAddress;
        $this->assertEquals('Palle', $address->Firstname);
        $this->assertEquals('Simonsen', $address->Lastname);
        $this->assertEquals('Rosenkæret 13', $address->Address);
        $this->assertEquals('Søborg', $address->City);
        $this->assertEquals('2860', $address->PostalCode);
        $this->assertEquals('DK', $address->Country);

        $this->assertInstanceOf(Address::class, $data->ShippingAddress);

        /** @var Address $address */
        $address = $data->ShippingAddress;
        $this->assertNull($address->Firstname);
        $this->assertNull($address->Lastname);
        $this->assertNull($address->Address);
        $this->assertNull($address->City);
        $this->assertNull($address->PostalCode);
        $this->assertNull($address->Country);

        $this->assertInstanceOf(Address::class, $data->RegisteredAddress);
    }

    /**
     * @depends      test_multiple_payment_transaction_data
     */
    public function test_multiple_payment_reconciliationidentifiers_data()
    {
        /** @var ReconciliationIdentifier $data */
        $data = $this->getMultiplePaymentTransaction()[0]->ReconciliationIdentifiers[0];

        $this->assertEquals('f4e2533e-c578-4383-b075-bc8a6866784a', $data->Id);
        $this->assertEquals(1.00, $data->Amount);
        $this->assertEquals('captured', $data->Type);
        $this->assertInstanceOf(\DateTime::class, $data->Date);
        $this->assertEquals('28-09-2010', $data->Date->format('d-m-Y'));
        $this->assertEquals('978', $data->currency);
    }
}

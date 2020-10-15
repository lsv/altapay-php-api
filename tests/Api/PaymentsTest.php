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
    /**
     * @return Transaction[]
     */
    protected function getMultiplePaymentTransaction()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/payments.xml');

        $api    = (new Payments($this->getAuth()))
            ->setClient($client);
        $result = $api->call();
        $this->assertIsArray($result);

        return $result;
    }

    /**
     * @return Transaction[]
     */
    protected function getSinglePaymentTransaction()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/payment.xml');

        $api    = (new Payments($this->getAuth()))
            ->setClient($client);
        $result = $api->call();
        $this->assertIsArray($result);

        return $result;
    }

    public function test_payments_exception(): void
    {
        $this->expectException(ClientException::class);

        $client = $this->getClient($mock = new MockHandler([
            new Response(400)
        ]));

        (new Payments($this->getAuth()))
            ->setClient($client)
            ->call();
    }

    public function test_payments_routing(): void
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/payments.xml');

        $api = (new Payments($this->getAuth()))
            ->setClient($client)
            ->setTransaction('transactionid')
            ->setTerminal('terminalvalue')
            ->setShopOrderId('shoporderid')
            ->setShop('shopkey')
            ->setPaymentId('mypaymentid');
        $api->call();

        $this->assertInstanceOf(Request::class, $api->getRawRequest());
        $this->assertInstanceOf(Response::class, $api->getRawResponse());

        $this->assertSame($this->getExceptedUri('payments/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertSame('transactionid', $parts['transaction_id']);
        $this->assertSame('terminalvalue', $parts['terminal']);
        $this->assertSame('shoporderid', $parts['shop_orderid']);
        $this->assertSame('shopkey', $parts['shop']);
        $this->assertSame('mypaymentid', $parts['payment_id']);
    }

    public function test_payments_transaction_object(): void
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/payments.xml');

        $trans                = new Transaction();
        $trans->TransactionId = '123';

        $api = (new Payments($this->getAuth()))
            ->setClient($client)
            ->setTransaction($trans);
        $api->call();

        $this->assertSame($this->getExceptedUri('payments/'), $api->getRawRequest()->getUri()->getPath());
        parse_str($api->getRawRequest()->getUri()->getQuery(), $parts);

        $this->assertSame('123', $parts['transaction_id']);
    }

    public function test_single_payment_transaction_count(): void
    {
        $this->assertCount(1, $this->getSinglePaymentTransaction());
    }

    public function test_multiple_payment_transaction_count(): void
    {
        $this->assertCount(2, $this->getMultiplePaymentTransaction());
    }

    /**
     * @depends test_multiple_payment_transaction_count
     */
    public function test_multiple_payment_transaction_data(): void
    {
        $data = $this->getMultiplePaymentTransaction()[0];
        $this->assertInstanceOf(Transaction::class, $data);

        $this->assertSame('1', $data->TransactionId);
        $this->assertSame('ccc1479c-37f9-4962-8d2c-662d75117e9d', $data->PaymentId);
        $this->assertSame('Valid', $data->CardStatus);
        $this->assertSame('93f534a2f5d66d6ab3f16c8a7bb7e852656d4bb2', $data->CreditCardToken);
        $this->assertSame('411111******1111', $data->CreditCardMaskedPan);
        $this->assertSame('false', $data->IsTokenized);
        $this->assertSame('Not_Applicable', $data->ThreeDSecureResult);
        $this->assertSame('Merchant', $data->LiableForChargeback);
        $this->assertSame('4f244dec4907eba0f6432e53b17a60ebcf51365e', $data->BlacklistToken);
        $this->assertSame('myorderid', $data->ShopOrderId);
        $this->assertSame('AltaPay Shop', $data->Shop);
        $this->assertSame('AltaPay Test Terminal', $data->Terminal);
        $this->assertSame('captured', $data->TransactionStatus);
        $this->assertSame('NONE', $data->ReasonCode);
        $this->assertSame('978', $data->MerchantCurrency);
        $this->assertSame('EUR', $data->MerchantCurrencyAlpha);
        $this->assertSame('978', $data->CardHolderCurrency);
        $this->assertSame('EUR', $data->CardHolderCurrencyAlpha);
        $this->assertSame(1.00, $data->ReservedAmount);
        $this->assertSame(1.00, $data->CapturedAmount);
        $this->assertSame(0.0, $data->RefundedAmount);
        $this->assertSame(0.0, $data->RecurringDefaultAmount);
        $this->assertInstanceOf(\DateTime::class, $data->CreatedDate);
        $this->assertInstanceOf(\DateTime::class, $data->UpdatedDate);
        $this->assertSame('28-09-2010', $data->CreatedDate->format('d-m-Y'));
        $this->assertSame('28-09-2010', $data->UpdatedDate->format('d-m-Y'));
        $this->assertSame('CreditCard', $data->PaymentNature);
        $this->assertSame('eCommerce', $data->PaymentSource);
        $this->assertSame(13.37, $data->FraudRiskScore);
        $this->assertSame('Fraud detection explanation', $data->FraudExplanation);

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
    public function test_multiple_payment_paymentnatureservice_data(): void
    {
        $data = $this->getMultiplePaymentTransaction()[0]->PaymentNatureService;
        $this->assertInstanceOf(PaymentNatureService::class, $data);

        $this->assertSame('TestAcquirer', $data->name);
        $this->assertTrue($data->SupportsRefunds);
        $this->assertTrue($data->SupportsRelease);
        $this->assertTrue($data->SupportsMultipleCaptures);
        $this->assertFalse($data->SupportsMultipleRefunds);
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function paymentinfosDataprovider()
    {
        return [
            [
                0,
                'Form_Created_At',
                '2010-09-28 12:34:56'
            ],
            [
                1,
                'Form_Provider',
                'AltaPay Test Form'
            ],
            [
                2,
                'Merchant_Provided_Info',
                'Some info by merchant'
            ]
        ];
    }

    /**
     * @dataProvider paymentinfosDataprovider
     * @depends      test_multiple_payment_transaction_data
     *
     * @param string $line
     * @param string $name
     * @param string $value
     */
    public function test_multiple_payment_paymentinfos_data($line, $name, $value): void
    {
        $datas = $this->getMultiplePaymentTransaction()[0]->PaymentInfos;

        $this->assertSame($name, $datas[$line]->name, 'checking name');
        $this->assertSame($value, $datas[$line]->PaymentInfo, 'checking value');
    }

    /**
     * @depends test_multiple_payment_transaction_data
     */
    public function test_multiple_payment_customerinfo_data(): void
    {
        $data = $this->getMultiplePaymentTransaction()[0]->CustomerInfo;

        $this->assertStringStartsWith('Mozilla/5.0', $data->UserAgent);
        $this->assertSame('127.127.127.127', $data->IpAddress);
        $this->assertSame('support@altapay.com', $data->Email);
        $this->assertSame('support', $data->Username);
        $this->assertSame('+45 7020 0056', $data->CustomerPhone);
        $this->assertSame('12345678', $data->OrganisationNumber);
        $this->assertInstanceOf(Country::class, $data->CountryOfOrigin);

        $country = $data->CountryOfOrigin;
        $this->assertInstanceOf(Country::class, $country);
        $this->assertSame('DK', $country->Country);
        $this->assertSame('BillingAddress', $country->Source);

        $this->assertInstanceOf(Address::class, $data->BillingAddress);

        $address = $data->BillingAddress;
        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Palle', $address->Firstname);
        $this->assertSame('Simonsen', $address->Lastname);
        $this->assertSame('Rosenkæret 13', $address->Address);
        $this->assertSame('Søborg', $address->City);
        $this->assertSame('2860', $address->PostalCode);
        $this->assertSame('DK', $address->Country);

        $this->assertInstanceOf(Address::class, $data->ShippingAddress);

        $address = $data->ShippingAddress;
        $this->assertInstanceOf(Address::class, $address);
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
    public function test_multiple_payment_reconciliationidentifiers_data(): void
    {
        $data = $this->getMultiplePaymentTransaction()[0]->ReconciliationIdentifiers[0];
        $this->assertInstanceOf(ReconciliationIdentifier::class, $data);

        $this->assertSame('f4e2533e-c578-4383-b075-bc8a6866784a', $data->Id);
        $this->assertSame(1.00, $data->Amount);
        $this->assertSame('captured', $data->Type);
        $this->assertInstanceOf(\DateTime::class, $data->Date);
        $this->assertSame('28-09-2010', $data->Date->format('d-m-Y'));
        $this->assertSame('978', $data->currency);
    }
}

<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Ecommerce\Callback;
use Altapay\Response\CallbackResponse;

class CallbackTest extends \PHPUnit\Framework\TestCase
{
    /** @var array<string, string> */
    private $data;

    public function setUp(): void
    {
        $this->data = [
            'shop_orderid'       => '000000022',
            'currency'           => '840',
            'type'               => 'payment',
            'embedded_window'    => '0',
            'amount'             => '0',
            'transaction_id'     => '10975531',
            'payment_id'         => 'd28df6b4-122d-49e2-add0-19c8271260b0',
            'nature'             => 'CreditCard',
            'require_capture'    => 'false',
            'payment_status'     => 'created',
            'masked_credit_card' => '457168*********0000',
            'blacklist_token'    => '1ba7bdb2752394286d511de4e9cc18d9f75f2946',
            'credit_card_token'  => '3fd3d781cc2faf1e8bb6d50e5ae2220554acbb14',
            'status'             => 'incomplete',
            'xml'                => <<<XML
<?xml version="1.0"?>
<APIResponse version="20170228">
    <Header>
        <Date>2018-01-05T14:05:04+01:00</Date>
        <Path>API/reservationOfFixedAmount3DSecureVerification</Path>
        <ErrorCode>0</ErrorCode>
        <ErrorMessage/>
    </Header>
    <Body>
        <Result>Failed</Result>
        <MerchantErrorMessage>Invalid account number (no such number)</MerchantErrorMessage>
        <CardHolderErrorMessage>Declined</CardHolderErrorMessage>
        <CardHolderMessageMustBeShown>false</CardHolderMessageMustBeShown>
        <Transactions>
            <Transaction>
                <TransactionId>1682</TransactionId>
                <PaymentId>96590f37-cf14-4861-bfb2-dff7f70be811</PaymentId>
                <AuthType>payment</AuthType>
                <CardStatus>Valid</CardStatus>
                <CreditCardExpiry>
                    <Year>2021</Year>
                    <Month>01</Month>
                </CreditCardExpiry>
                <CreditCardToken>f5d81f15a3e22150a0af972dc18e1e77f8a1cb02</CreditCardToken>
                <CreditCardMaskedPan>418000******0566</CreditCardMaskedPan>
                <IsTokenized>false</IsTokenized>
                <ThreeDSecureResult>Attempted</ThreeDSecureResult>
                <LiableForChargeback>Issuer</LiableForChargeback>
                <CVVCheckResult>MisMatched</CVVCheckResult>
                <BlacklistToken>fcbefb253fb26bf95284ae5f19b3e1345911c891</BlacklistToken>
                <ShopOrderId>000000316</ShopOrderId>
                <Shop>Dangleterre</Shop>
                <Terminal>Dangleterre CC DKK</Terminal>
                <TransactionStatus>3dsecure_failed</TransactionStatus>
                <ReasonCode>NONE</ReasonCode>
                <MerchantCurrency>208</MerchantCurrency>
                <MerchantCurrencyAlpha>DKK</MerchantCurrencyAlpha>
                <CardHolderCurrency>208</CardHolderCurrency>
                <CardHolderCurrencyAlpha>DKK</CardHolderCurrencyAlpha>
                <ReservedAmount>0.00</ReservedAmount>
                <CapturedAmount>0.00</CapturedAmount>
                <RefundedAmount>0.00</RefundedAmount>
                <CreditedAmount>0.00</CreditedAmount>
                <RecurringDefaultAmount>0.00</RecurringDefaultAmount>
                <SurchargeAmount>0.00</SurchargeAmount>
                <CreatedDate>2018-01-05 14:04:56</CreatedDate>
                <UpdatedDate>2018-01-05 14:05:03</UpdatedDate>
                <PaymentNature>CreditCard</PaymentNature>
                <PaymentSource>eCommerce</PaymentSource>
                <PaymentSchemeName>Visa</PaymentSchemeName>
                <PaymentNatureService name="AltapayAcquirer">
                    <SupportsRefunds>true</SupportsRefunds>
                    <SupportsRelease>true</SupportsRelease>
                    <SupportsMultipleCaptures>true</SupportsMultipleCaptures>
                    <SupportsMultipleRefunds>true</SupportsMultipleRefunds>
                </PaymentNatureService>
                <AddressVerification>G</AddressVerification>
                <AddressVerificationDescription>Address information is unavailable; international transaction; non-AVS participant</AddressVerificationDescription>
                <ChargebackEvents/>
                <PaymentInfos/>
                <CustomerInfo>
                    <UserAgent>Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36</UserAgent>
                    <IpAddress>83.221.155.149</IpAddress>
                    <Email>
                        <![CDATA[maa@scandesigns.dk]]>
                    </Email>
                    <Username/>
                    <CustomerPhone>10203040</CustomerPhone>
                    <OrganisationNumber/>
                    <CountryOfOrigin>
                        <Country>US</Country>
                        <Source>CardNumber</Source>
                    </CountryOfOrigin>
                    <BillingAddress>
                        <Firstname>
                            <![CDATA[test]]>
                        </Firstname>
                        <Lastname>
                            <![CDATA[test]]>
                        </Lastname>
                        <Address>
                            <![CDATA[test]]>
                        </Address>
                        <City>
                            <![CDATA[test]]>
                        </City>
                        <Region>
                            <![CDATA[0]]>
                        </Region>
                        <Country>
                            <![CDATA[DK]]>
                        </Country>
                        <PostalCode>
                            <![CDATA[1000]]>
                        </PostalCode>
                    </BillingAddress>
                    <ShippingAddress>
                        <Firstname>
                            <![CDATA[test]]>
                        </Firstname>
                        <Lastname>
                            <![CDATA[test]]>
                        </Lastname>
                        <Address>
                            <![CDATA[test]]>
                        </Address>
                        <City>
                            <![CDATA[test]]>
                        </City>
                        <Region>
                            <![CDATA[0]]>
                        </Region>
                        <Country>
                            <![CDATA[DK]]>
                        </Country>
                        <PostalCode>
                            <![CDATA[1000]]>
                        </PostalCode>
                    </ShippingAddress>
                </CustomerInfo>
                <ReconciliationIdentifiers/>
            </Transaction>
        </Transactions>
    </Body>
</APIResponse>
XML

        ];
    }

    public function test_can_handle_callback(): void
    {
        $call     = new Callback($this->data);
        $response = $call->call();
        $this->assertInstanceOf(CallbackResponse::class, $response);
        $this->assertSame('d28df6b4-122d-49e2-add0-19c8271260b0', $response->paymentId);
        $this->assertSame('000000022', $response->shopOrderId);
        $this->assertSame('incomplete', $response->status);
        $this->assertCount(1, $response->Transactions);
        $this->assertSame('1682', $response->Transactions[0]->TransactionId);
        $this->assertSame('Failed', $response->Result);
    }
}

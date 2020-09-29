<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Ecommerce\PaymentRequest;
use Altapay\Request\Config;
use Altapay\Response\PaymentRequestResponse;
use Altapay\Types\LanguageTypes;
use Altapay\Types\TypeInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class PaymentRequestTest extends AbstractApiTest
{

    const CONFIG_URL = 'https://myshop.com/callback';

    /**
     * @return PaymentRequest
     */
    protected function getapi()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/paymentrequest.xml'))
        ]));

        return (new PaymentRequest($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_required_options()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(
            'The required options "amount", "currency", "shop_orderid", "terminal" are missing.'
        );

        $api = $this->getapi();
        $api->call();
    }

    public function test_required_url()
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('createPaymentRequest/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('my terminal', $parts['terminal']);
        $this->assertEquals('order id', $parts['shop_orderid']);
        $this->assertEquals(200.50, $parts['amount']);
        $this->assertEquals(957, $parts['currency']);
    }

    public function test_options_url()
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $api->setLanguage('da');
        $api->setType('payment');
        $cctoken = $this->randomString(41);
        $api->setCcToken($cctoken);
        $api->setSaleReconciliationIdentifier('identifier');
        $api->setSaleInvoiceNumber('invoice number');
        $api->setSalesTax(15.55);
        $api->setCookie('cookie');
        $api->setPaymentSource('mail_order');
        $api->setCustomerInfo($this->getCustomerInfo());
        $api->setConfig($this->getConfig());
        $api->setFraudService('maxmind');
        $api->setShippingMethod('StorePickup');
        $api->setOrganisationNumber('my organisation');
        $api->setAccountOffer(true);
        $api->setOrderLines($this->getOrderLines());

        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('createPaymentRequest/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);

        foreach(PaymentRequest::REQUIRED_QUERY_PARAMS as $QUERY_PARAM){
            $this->assertArrayHasKey($QUERY_PARAM,$parts,'Create Payment Request is missing the "'.$QUERY_PARAM.'" query parameter.');
        }
        $this->assertEquals('da', $parts['language']);
        $this->assertIsNumeric($parts['amount'],'Amount is not numeric');
        $this->assertIsNumeric($parts['currency'],'Currency is not numeric');
        $this->assertEquals('my terminal', $parts['terminal']);
        $this->assertEquals('order id', $parts['shop_orderid']);
        $this->assertEquals(200.50, ((float)$parts['amount']));
        $this->assertEquals(957, ((int)$parts['currency']));
        if(strtolower($request->getMethod()) == 'post'){
            unset($parts);
            parse_str($request->getBody()->getContents(),$parts);
        }

        $this->assertEquals('payment', $parts['type']);
        $this->assertEquals($cctoken, $parts['ccToken']);
        $this->assertEquals('identifier', $parts['sale_reconciliation_identifier']);
        $this->assertEquals('invoice number', $parts['sale_invoice_number']);
        $this->assertEquals('15.55', $parts['sales_tax']);
        $this->assertEquals('cookie', $parts['cookie']);
        $this->assertEquals('mail_order', $parts['payment_source']);
        $this->assertEquals('maxmind', $parts['fraud_service']);
        $this->assertEquals('StorePickup', $parts['shipping_method']);
        $this->assertEquals('my organisation', $parts['organisation_number']);
        $this->assertEquals('required', $parts['account_offer']);

        // Orderlines
        $this->assertCount(2, $parts['orderLines']);
        $line = $parts['orderLines'][1];
        $this->assertEquals('Brown sugar', $line['description']);
        $this->assertEquals('productid2', $line['itemId']);
        $this->assertEquals('2.5', $line['quantity']);
        $this->assertEquals('8.75', $line['unitPrice']);
        $this->assertEquals('20', $line['taxPercent']);
        $this->assertEquals('kg', $line['unitCode']);

        // Config
        $this->assertIsArray($parts['config']);
        $config = $parts['config'];
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'form'), $config['callback_form']);
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'ok'), $config['callback_ok']);
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'fail'), $config['callback_fail']);
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'redirect'), $config['callback_redirect']);
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'open'), $config['callback_open']);
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'notification'), $config['callback_notification']);
        $this->assertEquals(sprintf('%s/%s', self::CONFIG_URL, 'verify'), $config['callback_verify_order']);

        // Customer info
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

    public function test_response()
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $response = $api->call();

        $this->assertInstanceOf(PaymentRequestResponse::class, $response);
        $this->assertEquals('Success', $response->Result);
        $this->assertEquals('2349494a-6adf-49f7-8096-2125a969e104', $response->PaymentRequestId);
        $this->assertEquals('https://gateway.altapaysecure.com/merchant.php/API/requestForm?pid=2349494a-6adf-49f7-8096-2125a969e104', $response->Url);
        $this->assertEquals('https://gateway.altapaysecure.com/eCommerce.php/API/embeddedPaymentWindow?pid=2349494a-6adf-49f7-8096-2125a969e104', $response->DynamicJavascriptUrl);
    }

    public function test_language_types()
    {
        $this->allowedTypes(
            LanguageTypes::class,
            'language',
            'setLanguage'
        );
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        $config = new Config();
        $config
            ->setCallbackForm(sprintf('%s/%s', self::CONFIG_URL, 'form'))
            ->setCallbackOk(sprintf('%s/%s', self::CONFIG_URL, 'ok'))
            ->setCallbackFail(sprintf('%s/%s', self::CONFIG_URL, 'fail'))
            ->setCallbackRedirect(sprintf('%s/%s', self::CONFIG_URL, 'redirect'))
            ->setCallbackOpen(sprintf('%s/%s', self::CONFIG_URL, 'open'))
            ->setCallbackNotification(sprintf('%s/%s', self::CONFIG_URL, 'notification'))
            ->setCallbackVerifyOrder(sprintf('%s/%s', self::CONFIG_URL, 'verify'))
        ;
        return $config;
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
            $api->setAmount(200.50);
            $api->setCurrency(957);
            $api->setShopOrderId('order id');
            $api->setTerminal('my terminal');
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
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The option "%s" with value "not allowed type" is invalid. Accepted values are: "%s".',
                $key,
                implode('", "', $class::getAllowed())
            )
        );

        $type = 'not allowed type';
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $api->{$method}($type);
        $api->call();
        $this->assertFalse($class::isAllowed($type));
    }

}

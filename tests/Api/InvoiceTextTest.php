<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\InvoiceText;
use Altapay\Response\Embeds\Address;
use Altapay\Response\Embeds\Transaction;
use Altapay\Response\InvoiceTextResponse as InvoiceTextDocument;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class InvoiceTextTest extends AbstractApiTest
{

    /**
     * @return InvoiceText
     */
    protected function getinvoicetext()
    {
        $client = $this->getClient($mock = new MockHandler([
            new Response(200, ['text-content' => 'application/xml'], file_get_contents(__DIR__ . '/Results/invoicetext.xml'))
        ]));

        return (new InvoiceText($this->getAuth()))
            ->setClient($client)
        ;
    }

    public function test_url()
    {
        $trans = new Transaction();
        $trans->TransactionId = 'my transaction number';

        $api = $this->getinvoicetext();
        $api->setTransaction($trans);
        $api->setAmount(35.33);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertEquals($this->getExceptedUri('getInvoiceText/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertEquals('my transaction number', $parts['transaction_id']);
        $this->assertEquals(35.33, $parts['amount']);
    }

    public function test_object()
    {
        $trans = new Transaction();
        $trans->TransactionId = 'my transaction number';

        $api = $this->getinvoicetext();
        $api->setTransaction($trans);
        $api->setAmount(35.33);
        /** @var InvoiceTextDocument $response */
        $response = $api->call();

        $this->assertEquals('200', $response->AccountOfferMinimumToPay);
        $this->assertStringStartsWith('Ønsker du å delbetale', $response->AccountOfferText);
        $this->assertEquals('123456789', $response->BankAccountNumber);
        $this->assertStringStartsWith('Logg på kunde', $response->LogonText);
        $this->assertEquals('234234523', $response->OcrNumber);
        $this->assertStringStartsWith('Fordringen er overdraget', $response->MandatoryInvoiceText);
        $this->assertEquals('7373', $response->InvoiceNumber);
        $this->assertEquals('832', $response->CustomerNumber);
        $this->assertInstanceOf(\DateTime::class, $response->InvoiceDate);
        $this->assertEquals('10-03-2011', $response->InvoiceDate->format('d-m-Y'));
        $this->assertInstanceOf(\DateTime::class, $response->DueDate);
        $this->assertEquals('24-03-2011', $response->DueDate->format('d-m-Y'));
        $this->assertCount(1, $response->TextInfos);
        $this->assertEquals('Password', $response->TextInfos[0]->Name);
        $this->assertEquals('xxxxxx', $response->TextInfos[0]->Value);
        $this->assertInstanceOf(Address::class, $response->Address);
        $this->assertEquals('John', $response->Address->Firstname);
        $this->assertEquals('John', $response->Address->Lastname);
        $this->assertEquals('Anywhere Street 12', $response->Address->Address);
        $this->assertEquals('Anywhere City', $response->Address->City);
        $this->assertEquals('1111', $response->Address->PostalCode);
        $this->assertEquals('DK', $response->Address->Country);
    }
}

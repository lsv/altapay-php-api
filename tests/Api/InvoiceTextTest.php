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
        $client = $this->getXmlClient(__DIR__ . '/Results/invoicetext.xml');

        return (new InvoiceText($this->getAuth()))
            ->setClient($client);
    }

    public function test_url(): void
    {
        $trans                = new Transaction();
        $trans->TransactionId = '123';

        $api = $this->getinvoicetext();
        $api->setTransaction($trans);
        $api->setAmount(35.33);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('getInvoiceText/'), $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $parts);
        $this->assertSame('123', $parts['transaction_id']);
        $this->assertSame('35.33', $parts['amount']);
    }

    public function test_object(): void
    {
        $trans                = new Transaction();
        $trans->TransactionId = '123';

        $api = $this->getinvoicetext();
        $api->setTransaction($trans);
        $api->setAmount(35.33);
        $response = $api->call();
        $this->assertInstanceOf(InvoiceTextDocument::class, $response);

        $this->assertSame('200', $response->AccountOfferMinimumToPay);
        $this->assertStringStartsWith('Ønsker du å delbetale', $response->AccountOfferText);
        $this->assertSame('123456789', $response->BankAccountNumber);
        $this->assertStringStartsWith('Logg på kunde', $response->LogonText);
        $this->assertSame('234234523', $response->OcrNumber);
        $this->assertStringStartsWith('Fordringen er overdraget', $response->MandatoryInvoiceText);
        $this->assertSame('7373', $response->InvoiceNumber);
        $this->assertSame('832', $response->CustomerNumber);
        $this->assertInstanceOf(\DateTime::class, $response->InvoiceDate);
        $this->assertSame('10-03-2011', $response->InvoiceDate->format('d-m-Y'));
        $this->assertInstanceOf(\DateTime::class, $response->DueDate);
        $this->assertSame('24-03-2011', $response->DueDate->format('d-m-Y'));
        $this->assertCount(1, $response->TextInfos);
        $this->assertSame('Password', $response->TextInfos[0]->Name);
        $this->assertSame('xxxxxx', $response->TextInfos[0]->Value);
        $this->assertInstanceOf(Address::class, $response->Address);
        $this->assertSame('John', $response->Address->Firstname);
        $this->assertSame('John', $response->Address->Lastname);
        $this->assertSame('Anywhere Street 12', $response->Address->Address);
        $this->assertSame('Anywhere City', $response->Address->City);
        $this->assertSame('1111', $response->Address->PostalCode);
        $this->assertSame('DK', $response->Address->Country);
    }
}

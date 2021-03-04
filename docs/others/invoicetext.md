[<](../index.md) Altapay - PHP Api - Invoice text
==============================================

GetInvoiceText is used for gathering information to print on the customer invoice for invoice/Arvato payments. This is typically used when merchants print their own invoices from their backend system. As the invoice sent to the customer needs to include some information from Arvato, this call is mandatory if you use Arvato's PayByBill-product.

Regarding TextInfos; These are a set of key/value-pairs which depends on which service you use. You can typically find the password for the customer to access Arvato systems (also found in LogonText), so you can customize the "LogonText" by using these values, if desired. The invoice number is always used as username.

Please refer to Arvato documentation for further details.

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Others\InvoiceText($auth);
$request->setTransactionId('12345');
$request->setAmount(200.50);
// Do the call
try {
    $response = $request->call();
    // See Response below
} catch (\Altapay\Exceptions\ClientException $e) {
    // Could not connect
} catch (\Altapay\Exceptions\ResponseHeaderException $e) {
    // Response error in header
    $e->getHeader()->ErrorMessage
} catch (\Altapay\Exceptions\ResponseMessageException $e) {
    // Error message
    $e->getMessage();
}
```

### Required

| Method  | Description | Type |
|---|---|---|
| setTransaction(string, Transaction) | The id of a specific payment. | string or [transaction object](../types/transaction.md)
| setAmount(float) | 	If you do not want to invoice the full amount a smaller amount can be captured. This amount must equal a previously captured amount | float

### Optional

No optional options allowed

### Example

```php
$request = new \Altapay\Api\Others\InvoiceText($auth);
$request->setAmount(200.50);
$request->setTransaction('12345678');
// Or
$request->setTransaction($transactionObject);
```

# Response

```
$response = $request->call();
```

Response is now a object of `\Altapay\Response\InvoiceTextResponse`

| Method  | Description | Type |
|---|---|---|
| `$response->AccountOfferMinimumToPay` | | string
| `$response->AccountOfferText` | | string
| `$response->BankAccountNumber` | | string
| `$response->LogonText` | | string
| `$response->OcrNumber` | | string
| `$response->MandatoryInvoiceText` | | string
| `$response->InvoiceNumber` | | string
| `$response->CustomerNumber` | | string
| `$response->InvoiceDate` | | DateTime
| `$response->DueDate` | | DateTime
| `$response->TextInfos` | array of `\Altapay\Response\Embeds\TextInfo` object | array
| `$response->Address` | | `\Altapay\Response\Embeds\Address` object

### `\Altapay\Response\Embeds\Address` object

| Method  | Description | Type |
|---|---|---|
| `$object->Firstname` |  | string
| `$object->Lastname` |  | string
| `$object->Address` |  | string
| `$object->City` |  | string
| `$object->PostalCode` |  | string
| `$object->Region` |  | string
| `$object->Country` |  | string

### `\Altapay\Response\Embeds\TextInfo` object

| Method  | Description | Type |
|---|---|---|
| `$object->Name` | | string
| `$object->Value` | | string

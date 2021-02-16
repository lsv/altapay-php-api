[<](../index.md) Altapay - PHP Api - Payments
==========================================

This action is used to find and check the status of a specific payment. This action takes some optional parameters which will limit the number of results.

This is NOT intended for finding multiple payments or creating reports. A report of multiple payments should be done through [Custom report](custom_report.md)

Please note that the maximum number of transactions returned is 10.

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Others\Payments($auth);
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
| setPaymentId(string) | The id of a specific payment. | string
| setShopOrderId(string) | The shop orderid passed in by the shop when the payment was created. | string

### Optional

| Method  | Description | Type |
|---|---|---|
| setShop(string) | The shop that you want to find a payment in – default is to show payments from all the shops enabled for the API-user. The shop filter is only supposed to be used in conjunction with one of the other filters (except terminal). Use this if you want to ensure that the payment returned exists in this context. | string
| setTerminal(string or Terminal object) | The terminal you want to find a payment in. The terminal filter is only supposed to be used in conjunction with one of the other filters (except shop). Use this if you want to ensure that the payment returned exists in this context. | string or [Terminal object](../types/terminal.md)

### Example

```php
$request = new \Altapay\Api\Others\Payments($auth);
$request->setTransaction('12345678');
$request->setPaymentId('12345678-12345678');
$request->setShopOrderId('shop order id');
```

# Response

```
$response = $request->call();
```

Response is a array of [`\Altapay\Response\Embeds\Transaction`](../types/transaction.md) objects

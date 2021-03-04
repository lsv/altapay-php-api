[<](../index.md) Altapay - PHP Api - Funding download
==================================================

Used to get a comma separated value file containing the details of a funding.

- [Request](#request)
    + [Required](#required)
        * [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Others\FundingDownload($auth);
$request->setFunding($funding);
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
| setFunding(Funding) |	| Funding object, which you can get from [Funding List](funding_list.md)

##### Example

```php
$request = new \Altapay\Api\Others\FundingDownload($auth);
$request->setFunding($funding);
$response = $request->call();
```

# Response

```
$response = $request->call();
```

`$response` is now a text string having this

```

Date;Type;ID;"Reconciliation Identifier";Payment;Order;Terminal;Shop;"Transaction Currency";"Transaction Amount";"Exchange Rate";"Settlement Currency";"Settlement Amount";"Fixed Fee";"Fixed Fee VAT";"Rate Based Fee";"Rate Based Fee VAT"
"2010-09-22 00:00:00";payment;5;3f664acc-c71d-45e6-8c6a-a15838451a77;5;"settlement functional4c9a127b159a0";"Altapay Test Terminal";"Altapay Functional Test Shop";EUR;50.00;100.000000;EUR;50.00;;;;
```

**Note** that the columns in the output are defined by each individual report.

To get the response as a array you can use

```
$response = $request->call();
$array = $response->__toArray(true);
// Set it to false to let the first row not have headers
```

`$array` now holds this

```
$array[0][0] = 'Date';
$array[0][1] = 'Type';
$array[0][2] = 'Reconciliation Identifier';
$array[1][0] = '2010-09-22 00:00:00';
$array[1][1] = 'payment';
$array[1][2] = '5';
```

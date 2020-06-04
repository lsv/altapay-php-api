[<](../index.md) Valitor - PHP Api - Custom report
===============================================

Used to get a comma separated value file containing the custom report. Find the id and see the optional parameters that can be passed to each custom report on the custom report details page in the merchant interface (this is only visible if you have api credentials). Custom reports can be enabled by Valitor support.

- [Request](#request)
    + [Required](#required)
        * [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Valitor\Api\Others\CustomReport($auth);
$request->setId('my terminal');
// Do the call
try {
    $response = $request->call();
    // See Response below
} catch (\Valitor\Exceptions\ClientException $e) {
    // Could not connect
} catch (\Valitor\Exceptions\ResponseHeaderException $e) {
    // Response error in header
    $e->getHeader()->ErrorMessage
} catch (\Valitor\Exceptions\ResponseMessageException $e) {
    // Error message
    $e->getMessage();
}
```

### Required

| Method  | Description | Type |
|---|---|---|
| setId(string) | 	Report id - find the id in the url when viewing the custom report in the merchant interface. | string

##### Example

```php
$request = new \Valitor\Api\Others\CustomReport($auth);
$request->setId('my custom report');
```

# Response

```
$response = $request->call();
```

`$response` is now a text string having this

```
"Order ID";"Payment ID";"Terminal title";"Transaction Date";Amount;Currency;Acquirer
orderid;7;"Valitor Soap Test Terminal";"2014-08-11 15:37:36";12.00;EUR;SoapTestAcquirer
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
$array[0][0] = 'Order ID';
$array[0][1] = 'Payment ID';
$array[0][2] = 'Terminal title';
$array[1][0] = 'orderid';
$array[1][1] = '7';
$array[1][2] = 'Valitor Soap Test Terminal';
```

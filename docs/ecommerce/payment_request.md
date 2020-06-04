[<](../index.md) Valitor - PHP Api - Payment request
=================================================

This is the preferred way of redirecting a customer to the Valitor payment page. By invoking this method you send all the relavant payment information to the gateway and in return you get a URL which you can redirect the customer to.

Because this call does not happen in the browser, you do not need to worry about the user tampering with or even seeing the data posted to the gateway, and thus don't need to pass a checksum for the customer information.

- [Request](#request)
    + [Required](#required)
        * [Example](#example)
    + [Optional](#optional)
        * [Optional parameters for invoice payments](#optional-parameters-for-invoice-payments)
    + [Required on specific payments](#required-on-specific-payments)
        * [Mandatory parameters if MCC is 6012](#mandatory-parameters-if-mcc-is-6012)
        * [Mandatory parameters for invoice payments](#mandatory-parameters-for-invoice-payments)
    + [Fraud detection](#fraud-detection)
        * [Mandatory fraud detection parameters](#mandatory-fraud-detection-parameters)
        * [Optional fraud detection parameters](#optional-fraud-detection-parameters)
- [Response](#response)

# Request

```php
$request = new \Valitor\Api\Ecommerce\PaymentRequest($auth);
// Set the options - see Required and Optional below
$request->setTerminal('my terminal');
// etc
// Do the call
try {
    $response = $request->call();
    // Response will be a object - See Response below
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
| setTerminal(string) | The title of your terminal<br/>It is also possible to pass in a terminal name with the currency as wildcard. Ex. 'My {currency} Terminal' would use the 'My EUR Terminal' if you also pass EUR along as the currency. | string
| setShopOrderId(string) | The id of the order in your webshop. This is what we will post back to you so you know which order a given payment is associated with. | [a-zA-Z0-9]{1,100} |
| setAmount(float) | The amount of the payment in english notation (ex. 89.95)<br />For a subscription the amount is the default amount for each capture.<br />Amount is limited to 2 decimals, an error will be returned if more decimals is supplied. | int, float
| setCurrency(string) | The currency of the payment in ISO-4217 format. Either the 3-digit numeric code, or the 3-letter character code. | string, int - [See currencies](../types/currencies.md)

##### Example

```php
$request = new \Valitor\Api\Ecommerce\PaymentRequest($auth);
$request->setTerminal('my terminal');
$request->setShopOrderId('123456');
$request->setAmount(200.45);
$request->setCurrency('SEK');
```

### Optional

| Method  | Description | Type |
|---|---|---|
| setLanguage(string) | The language of the payment form. Will carry on the optional parameter if specified 1). If the optional parameter is not specified it will hold the language derived from the browsers accept-headers. If none of the languages accepted by the browser are supported the language will default to English "en". | string - [See languages](../types/languages.md)
| setTransactionInfo(array) | This is a one-dimensional associative array. This is where you put any value that you would like to bind to the payment. | array
| setType(string) | The type of the authorization, please refer to the description of [Payment Types](../types/paymenttypes.md)<br />Defaults is "payment". | string - [See payment types](../types/paymenttypes.md)
| setCcToken(string) | Use the credit_card_token from a previous payment to allow your customer to buy with the same credit card again. The terminal settings have to have "Enable credit card token" enabled for this to work. | [a-z0-9]{41}
| setSaleReconciliationIdentifier(string) | If you wish to define the reconciliation identifier used in the reconciliation csv files, you can choose to set it here. This will only be used for paymentAndCapture payments. | string
| setSaleInvoiceNumber(string) | This sets the invoice number to be used on capture, if no invoice number is passed in on capture, and the amount captured is equal to the initial amount. | string
| setSalesTax(float) | 	This sets the sales tax amount that will be used on capture, unless a sales tax is passed on capture api call, and the amount captured is equal to the initial amount. | int, float
| setCookie(string) | Additionally a cookie parameter can be sent to createPaymentRequest, which is then passed back as the complete cookie for the callbacks.<br />For example, if the cookie parameter is set to: "PHPSESSID=asdfasdfdf23; mycookie=mycookievalue", the Cookie header in the callback to your page will be:<br />Cookie: PHPSESSID=asdfasdfdf23; mycookie=mycookievalue | string
| setPaymentSource(string) | The source of the payment. Default is "eCommerce" | string - [See Payment sources](../types/paymentsources.md)
| setCustomerInfo(Customer) | Customer info | Customer object [See customer info](../request/customerinfo.md) |
| setConfig(Config) | used to overwrite the terminal settings | Config object [See config](../request/config.md)
| orderLines(array or OrderLine) | Order lines | array of OrderLine objects - [See OrderLine](../request/orderline.md)

##### Optional parameters for invoice payments

| Method  | Description | Type |
|---|---|---|
| setOrganisationNumber(string) | If the organisation_number parameter is given the organisation number field in the invoice payment form is prepopulated, and if no other payment options is enabled on the terminal the form will auto submit. | string
| setAccountOffer(string) | To require having account enabled for an invoice payment for this specific customer, set this to required. To disable account for this specific customer, set to disabled. | string

### Required on specific payments

##### Mandatory parameters if MCC is 6012

MCC is short for merchant category code and enumerates what kind of business you are running. 6012 is the code for debt collection in the UK

| Method  | Description |
|---|---|---|
| Birthdate | The birth date of the customer |
| Billing Lastname | The last name for the customer's billing address. |
| Billing Postal | The postal code of the customer's billing address. |

##### Mandatory parameters for invoice payments

| Method  | Description |
|---|---|---|
| billing firstname | The first name for the customer's billing address. | string
| billing lastname | The last name for the customer's billing address. | string
| billing address | The street address of the customer's billing address. | string
| billing postal | The postal code of the customer's billing address. |

### Fraud detection

##### Mandatory fraud detection parameters

To enable fraud detection

| Method  | Description |
|---|---|---|
| setFraudService(string) | If you wish to decide pr. Payment wich fraud detection service to use | string - [See fraud services](../types/fraudservices.md)

| Method  | Description |
|---|---|---|
| billing city | The city of the customer's billing address. |
| billing region | The region of the customer's billing address. |
| billing postal | The postal code of the customer's billing address. |
| billing country | The country of the customer's billing address as a 2 character ISO-3166 country code. |

##### Optional fraud detection parameters

| Method  | Description | Type |
|---|---|---|
| email | The customer's email address. | string
| username | The customer's e-shop username/user id. This should uniquely identify the user in your (the merchant) system.	 | string
| phone | The customer's telephone number. | string
| bank name | The name of the bank where the credit card was issued. | string
| bank phone | The phone number of the bank where the credit card was issued. | string
| billing firstname | The first name for the customer's billing address. | string
| billing lastname | The last name for the customer's billing address. | string
| billing address | The street address of the customer's billing address. | string
| shipping firstname | The first name for the customer's shipping address. | string
| shipping lastname | The last name for the customer's shipping address. | string
| shipping adress | The street address of the customer's shipping address. | string
| shipping city | The city of the customer's shipping address. | string
| shipping region | The region of the customer's shipping address. | string
| shipping postal | The postal code of the customer's shipping address. | string
| shipping country | The country of the customer's shipping address as a 2 character ISO-3166 country code. | string
| created date | The creation date of the customer in your shop system. Fraud detection services can use this parameter in the fraud detection calculations. | DateTime
| setShippingMethod(string) | Fraud detection services can use this parameter in the fraud detection calculations. | string - [See shipping methods](../types/shippingmethods.md)

[See customer info](../request/customerinfo.md) on how to build a customer object

# Response

Object of `\Valitor\Response\PaymentRequestResponse`

| Method  | Description | Type |
|---|---|---|
| `$response->Result` | The result | string
| `$response->PaymentRequestId` | The ID of the payment request | string
| `$response->Url` | URL to a secure form you can redirect the user to | string
| `$response->DynamicJavascriptUrl` | Javascript URL which you can open in a iframe | string

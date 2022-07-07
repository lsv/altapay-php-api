[<](../index.md) Altapay - PHP Api - Calculate surcharge
=====================================================

This method is used to calculate the surcharge beforehand, based on a previously completed payment or a terminal, creditcard token, currency combo.

- [Request](#request)
    + [Required](#required)
        * [Example](#example)
- [Response](#response)
    + [3D results](#3d-results)

# Request

```php
$request = new \Altapay\Api\Others\CalculateSurcharge($auth);
// Set the options - see Required and Optional below
$request->setAmount(200.50);
// etc
// Do the call
try {
    $response = $request->call();
    // Response will be a object - See Response below
} catch (\Altapay\Exceptions\ClientException $e) {
    // Could not connect
} catch (\Altapay\Exceptions\ResponseHeaderException $e) {
    // Response error in header
    $e->getHeader()->ErrorMessage;
} catch (\Altapay\Exceptions\ResponseMessageException $e) {
    // Error message
    $e->getMessage();
}
```

### Required

| Method                     | Description                                                                                                                                   | Type                                                   |
|----------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------|
| setAmount(float)           | The amount to calculate the surcharge from                                                                                                    | float                                                  |
| setCurrency(string)        | Either 3 letter or 3 digit currency code. ISO-4217                                                                                            | string, int - [See currencies](../types/currencies.md) |
| setTerminal(string)        | The name of the terminal the payment will be made on.                                                                                         | string                                                 |
| setCreditCardToken(string) | A credit card token previously received from an eCommerce payment or an other MO/TO payment.                                                  | string                                                 |
| setPaymentId(string)       | The id of an existing payment/subscription to base calculation on. If payment id is sent, only amount of the other parameters should be sent. | string                                                 |

###### Example

```php
$request = new \Altapay\Api\Others\CalculateSurcharge($auth);
$request->setTerminal('my terminal');
$request->setAmount(125.50);
$request->setCurrency('SEK');
$request->setCreditCardToken('abcdef1234567890abcdef1234567890');
```

or if you have a payment ID

```php
$request = new \Altapay\Api\Others\CalculateSurcharge($auth);
$request->setPaymentId('payment id');
$request->setAmount(125.50);
```

# Response

Object of `\Altapay\Response\SurchargeResponse`

| Method                          | Description                            | Type   |
|---------------------------------|----------------------------------------|--------|
| `$response->Result`             | The result                             | string |
| `$response->SurchargeAmount`    | The amount of which will be surcharged | float  |
| `$response->ThreeDSecureResult` | The 3d secure result                   | string |

### 3D results

`Not_Applicable`: 3d secure is not applicable for this type of payment or for the state the payment is in

`Disabled`: 3D Secure has been disabled in the gateway

`CardType_Not_Supported`: The card is not part of a 3d secure scheme

`Not_Attempted`: Altapay does not support 3d secure with this acquirer/card type combination

`Not_Enrolled`: The card is not enrolled in the applicable 3d secure scheme, so the transaction was completed without 3d secure

`Declined`: 3d Secure is declined, so the payment have been declined as well

`Error`: An error occured, so the transaction was completed without 3d secure

`Attempted`: 3d secure was attempted but not successful, so the transaction was completed without full 3d secure

`Successful`: Successful and 3d secured payment

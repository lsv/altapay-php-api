[<](../index.md) Altapay - PHP Api - Query giftcard
================================================

This method is used to get information about a gift card.

Note: Currently the only information available, is the amount available on the gift card.

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Others\QueryGiftcard($auth);
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
| setTerminal(string or Terminal object) | The name of the terminal to check against. | string or [Terminal object](../types/terminal.md)
| setGiftcard(Giftcard) | The giftcard | [Giftcard object](#giftcard_object)

### Optional

No optional options allowed

### Giftcard object

```
$giftcard = new \Altapay\Request\Giftcard(account, provider, token);
```

`Account`: The card number of the gift card.
`Provider`: The gift card provider that this gift card is for. Currently the supported values are: test, PPS
`Token`: A previously returned gift card token can be used in place of the account identifier and provider. A token is only usable on terminals that have the giftcard provider enabled for which the token belongs.

### Example

```php
$giftcard = new \Altapay\Request\Giftcard('account', 'pps', '1234-1234-12345');
$request = new \Altapay\Api\Others\QueryGiftcard($auth);
$request->setTerminal('my terminal');
$request->setGiftcard($giftcard);
```

# Response

```
$response = $request->call();
```

Response is now a object of `\Altapay\Response\GiftcardResponse`

| Method  | Description | Type |
|---|---|---|
| `$response->Result` | | string
| `$response->Accounts` | array of `\Altapay\Response\Embeds\GiftCardAccount` object | array

`\Altapay\Response\Embeds\GiftCardAccount` object

| Method  | Description | Type |
|---|---|---|
| `$object->Currency` |  | string
| `$object->Balance` |  | float

[<](../index.md) Altapay - PHP Api - Terminals
===========================================

This method will allow you to extract a list of terminals that you have access to. The list will contains some details about the terminals..

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Altapay\Api\Others\Terminals($auth);
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

None required options allowed

### Optional

No optional options allowed

### Example

```php
$request = new \Altapay\Api\Others\Terminals($auth);
```

# Response

```
$response = $request->call();
```

Response is now a object of `\Altapay\Response\TerminalsResponse`

| Method  | Description | Type |
|---|---|---|
| `$response->Result` | | string
| `$response->Terminals` | array of `\Altapay\Response\Embeds\Terminal` objects | array

See [here for description of the terminal object](../types/terminal.md)

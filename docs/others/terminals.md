[<](../index.md) Valitor - PHP Api - Terminals
===========================================

This method will allow you to extract a list of terminals that you have access to. The list will contains some details about the terminals..

- [Request](#request)
    + [Required](#required)
    + [Optional](#optional)
    + [Example](#example)
- [Response](#response)

# Request

```php
$request = new \Valitor\Api\Others\Terminals($auth);
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

None required options allowed

### Optional

No optional options allowed

### Example

```php
$request = new \Valitor\Api\Others\Terminals($auth);
```

# Response

```
$response = $request->call();
```

Response is now a object of `\Valitor\Response\TerminalsResponse`

| Method  | Description | Type |
|---|---|---|
| `$response->Result` | | string
| `$response->Terminals` | array of `\Valitor\Response\Embeds\Terminal` objects | array

See [here for description of the terminal object](../types/terminal.md)

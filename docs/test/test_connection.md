[<](../index.md) Altapay - PHP Api - Test connection
=================================================

For testing if your server can reach Altapay's gateway you can use this call

```php
$baseurl = 'https://<YourShopName>.altapaysecure.com';
// Or leave $baseurl to null, to test up against the test gateway
$response = (new \Altapay\Api\Test\TestConnection($baseurl))->call();

if ($response) {
    // Connection successful
} else {
    // Connection failed
}
```

This call is the only call you can do without authentication

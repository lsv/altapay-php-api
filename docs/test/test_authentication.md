[<](../index.md) Valitor - PHP Api - Test authentication
=====================================================

For testing if your authentication is working you can use this call

```php
$auth = new \Valitor\Authentication('username', 'password', 'baseurl');
$response = (new \Valitor\Api\Test\TestAuthentication($auth))->call();

if ($response) {
    // Authentication successful
} else {
    // Authentication failed
}
```

Leave `baseurl` to null, to test up against the test gateway

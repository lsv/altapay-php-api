[<](../index.md) Altapay - PHP Api - Authentication
================================================

To all API calls (but not [Test Connection](../test/test_connection.md)) you will need a authentication object

```php
$auth = new \Altapay\Authentication('username', 'password', 'baseurl');
```

If you leave `baseurl` to null, then you will use the test gateway, otherwise use the baseurl you were given, ex `https://<YourShopName>.altapaysecure.com`

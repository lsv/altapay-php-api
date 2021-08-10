Altapay - API PHP
=================

For accessing Altapay payment gateway through the API

## Install

`composer require altapay/api-php`

## Usage

For doing a [`capture`](docs/capture.md) the following can be used

```php
$auth = new \Altapay\Authentication('username', 'password' , 'myshop.gateway.com');
$api = new \Altapay\Api\CaptureReservation($auth);
$api->setTransactionId('transaction id');
// Or you can use a transaction object you got from a previous API call
// $api->setTransaction($transactionObject);
try {
    $response = $api->call();
    // If everything went perfect, you will get a \Altapay\Api\Document\Capture in the response
} catch (\Altapay\Api\Exceptions\ClientException $e) {
    // If anything went wrong, you will get a exception where you can see the raw request and the raw response
}
```

More details in the [documentation](docs/index.md)

## Requirements

The AltaPay API PHP requires PHP 5.6.0 or grater with the following extensions installed:

- date
- filter
- mbstring
- pcre
- Reflection
- SimpleXML
- spl


## Changelog

See [CHANGELOG.md](CHANGELOG.md)

## License

See [LICENSE](LICENSE)

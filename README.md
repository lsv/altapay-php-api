# AltaPay - API PHP

For accessing AltaPay payment gateway through the API

## Installation

`composer require altapay/api-php`

## Usage

For doing a [`capture`](docs/payments/capture_reservation.md) the following can be used

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

## Requirements

The AltaPay API PHP requires PHP 5.6.0 or greater with the following extensions installed:

- date
- filter
- mbstring
- pcre
- Reflection
- SimpleXML
- spl


## Changelog

See [Changelog](CHANGELOG.md) for all the release notes.

## License

Distributed under the MIT License. See [LICENSE](LICENSE) for more information.

## Documentation

For more details please see [documentation](docs/index.md)

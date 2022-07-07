[<](../index.md) Altapay - PHP Api - Card
======================================

To create a credit card, which you can use to some calls

```php
$card = new \Altapay\Request\Card(
    'card number',
    'expiry month',
    'expiry year',
    'cvc code if needed'
);
```


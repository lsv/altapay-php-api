[<](../index.md) Altapay - PHP Api - Customer Info
==================================================

A order line object requries
- description of the item
- itemid (sku) of the item
- quantity
- Unit price excluding sales tax

```php
$description = 'Brown sugar';
$itemid = 'brown_sugar';
$quantity = 2;
$price = 17.55;

$orderline = new \Altapay\Request\OrderLine(
    $description,
    $itemid,
    $quantity,
    $price
);
```

| Method  | Description | Type |
|---|---|---|
taxAmount | Tax amount should be the total tax amount for order line | float
unitCode | Measurement unit, e.g., kg. | string
discount | The discount in percent | float
setGoodsType(string) | The type of order line it is. Should be one of the following | Can only be shipment, handling or item
imageUrl | Full url for icon of the item | string

```php
$orderline->taxAmount = 5.75;
$orderline->setGoodsType('shipment');
```


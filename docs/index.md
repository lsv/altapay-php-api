Valitor - PHP Api - Index
=========================

Docs: https://testgateway.altapaysecure.com/merchant.php/help/Merchant_API

### Methods

| Method  | Description |
|---|---|
| [Authentication](methods/authentication.md) | How to authenticate |
| [Card](methods/card.md) | Create a creditcard |

### Tests

| Method  | Description |
|---|---|
| [Test connection](test/test_connection.md) | This method requires no authentication and is used for test the connection to our system |
| [Test authentication](test/test_authentication.md) | This method test your authentication to our system. |

### eCommerce

| Method  | Description |
|---|---|
| [Payment request](ecommerce/payment_request.md) | This is the preferred way of redirecting a customer to the Valitor payment page |

### Payments

| Method  | Description |
|---|---|
| x [Capture reservation](payments/capture_reservation.md) | When the funds of a payment has been reserved and the goods are ready for delivery your system should capture the payment. |
| x [Release reservation](payments/release_reservation.md) |  Every now and then you for some reason do not want to capture a payment. In these cases you must cancel it to release the reservation of the funds. |
| x [Refund captured reservation](payments/refund_captured_reservation.md) | Sometimes after delivering the goods/services and capturing the funds you want to repay/refund the customer. Either you want to make a full refund or you only want to make a partial refund. |
| x [Reservation of fixed amount](payments/reservation_of_fixed_amount.md) | This will create a MO/TO payment. The payment can be made with a credit card, or a credit card token and the CVV |
| x [Credit](payments/credit.md) | This will create a Credit payment. The payment can be made with a credit card, or a credit card token and the CVV |
| - [Invoice reservation](payments/invoice_reservation.md) | |

### Subscription

| Method  | Description |
|---|---|
| x [Setup subscription](subscription/setup_subscription.md) | This method is used to setup a subscription |
| x [Charge subscription](subscription/charge_subscription.md) |  This is used to capture a recurring payments once. You can call this multiple times with the same payment to do several captures. |
| x [Reserve subscription charge](subscription/reserve_subscription_charge.md) | This is used to create a preauth from a subscription, as opposed to capturing it right away. You can call this multiple times with the same 'recurring_confirmed' payment to do several preauths. |

### Others

| Method  | Description |
|---|---|
| [Payments](others/payments.md) | This is used to find and check the status of a specific payment. This is **NOT** intended for finding multiple payments or creating reports. Please note that the maximum number of transactions returned is 10. |
| [Funding list](others/funding_list.md) | Used to get a list of fundings, which details when and how much money is transfered to your companys bank account. |
| [Funding download](others/funding_download.md) | Used to get a comma separated value file containing the details of a funding. |
| [Custom report](others/custom_report.md) | Used to get a comma separated value file containing the custom report |
| [Terminals](others/terminals.md) | This method will allow you to extract a list of terminals that you have access to |
| [Invoice text](others/invoicetext.md) | This is used for gathering information to print on the customer invoice for invoice/Arvato payments |
| [Calculate surcharge](others/calculate_surcharge.md) | This method is used to calculate the surcharge beforehand, based on a previously completed payment or a terminal, creditcard token, currency combo. |
| [Query giftcard](others/query_giftcard.md) | This method is used to get information about a gift card. |

```
x = Docs not written
/ = Some PHP code is missing
- = PHP code missing
```

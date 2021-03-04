[<](../index.md) Altapay - PHP Api - Payment types
==================================================

Following payment types are accepted

```
'payment',
'paymentAndCapture',
'subscription',
'subscriptionAndCharge',
'subscriptionAndReserve',
'verifyCard',
```

`payment`

When the end-user types in his creditcard info, the payment will thereafter be in the preauth state. The merchant then have to capture the payment through either the Merchant API or the Merchant Interface. This is used when the purchased goods is sent at a later date.

`paymentAndCapture`

When the end-user types in his creditcard info, the payment is authorized and captured right away. This is used when the purchased goods are available to the end-user right away, for example when paying for downloadable software. In some countries it is illegal to capture the money before sending the physical goods.

`subscription`

This is used for subscription type payments. The payment will be authed right away, but the merchant have to capture each installment either through the Merchant API or the Merchant Interface.
Note that not all acquirers support this feature. Please see Credit card acquirers for more details.

`subscriptionAndCharge`

Like the above, but will also attempt to create the first 'charge' on the subscription. In the unlikely event that the subscription cannot be setup, the charge will still be attempted. And in the event that the first charge cannot be performed (the customer might not have sufficient funds) an attempt to setup the subscription is still performed. For this reason the handling of the outcome requires a little more business logic. As a subtle consequence of this a successful subscription with a failed charge will result in a successful response, since the subscription was indeed a success. Or vice versa, if the charge succeeds but the subscription fails, the response will be successful.

`subscriptionAndReserve`

Like the above, but will also attempt to create the first 'reservation' on the subscription. In the unlikely event that the subscription cannot be setup, the reservation will still be attempted. And in the event that the first reservation cannot be performed (the customer might not have sufficient funds) an attempt to setup the subscription is still performed. For this reason the handling of the outcome requires a little more business logic. As a subtle consequence of this a successful subscription with a failed reservation will result in a successful response, since the subscription was indeed a success. Or vice versa, if the reservation succeeds but the subscription fails, the response will be successful.
Note that not all acquirers support this feature. Please see Credit card acquirers for more details.

`verifyCard`

This is used to verify the authenticity of the card, without reserving or capturing any money. This is currently only available for some acquirers, so please contact support if you wish to use this functionality.
Note that not all acquirers support this feature. Please see Credit card acquirers for more details.

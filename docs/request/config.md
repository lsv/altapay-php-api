[<](../index.md) Altapay - PHP Api - Config
===========================================

A config can have the following

```php
$config = new \Altapay\Request\Config();
$config->setCallbackForm($url);
$config->setCallbackOk($url);
$config->setCallbackFail($url);
$config->setCallbackRedirect($url);
$config->setCallbackOpen($url);
$config->setCallbackNotification($url);
$config->setCallbackVerifyOrder($url);
// Where $url is your url to your different callbacks
```

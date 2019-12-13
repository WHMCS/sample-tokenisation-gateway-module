# WHMCS Sample Tokenisation Gateway Module #

## Summary ##

Payment Gateway modules allow you to integrate payment solutions with the WHMCS
platform.

There are two types of gateway module:

* Third Party Gateways - these are payment solutions where checkout occurs
on a remote website, usually hosted by the payment gateway themselves.

* Merchant Gateways - these are payment solutions where credit card details
are collected - usually within the WHMCS application, though more and more
often this will be done remotely, typically via an iframe, with a page hosted
remotely by the payment gateway enabling tokenised storage.

The sample files here demonstrate how we suggest a Tokenised Payment Gateway
module for WHMCS be structured and implemented.

For more information, please refer to the documentation at:
https://developers.whmcs.com/payment-gateways/

## Tokenised Module ##

A tokenised module is a type of merchant gateway that accepts input of pay
method data locally and then exchanges it for a token that is stored locally
for future billing attempts.

Within WHMCS, sensitive payment data such as a card number is not stored
locally when a tokenisation module is used.

## Recommended Module Content ##

The recommended structure of a merchant gateway module is as follows.

```
 modules/gateways/
  |  tokengateway.php
```

## Minimum Requirements ##

For the latest WHMCS minimum system requirements, please refer to
https://docs.whmcs.com/System_Requirements

We recommend your module follows the same minimum requirements wherever
possible.

## Useful Resources
* [Developer Resources](https://developers.whmcs.com/)
* [Hook Documentation](https://developers.whmcs.com/hooks/)
* [API Documentation](https://developers.whmcs.com/api/)

[WHMCS Limited](https://www.whmcs.com)

# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.2.4] - 2022-06-14
### Added
- Add PreauthAmount to ReserveSubscriptionResponse
- Add missing properties from forks

## [3.2.3] - 2022-06-14
### Added
- Added CardInformation parameters for Transaction class

## [3.2.2] - 2022-04-14
### Added
- Added optional parameters for CardWalletAuthorize class
- Added setter for Transaction data 

## [3.2.1] - 2022-04-04
### Added
- Added support for the new endpoints for Apple Pay

## [3.2.0] - 2022-03-01
### Added
- Added User-Agent in API call headers
- Added new properties for ReleaseReservationResponse class
- Added new property and setter for ChargeSubscriptionResponse class
- Added support for upto PHP 8.1

## [3.1.1] - 2020-11-04
### Added
- Added missing setters
### Fixes
- Correctly parse boolean parameters

## [3.1.0] - 2020-10-19
### Added
- Support for installation and autoloading via composer
- Document all function arguments
- Added DynamicJavascriptUrl on PensioCreatePaymentRequest
- The previous exception is now forwarded

### Changed
- Split ResponseSerializer serialize() and serializeChildren()

### Fixes
- Better handle invalid responses
- Corrected remaining warning messages
- Corrected all types in code DocBlocks
- Correctly handle non-US-ASCII symbols
- Make sure all exceptions have a message

### Removed
- The definedFilters property

## [3.0.0] - 2020-09-30
**Changes**
- Rebranded back to AltaPay
- Corrected dependencies
- ResponseInfo::getRegisteredAddress() will now return null when no address was set
- Package content has been reduced in size

**Fixes**
- Errors will now be Altapay\Exceptions\ClientException instead of Guzzle exceptions
- Corrected some warning messages
- Corrected some types in code DocBlocks

## [2.1.0] - 2020-06-04
**Added**
- Klarna Payments parameters to the Create payment request, and the Capture and refund
- Parse more credit card details

**Changed**
- Removed the restriction where "taxPercent" and "taxAmount" could be used
- Set the POST method as default
- Update code style

## [2.0.0] - 2019-12-19
**Changed**
- Rebranding from AltaPay to Valitor

## [1.8.0] - 2019-08-30
**Added**
- Add merchant error message in the release reservation response

**Fixed**
- Norwegian language not always automatically converted

## [1.7.0] - 2019-07-26
**Change**
- Update the allowed languages

**Fixes**
 - Make payment requests as POST requests by default

## [1.6.3] - 2018-11-21
**Change**
- Update the allowed languages

## [1.6.2] - 2018-09-13
**Added**
- Setup subscription changes

## [1.6.1] - 2018-09-13
**Changes**
- Allow changing type on a setup subscription

## [1.6.0] - 2018-06-16
**Added**
- IsTokenized field to the Transaction class

**Fixes**
- Compatibility with PHP 7.2

## [1.5.0] - 2018-02-14
**Added**
- PaymentSource field

**Fixes**
- Handle the decline and error cases when Refund issued

## [1.4.0] - 2017-03-30
**Changes**
- Improve Exception class

## [1.3.0] - 2017-03-22
**Changes**
- Throws the error if capture fails

## [1.2.0] - 2016-12-27
**Added**
- Factory class

**Fixes**
- Handled warnings in Callback class

## [1.1.0] - 2016-07-06
**Changes**
- It is no longer necessary to provide "/merchant" to the URL constructor

## [1.0.0] - 2016-06-16
**Added**
- The first release, see the documentation for the full feature list

# Registry Handler
HandlerRegistries grouping a set of handlers which is useful if one handler dynamically provide functions to use.

## ChainHandlerRegistry
```php
use PHPSharkTank\Anonymizer\Registry\ChainHandlerRegistry;
```
Provides a matching Handler for a certain handler name.

## FakerHandlerRegistry
```php
use PHPSharkTank\Anonymizer\Registry\FakerHandlerRegistry;
```
Provides a matching Handler for a Faker provider.

## HashHandlerRegistry
```php
use PHPSharkTank\Anonymizer\Registry\HashHandlerRegistry;
```
Provides a matching handler for all hash algorithms from [hash_hmac_algos](https://www.php.net/manual/en/function.hash-hmac-algos.php)

## HandlerRegistry
```php
use PHPSharkTank\Anonymizer\Registry\HandlerRegistry;
```
Generic registry for handlers.


# Handler
Handlers define the process of how the anonymization works on a certain property

## CallbackHandler
```php
use PHPSharkTank\Anonymizer\Handler\CallbackHandler;
```
Handles the anonymization by calling a callback function.

`Name: callback`

## FakerHandler
```php
use PHPSharkTank\Anonymizer\Handler\FakerHandler;
```
Handles the anonymization by using a Faker provider. Note that not all faker providers are useful to guarantee full anonymization. A few functions need some additional parameters.

Names: (List covers all Faker Providers which are provided by the default `Faker/Factory`. Some are language specific. The Factory uses `en_US` by default)

| Provider | Function |
| --- | --- |
| Faker\Provider\Base | randomDigit, randomDigitNotNull, randomNumber, randomFloat, numberBetween, passthrough, randomLetter, randomAscii, randomElements, randomElement, randomKey, shuffle, shuffleArray, shuffleString, numerify, lexify, bothify, asciify, regexify, toLower, toUpper, optional, unique, valid |
| Faker\Provider\Uuid | uuid |
| Faker\Provider\UserAgent | macProcessor, linuxProcessor, userAgent, chrome, firefox, safari, opera, internetExplorer, windowsPlatformToken, macPlatformToken, linuxPlatformToken |
| Faker\Provider\[en_US]\Text | realText, realTextBetween |
| Faker\Provider\[en_US]\PhoneNumber | tollFreeAreaCode, tollFreePhoneNumber, phoneNumberWithExtension, areaCode, exchangeCode, phoneNumber, e164PhoneNumber, imei |
| Faker\Provider\[en_US]\Person | suffix, ssn, name, firstName, firstNameMale, firstNameFemale, lastName, title, titleMale, titleFemale |
| Faker\Provider\[en_US]\Payment | creditCardType, creditCardNumber, creditCardExpirationDate, creditCardExpirationDateString, creditCardDetails, iban, swiftBicNumber |
| Faker\Provider\Miscellaneous | boolean, md5, sha1, sha256, locale, countryCode, countryISOAlpha3, languageCode, currencyCode, emoji |
| Faker\Provider\Medical | bloodType, bloodRh, bloodGroup |
| Faker\Provider\Lorem | word, words, sentence, sentences, paragraph, paragraphs, text |
| Faker\Provider\Internet | email, safeEmail, freeEmail, companyEmail, freeEmailDomain, safeEmailDomain, userName, password, domainName, domainWord, tld, url, slug, ipv4, ipv6, localIpv4, macAddress |
| Faker\Provider\Image | imageUrl, image |
| Faker\Provider\HtmlLorem | randomHtml |
| Faker\Provider\Internet | email, safeEmail, freeEmail, companyEmail, freeEmailDomain, safeEmailDomain, userName, password, domainName, domainWord, tld, url, slug, ipv4, ipv6, localIpv4, macAddress |
| Faker\Provider\File | mimeType, fileExtension, file |
| Faker\Provider\DateTime | unixTime, dateTime, dateTimeAD, iso8601, date, time, dateTimeBetween, dateTimeInInterval, dateTimeThisCentury, dateTimeThisDecade, dateTimeThisYear, dateTimeThisMonth, amPm, dayOfMonth, dayOfWeek, month, monthName, year, century, timezon, setDefaultTimezone, getDefaultTimezone |
| Faker\Provider\[en_US]\Company | catchPhrase, bs, ein, company, companySuffix, jobTitle |
| Faker\Provider\Color | hexColor, safeHexColor, rgbColorAsArray, rgbColor, rgbCssColor, rgbaCssColor, safeColorName, colorName, hslColor, hslColorAsArray |
| Faker\Provider\Biased | biasedNumberBetween |
| Faker\Provider\Barcode | ean13, ean8, isbn10, isbn13 |
| Faker\Provider\[en_US]\Address | cityPrefix, secondaryAddress, state, stateAbbr, citySuffix, streetSuffix, buildingNumber, city, streetName, streetAddress, postcode, address, country, latitude, longitude, localCoordinates |

## HashHandler
```php
use PHPSharkTank\Anonymizer\Handler\HashHandler;
```
Handles the anonymization by using a hash algorithm.
```
Names: (not a complete list)
* md2
* md4
* md5
* sha1
* sha256
* sha512
* snefru
* haval256,5
```
For a full list have a look on `hash_hmac_algos()` - function.

## NullHandler
```php
use PHPSharkTank\Anonymizer\Handler\NullHandler;
```
`Name: 'null'`

Handles the anonymization by returning null.

# Example

```php
<?php

declare(strict_types=1);

namespace App;

require_once __DIR__.'/../vendor/autoload.php';

use Faker\Factory;
use PHPSharkTank\Anonymizer\Anonymizer;
use PHPSharkTank\Anonymizer\Handler\CallbackHandler;
use PHPSharkTank\Anonymizer\Handler\NullHandler;
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
use PHPSharkTank\Anonymizer\Loader\ChainLoader;
use PHPSharkTank\Anonymizer\Loader\YamlFileLoader;
use PHPSharkTank\Anonymizer\Registry\ChainHandlerRegistry;
use PHPSharkTank\Anonymizer\Registry\FakerHandlerRegistry;
use PHPSharkTank\Anonymizer\Registry\HandlerRegistry;
use PHPSharkTank\Anonymizer\Registry\HashHandlerRegistry;
use PHPSharkTank\Anonymizer\Visitor\GraphNavigator;
use PHPSharkTank\Anonymizer\Attribute\EnableAnonymize;
use PHPSharkTank\Anonymizer\Attribute\Handler;

#[EnableAnonymize]
class Person {
    // FakerHandler => PersonProvider
    #[Handler(value: 'firstName')]
    public string $name = '';

    // FakerHandler => Text with options
    #[Handler(value: 'realText', options: ['maxNbChars' => '30'])]
    public string $text = '';

    // NullHandler
    #[Handler(value: 'null')]
    public ?string $nullable = '';

    // HashHandler
    #[Handler(value: 'snefru')]
    public string $hash = '';

    // CallbackHandler
    #[Handler(value: 'callback', options: ['method' => 'getResult'])]
    public string $callback = '';

    public function getResult(): string
    {
        return 'result';
    }
}

$anonymizer = new Anonymizer(new GraphNavigator(
    new ChainLoader([
        new AttributeLoader(),
        new YamlFileLoader([__DIR__ . '/entities.yaml']),
    ]),
    new ChainHandlerRegistry([
        new FakerHandlerRegistry(Factory::create()),
        new HashHandlerRegistry(),
        new HandlerRegistry([
            new CallbackHandler(),
            new NullHandler(),
        ])
    ]),
));

$person = new Person();
$anonymizer->process($person);
var_dump($person);
```
Result:
```
/var/web/app » php index.php
/var/web/app/index.php.php:79:
class App\Person#38 (5) {
  public string $name =>
  string(6) "Jackie"
  public string $text =>
  string(29) "It's enough to try the thing."
  public ?string $nullable =>
  NULL
  public string $hash =>
  string(64) "8617f366566a011837f4fb4ba5bedea2b892f3ed8b894023d16ae344b2be5881"
  public string $callback =>
  string(6) "result"
}
```

# Anonymizer

[![CI](https://github.com/PHPSharkTank/Anonymizer/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/PHPSharkTank/Anonymizer/actions/workflows/ci.yml)
[![Coverage](https://codecov.io/gh/PHPSharkTank/Anonymizer/branch/main/graph/badge.svg)](https://codecov.io/gh/PHPSharkTank/Anonymizer)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![semantic-release](https://img.shields.io/badge/semantic--release-angular-e10079?logo=semantic-release)](https://github.com/semantic-release/semantic-release)

[![Latest Stable Version](https://img.shields.io/packagist/v/php-shark-tank/anonymizer)](https://packagist.org/packages/php-shark-tank/anonymizer)
[![Total Downloads](https://img.shields.io/packagist/dt/php-shark-tank/anonymizer)](https://packagist.org/packages/php-shark-tank/anonymizer)
[![PHP Version](https://img.shields.io/packagist/dependency-v/php-shark-tank/anonymizer/php)](https://packagist.org/packages/php-shark-tank/anonymizer)
[![License](https://img.shields.io/packagist/l/php-shark-tank/anonymizer)](LICENSE)

The Anonymizer is a library for PHP applications to make it
easy to modify data of any object using PHP Attributes or other
structured configurations.

# Installation
With composer run
```bash
composer require php-shark-tank/anonymizer:^1.0
```

# Documentation
* [Basics](./doc/010-basic.md)
* [Loader](./doc/020-loader.md)
* [Registry / Handler](./doc/030-registry_handler.md)
* [Events](./doc/040-events.md)
* ExclusionStrategies

# Quick Example

```php
<?php

declare(strict_types=1);

namespace App;

require_once __DIR__.'/../vendor/autoload.php';

use PHPSharkTank\Anonymizer\Anonymizer;
use PHPSharkTank\Anonymizer\Handler\CallbackHandler;
use PHPSharkTank\Anonymizer\Handler\NullHandler;
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
use PHPSharkTank\Anonymizer\Registry\HandlerRegistry;
use PHPSharkTank\Anonymizer\Visitor\GraphNavigator;
use PHPSharkTank\Anonymizer\Attribute\EnableAnonymize;
use PHPSharkTank\Anonymizer\Attribute\Handler;

#[EnableAnonymize]
class Person {
    #[Handler(value: 'callback', options: ['method' => 'getNameDefault'])]
    public string $name = '';
    
    #[Handler(value: 'null')]
    public ?string $nullable = '';

    public function getNameDefault(): string
    {
        return 'name';
    }
}

$anonymizer = new Anonymizer(new GraphNavigator(
    new AttributeLoader(),
        new HandlerRegistry([
            new CallbackHandler(),
            new NullHandler()
        ]),
));

$person = new Person();
$anonymizer->process($person);
var_dump($person);
```
Result:
```
/var/web/app » php index.php
/var/web/app/index.php.php:42:
class App\Person#10 (2) {
  public string $name =>
  string(4) "name"
  public ?string $nullable =>
  NULL
}
```

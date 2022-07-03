# Basics

To enable anonymization on an entity or other object it is necessary to add the `EnableAnonymize` Attribute

Choose an Anonymize - `Handler` for every property you want to anonymize.

Instantiate the Anonymizer and let the magic happened.

```php
<?php

declare(strict_types=1);

namespace App;

require_once __DIR__.'/../vendor/autoload.php';

use Faker\Factory;
use PHPSharkTank\Anonymizer\Anonymizer;
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
use PHPSharkTank\Anonymizer\Registry\FakerHandlerRegistry;
use PHPSharkTank\Anonymizer\Visitor\GraphNavigator;
use PHPSharkTank\Anonymizer\Attribute\EnableAnonymize;
use PHPSharkTank\Anonymizer\Attribute\Handler;

#[EnableAnonymize]
class Person {

    #[Handler(value: 'firstName')]
    public string $name = '';
}

$anonymizer = new Anonymizer(new GraphNavigator(
    new AttributeLoader(),
    new FakerHandlerRegistry(Factory::create())
));

$person = new Person();
$anonymizer->process($person);
var_dump($person);

```
Result:
```
/var/web/app » php index.php
/var/web/app/index.php.php:31:
class App\Person#31 (1) {
  public string $name =>
  string(6) "Glenna"
}
```

The Anonymizer is highly customizable with new registries/handlers, loaders or exclusion strategies. But it ofers a bunch of cool stuff build in.


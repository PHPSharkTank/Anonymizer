# Events
The Anonymizer provides several events to hook into.

## Lifetime Events
Use #[PreAnonymize] and #[PostAnonymize] to hook into the lifetime of the anonymization.

```php
<?php

declare(strict_types=1);

namespace App;

use PHPSharkTank\Anonymizer\Attribute\EnableAnonymize;
use PHPSharkTank\Anonymizer\Attribute\PostAnonymize;
use PHPSharkTank\Anonymizer\Attribute\PreAnonymize;
use PHPSharkTank\Anonymizer\Anonymizer;
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
use PHPSharkTank\Anonymizer\Registry\ChainHandlerRegistry;
use PHPSharkTank\Anonymizer\Visitor\GraphNavigator;

require_once __DIR__.'/../vendor/autoload.php';

#[EnableAnonymize]
class Person {
    public string $name = '';

    #[PreAnonymize]
    public function preAnonymize(): void {
        $this->name = 'Pre Anonymized';
    }

    #[PostAnonymize]
    public function postAnonymize(): void
    {
        $this->name = 'Post Anonymized';
    }
}

$anonymizer = new Anonymizer(new GraphNavigator(
    new AttributeLoader(),
    new ChainHandlerRegistry([]),
));

$person = new Person();
$anonymizer->process($person);
var_dump($person);
```
Result:
```
/var/web/app » php index.php
/var/web/app/index.php.php:40:
class App\Person#8 (1) {
  public string $name =>
  string(15) "Post Anonymized"
}
```

## Event Dispatcher
With the event dispatcher you can hook into the anonymization process. You can listen to PreAnonymizeEvent and PostAnonymizeEvent.

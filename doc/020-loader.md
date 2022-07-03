# Loader
Loader are necessary to fill the anonymizer with all the information about the properties which should be anonymized.

## ChainLoader
```php
use PHPSharkTank\Anonymizer\Loader\ChainLoader;
```
Merge anonymize information from multiple loaders. NOTE!: The order of the loaders is important.

## Caching Loader
```php
use PHPSharkTank\Anonymizer\Loader\CachingLoader;
```
Decorator for other loaders to fetch information from a cache which implements the `CacheItemPoolInterface` from PSR-6

## AttributeLoader
```php
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
```
Loads the attributes from the class and property attributes.

## YamlLoader
```php
use PHPSharkTank\Anonymizer\Loader\YamlFileLoader;
```
Loads information from a given yaml file.

## Example
```php
use PHPSharkTank\Anonymizer\Anonymizer;
use PHPSharkTank\Anonymizer\Visitor\GraphNavigator;
use PHPSharkTank\Anonymizer\Loader\ChainLoader;
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
use PHPSharkTank\Anonymizer\Loader\YamlFileLoader;
use PHPSharkTank\Anonymizer\Registry\FakerHandlerRegistry;
use Faker\Factory;

$anonymizer = new Anonymizer(new GraphNavigator(
    new ChainLoader([
        new AttributeLoader(),
        new YamlFileLoader([__DIR__ . '/entities.yaml']),
    ]),
    new FakerHandlerRegistry(Factory::create())
));

```

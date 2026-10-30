<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Metadata;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

class ClassMetadataInfoTest extends TestCase
{
    public function testSerializationRoundTrip(): void
    {
        $object = new class {
            public string $name = 'secret';
        };

        $propertyMetadata = new PropertyMetadata($object::class, 'name', 'faker');
        $propertyMetadata->setOptions(['locale' => 'de_DE']);
        $propertyMetadata->expr = 'value == ""';

        $metadata = new ClassMetadataInfo($object::class);
        $metadata->addPropertyMetadata($propertyMetadata);
        $metadata->preAnonymizeable = ['pre'];
        $metadata->postAnonymizeable = ['post'];
        $metadata->expr = 'false';

        $restored = unserialize(serialize($metadata));

        self::assertInstanceOf(ClassMetadataInfo::class, $restored);
        self::assertSame($object::class, $restored->className);
        self::assertSame($object::class, $restored->reflection->getName());
        self::assertSame(['pre'], $restored->preAnonymizeable);
        self::assertSame(['post'], $restored->postAnonymizeable);
        self::assertSame('false', $restored->expr);

        $restoredProperty = $restored->getPropertyMetadata()['name'];
        self::assertSame('name', $restoredProperty->getName());
        self::assertSame('faker', $restoredProperty->getHandler());
        self::assertSame(['locale' => 'de_DE'], $restoredProperty->getOptions());
        self::assertSame('value == ""', $restoredProperty->expr);

        $restoredProperty->setValue($object, 'anonymized');
        self::assertSame('anonymized', $restoredProperty->getValue($object));
    }
}

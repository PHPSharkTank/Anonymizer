<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\ExclusionStrategy;

use PHPSharkTank\Anonymizer\AnonymizableInterface;
use PHPSharkTank\Anonymizer\ExclusionStrategy\DefaultExclusionStrategy;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

class DefaultExclusionStrategyTest extends TestCase
{
    public function testShouldSkipObjectWithoutInterface(): void
    {
        $strategy = new DefaultExclusionStrategy();
        self::assertFalse($strategy->shouldSkipObject(new TestClassWithoutInterface(), new ClassMetadataInfo(TestClassWithoutInterface::class)));
    }

    public function testShouldSkipObjectWithInterfaceFalse(): void
    {
        $strategy = new DefaultExclusionStrategy();
        self::assertTrue($strategy->shouldSkipObject(new TestClassWithInterface(false), new ClassMetadataInfo(TestClassWithInterface::class)));
    }

    public function testShouldSkipObjectWithInterfaceTrue(): void
    {
        $strategy = new DefaultExclusionStrategy();
        self::assertFalse($strategy->shouldSkipObject(new TestClassWithInterface(true), new ClassMetadataInfo(TestClassWithInterface::class)));
    }

    public function testShouldSkipProperty(): void
    {
        $strategy = new DefaultExclusionStrategy();
        self::assertFalse($strategy->shouldSkipProperty(new TestClassWithoutInterface(), new PropertyMetadata(TestClassWithoutInterface::class, 'name')));
    }
}

class TestClassWithoutInterface
{
    public string $name;
}

class TestClassWithInterface implements AnonymizableInterface
{
    public function __construct(
        private readonly bool $isAnonimizable
    ) {}

    public function isAnonymizable(): bool
    {
        return $this->isAnonimizable;
    }
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\ExclusionStrategy;

use PHPSharkTank\Anonymizer\ExclusionStrategy\ChainExclusionStrategy;
use PHPSharkTank\Anonymizer\ExclusionStrategy\StrategyInterface;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ChainExclusionStrategyTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldSkipObjectFalse(): void
    {
        $exclusionStrategy = new ChainExclusionStrategy([]);
        self::assertFalse($exclusionStrategy->shouldSkipObject(new TestObject(), new ClassMetadataInfo(TestObject::class)));
    }

    public function testShouldSkipObjectFalseWithStrategy(): void
    {
        $class = new TestObject();
        $meta = new ClassMetadataInfo(get_class($class));

        $strategy = $this->prophesize(StrategyInterface::class);
        $strategy->shouldSkipObject($class, $meta)->shouldBeCalledOnce()->willReturn(false);

        $exclusionStrategy = new ChainExclusionStrategy([$strategy->reveal()]);
        self::assertFalse($exclusionStrategy->shouldSkipObject($class, $meta));
    }

    public function testShouldSkipObjectTrue(): void
    {
        $class = new TestObject();
        $meta = new ClassMetadataInfo(get_class($class));

        $strategy1 = $this->prophesize(StrategyInterface::class);
        $strategy1->shouldSkipObject($class, $meta)->shouldBeCalledOnce()->willReturn(false);

        $strategy2 = $this->prophesize(StrategyInterface::class);
        $strategy2->shouldSkipObject($class, $meta)->shouldBeCalledOnce()->willReturn(true);

        $exclusionStrategy = new ChainExclusionStrategy([$strategy1->reveal(), $strategy2->reveal()]);
        self::assertTrue($exclusionStrategy->shouldSkipObject($class, $meta));
    }

    public function testShouldSkipPropertyFalseWithStrategy(): void
    {
        $class = new TestObject();
        $meta = new PropertyMetadata(get_class($class), 'value');

        $strategy = $this->prophesize(StrategyInterface::class);
        $strategy->shouldSkipProperty($class, $meta)->shouldBeCalledOnce()->willReturn(false);

        $exclusionStrategy = new ChainExclusionStrategy([$strategy->reveal()]);
        self::assertFalse($exclusionStrategy->shouldSkipProperty($class, $meta));
    }

    public function testShouldSkipPropertyTrue(): void
    {
        $class = new TestObject();
        $meta = new PropertyMetadata(get_class($class), 'value');

        $strategy1 = $this->prophesize(StrategyInterface::class);
        $strategy1->shouldSkipProperty($class, $meta)->shouldBeCalledOnce()->willReturn(false);

        $strategy2 = $this->prophesize(StrategyInterface::class);
        $strategy2->shouldSkipProperty($class, $meta)->shouldBeCalledOnce()->willReturn(true);

        $exclusionStrategy = new ChainExclusionStrategy([$strategy1->reveal(), $strategy2->reveal()]);
        self::assertTrue($exclusionStrategy->shouldSkipProperty($class, $meta));
    }
}

class TestObject
{
    public string $value = '';
}

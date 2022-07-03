<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\ExclusionStrategy;

use PHPSharkTank\Anonymizer\Attribute\EnableAnonymize;
use PHPSharkTank\Anonymizer\Attribute\Handler;
use PHPSharkTank\Anonymizer\Attribute\Skip;
use PHPSharkTank\Anonymizer\ExclusionStrategy\ExpressionExclusionStrategy;
use PHPSharkTank\Anonymizer\Loader\AttributeLoader;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionExclusionStrategyTest extends TestCase
{
    use ProphecyTrait;

    private ExpressionLanguage|ObjectProphecy $expressionLanguage;

    protected function setUp(): void
    {
        $this->expressionLanguage = $this->prophesize(ExpressionLanguage::class);
    }

    public function testShouldSkipObjectWithoutExpression(): void
    {
        $object = new StrategyTestObjectWithoutSkip();
        $strategy = new ExpressionExclusionStrategy($this->expressionLanguage->reveal());

        self::assertFalse($strategy->shouldSkipObject($object, (new AttributeLoader())->getMetadataFor(get_class($object))));
    }

    public function testShouldSkipObjectWithExpressionFalse(): void
    {
        $object = new StrategyTestObject();
        $this->expressionLanguage->evaluate('expression', ['obj' => $object])->shouldBeCalledOnce()->willReturn(false);

        $strategy = new ExpressionExclusionStrategy($this->expressionLanguage->reveal());

        self::assertFalse($strategy->shouldSkipObject($object, (new AttributeLoader())->getMetadataFor(get_class($object))));
    }

    public function testShouldSkipObjectWithExpressionTrue(): void
    {
        $object = new StrategyTestObject();
        $this->expressionLanguage->evaluate('expression', ['obj' => $object])->shouldBeCalledOnce()->willReturn(true);

        $strategy = new ExpressionExclusionStrategy($this->expressionLanguage->reveal());

        self::assertTrue($strategy->shouldSkipObject($object, (new AttributeLoader())->getMetadataFor(get_class($object))));
    }

    public function testShouldSkipPropertyWithoutExpression(): void
    {
        $object = new StrategyTestObjectWithoutSkip();
        $strategy = new ExpressionExclusionStrategy($this->expressionLanguage->reveal());

        self::assertFalse($strategy->shouldSkipProperty($object, (new AttributeLoader())->getMetadataFor(get_class($object))->getPropertyMetadata()['lastName']));
    }

    public function testShouldSkipPropertyWithExpressionFalse(): void
    {
        $object = new StrategyTestObject();
        $this->expressionLanguage->evaluate('expression', ['obj' => $object, 'property' => 'lastName', 'value' => 'Name'])->shouldBeCalledOnce()->willReturn(false);

        $strategy = new ExpressionExclusionStrategy($this->expressionLanguage->reveal());

        self::assertFalse($strategy->shouldSkipProperty($object, (new AttributeLoader())->getMetadataFor(get_class($object))->getPropertyMetadata()['lastName']));
    }

    public function testShouldSkipPropertyWithExpressionTrue(): void
    {
        $object = new StrategyTestObject();
        $this->expressionLanguage->evaluate('expression', ['obj' => $object, 'property' => 'lastName', 'value' => 'Name'])->shouldBeCalledOnce()->willReturn(true);

        $strategy = new ExpressionExclusionStrategy($this->expressionLanguage->reveal());

        self::assertTrue($strategy->shouldSkipProperty($object, (new AttributeLoader())->getMetadataFor(get_class($object))->getPropertyMetadata()['lastName']));
    }
}

#[EnableAnonymize]
class StrategyTestObjectWithoutSkip
{
    #[Handler('callback', options: ['method' => 'removeName'])]
    public $lastName = 'Name';
}

#[EnableAnonymize]
#[Skip('expression')]
class StrategyTestObject
{
    #[Handler('callback', options: ['method' => 'removeName'])]
    public $name = 'Name';

    #[Handler('callback', options: ['method' => 'removeName'])]
    #[Skip('expression')]
    public $lastName = 'Name';

    public function removeName(): void
    {
        $this->name = null;
    }
}

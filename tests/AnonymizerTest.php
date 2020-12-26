<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests;

use PHPSharkTank\Anonymizer\Anonymizer;
use PHPSharkTank\Anonymizer\Visitor\GraphNavigatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AnonymizerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var GraphNavigatorInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $navigator;

    private Anonymizer $anonymizer;

    protected function setUp(): void
    {
        $this->navigator = $this->prophesize(GraphNavigatorInterface::class);
        $this->anonymizer = new Anonymizer($this->navigator->reveal());
    }

    public function testProcess(): void
    {
        $value = new \stdClass();

        $this->navigator->visit($value)
            ->shouldBeCalled();

        $this->anonymizer->process($value);
    }
}

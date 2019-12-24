<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Tests\Anonymizer;

use PHPSharkTank\AnonymizeBundle\Anonymizer\Anonymizer;
use PHPSharkTank\AnonymizeBundle\Visitor\GraphNavigatorInterface;
use PHPUnit\Framework\TestCase;

class AnonymizerTest extends TestCase
{
    /**
     * @var GraphNavigatorInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $navigator;
    /**
     * @var Anonymizer
     */
    private $anonymizer;

    protected function setUp(): void
    {
        $this->navigator = $this->prophesize(GraphNavigatorInterface::class);
        $this->anonymizer = new Anonymizer($this->navigator->reveal());
    }

    public function testProcess()
    {
        $value = new \stdClass();

        $this->navigator->visit($value)
            ->shouldBeCalled();

        $this->anonymizer->process($value);
    }
}

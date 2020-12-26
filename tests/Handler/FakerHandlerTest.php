<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Handler;

use Faker\Generator;
use PHPSharkTank\Anonymizer\Handler\FakerHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FakerHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Generator|\Prophecy\Prophecy\ObjectProphecy
     */
    private $generator;

    protected function setUp(): void
    {
        $this->generator = $this->prophesize(Generator::class);
    }

    public function testGetName(): void
    {
        $handler = new FakerHandler($this->generator->reveal(), 'foo');

        self::assertSame('foo', $handler->getName());
    }

    public function testProcess(): void
    {
        $handler = new FakerHandler($this->generator->reveal(), 'foo');
        $this->generator->format('foo', [])->willReturn('bar');

        self::assertSame('bar', $handler->process(new \stdClass(), []));
    }
}

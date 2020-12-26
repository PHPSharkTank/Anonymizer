<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Registry;

use Faker\Generator;
use Faker\Provider\Lorem;
use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Handler\FakerHandler;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;
use PHPSharkTank\Anonymizer\Registry\FakerHandlerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FakerHandlerRegistryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Generator|\Prophecy\Prophecy\ObjectProphecy
     */
    private $generator;

    private FakerHandlerRegistry $registry;

    protected function setUp(): void
    {
        $this->generator = $this->prophesize(Generator::class);
        $this->registry = new FakerHandlerRegistry($this->generator->reveal());
    }

    public function testRegister(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->registry->register($this->prophesize(HandlerInterface::class)->reveal());
    }

    public function testGet(): void
    {
        $this->generator->getProviders()->willReturn([new Lorem($this->generator->reveal())]);

        self::assertInstanceOf(FakerHandler::class, $this->registry->get('text'));
    }

    public function testGetInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        $this->generator->getProviders()->willReturn([]);

        self::assertInstanceOf(FakerHandler::class, $this->registry->get('invalid'));
    }

    public function testUnregister(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->registry->unregister('foo');
    }
}

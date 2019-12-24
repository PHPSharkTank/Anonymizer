<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Tests\Handler;

use PHPSharkTank\AnonymizeBundle\Registry\FakerHandlerRegistry;
use PHPSharkTank\AnonymizeBundle\Handler\FakerHandler;
use PHPSharkTank\AnonymizeBundle\Handler\HandlerInterface;
use PHPSharkTank\AnonymizeBundle\Exception\RuntimeException;
use Faker\Generator;
use Faker\Provider\Lorem;
use PHPUnit\Framework\TestCase;

class FakerHandlerRegistryTest extends TestCase
{
    /**
     * @var Generator|\Prophecy\Prophecy\ObjectProphecy
     */
    private $generator;
    /**
     * @var FakerHandlerRegistry
     */
    private $registry;

    protected function setUp(): void
    {
        $this->generator = $this->prophesize(Generator::class);
        $this->registry = new FakerHandlerRegistry($this->generator->reveal());
    }

    public function testRegister()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->registry->register($this->prophesize(HandlerInterface::class)->reveal());
    }

    public function testGet()
    {
        $this->generator->getProviders()->willReturn([new Lorem($this->generator->reveal())]);

        self::assertInstanceOf(FakerHandler::class, $this->registry->get('text'));
    }

    public function testGetInvalid()
    {
        $this->expectException(RuntimeException::class);

        $this->generator->getProviders()->willReturn([]);

        self::assertInstanceOf(FakerHandler::class, $this->registry->get('invalid'));
    }

    public function testUnregister()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->registry->unregister('foo');
    }
}
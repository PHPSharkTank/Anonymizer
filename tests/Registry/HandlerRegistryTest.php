<?php

namespace PHPSharkTank\Anonymizer\Tests\Registry;

use PHPSharkTank\Anonymizer\Exception\LogicException;
use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;
use PHPSharkTank\Anonymizer\Registry\HandlerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HandlerRegistryTest extends TestCase
{
    use ProphecyTrait;

    public function testRegister(): void
    {
        $registry = new HandlerRegistry([]);
        $handler = $this->prophesize(HandlerInterface::class);
        $handler->getName()->willReturn('name')->shouldBeCalledOnce();
        $handler = $handler->reveal();

        $registry->register($handler);
        self::assertSame($handler, $registry->get('name'));
    }

    public function testRegisterTwice(): void
    {
        self::expectException(LogicException::class);

        $registry = new HandlerRegistry([]);
        $handler = $this->prophesize(HandlerInterface::class);
        $handler->getName()->willReturn('name')->shouldBeCalledTimes(2);
        $handler = $handler->reveal();

        $registry->register($handler);
        $registry->register($handler);
    }

    public function testGetHandlerFailed(): void
    {
        self::expectException(RuntimeException::class);

        $registry = new HandlerRegistry([]);
        $registry->get('handler');
    }

    public function testCreateRegistry(): void
    {
        $handler = $this->prophesize(HandlerInterface::class);
        $handler->getName()->willReturn('name')->shouldBeCalledOnce();
        $handler = $handler->reveal();
        $registry = new HandlerRegistry([$handler]);

        self::assertSame($handler, $registry->get('name'));
    }

    public function testUnregisterFailed(): void
    {
        self::expectException(LogicException::class);

        $registry = new HandlerRegistry([]);
        $registry->unregister('handler');
    }

    public function testUnregister(): void
    {
        $handler = $this->prophesize(HandlerInterface::class);
        $handler->getName()->willReturn('handler')->shouldBeCalledOnce();
        $handler = $handler->reveal();
        $registry = new HandlerRegistry([$handler]);

        $registry->unregister('handler');

        self::expectException(LogicException::class);
        $registry->unregister('handler');
    }
}
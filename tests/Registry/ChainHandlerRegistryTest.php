<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Tests\Handler;

use PHPSharkTank\AnonymizeBundle\Handler\HandlerInterface;
use PHPSharkTank\AnonymizeBundle\Registry\ChainHandlerRegistry;
use PHPSharkTank\AnonymizeBundle\Registry\HandlerRegistryInterface;
use PHPSharkTank\AnonymizeBundle\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class ChainHandlerRegistryTest extends TestCase
{
    public function testUnregister()
    {
        $this->expectException(\BadMethodCallException::class);

        $handler = new ChainHandlerRegistry([]);
        $handler->unregister('Test');
    }

    public function testGet()
    {
        $name = 'foo';
        $handler = $this->prophesize(HandlerInterface::class);

        $registryOne = $this->prophesize(HandlerRegistryInterface::class);
        $registryTwo = $this->prophesize(HandlerRegistryInterface::class);

        $registryOne->get($name)->willThrow(new RuntimeException());
        $registryTwo->get($name)->willReturn($handler->reveal());

        $registry = new ChainHandlerRegistry([
            $registryOne->reveal(),
            $registryTwo->reveal(),
        ]);

        self::assertSame($handler->reveal(), $registry->get($name));
    }

    public function testRegister()
    {
        $this->expectException(\BadMethodCallException::class);

        $handler = new ChainHandlerRegistry([]);
        $handler->register($this->prophesize(HandlerInterface::class)->reveal());
    }
}

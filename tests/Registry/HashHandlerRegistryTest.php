<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Registry;

use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;
use PHPSharkTank\Anonymizer\Registry\HashHandlerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HashHandlerRegistryTest extends TestCase
{
    use ProphecyTrait;

    public function testRegister(): void
    {
        self::expectException(\BadMethodCallException::class);

        $hashHandlerRegistry = new HashHandlerRegistry();
        $hashHandlerRegistry->register($this->prophesize(HandlerInterface::class)->reveal());
    }

    public function testUnregister(): void
    {
        self::expectException(\BadMethodCallException::class);

        $hashHandlerRegistry = new HashHandlerRegistry();
        $hashHandlerRegistry->unregister('hanlder');
    }

    public function testGetUnknownHashAlgo(): void
    {
        self::expectException(RuntimeException::class);

        $hashHandlerRegistry = new HashHandlerRegistry();
        $hashHandlerRegistry->get('UnknownAlgorithm');
    }

    public function testGetKnownHashAlgo(): void
    {
        $hashHandlerRegistry = new HashHandlerRegistry();
        $handler = $hashHandlerRegistry->get('md5');

        self::assertSame('md5', $handler->getName());
    }
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Handler;

use PHPSharkTank\Anonymizer\Handler\HashHandler;
use PHPUnit\Framework\TestCase;

class HashHandlerTest extends TestCase
{
    public function testGetName(): void
    {
        $handler = new HashHandler('name');
        self::assertSame('name', $handler->getName());
    }

    public function testProcess(): void
    {
        $md5Handler = new HashHandler('md5');
        self::assertSame('098f6bcd4621d373cade4e832627b4f6', $md5Handler->process('', ['currentValue' => 'test']));
    }
}

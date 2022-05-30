<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Handler;

use PHPSharkTank\Anonymizer\Handler\NullHandler;
use PHPUnit\Framework\TestCase;

class NullHandlerTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertSame('null', (new NullHandler())->getName());
    }

    public function testProcess(): void
    {
        self::assertNull((new NullHandler())->process('', []));
    }
}

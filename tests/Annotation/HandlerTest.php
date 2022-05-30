<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Annotation;

use PHPSharkTank\Anonymizer\Annotation\Handler;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testConstructor(): void
    {
        $handler = new Handler();
        self::assertSame('text', $handler->value);
    }
}

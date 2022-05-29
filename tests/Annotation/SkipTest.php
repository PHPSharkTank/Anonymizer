<?php

namespace PHPSharkTank\Anonymizer\Tests\Annotation;

use PHPSharkTank\Anonymizer\Annotation\Skip;
use PHPUnit\Framework\TestCase;

class SkipTest extends TestCase
{
    public function testConstructor(): void
    {
        $handler = new Skip('value');
        self::assertSame('value', $handler->value);
    }
}
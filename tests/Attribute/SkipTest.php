<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Attribute;

use PHPSharkTank\Anonymizer\Attribute\Skip;
use PHPUnit\Framework\TestCase;

class SkipTest extends TestCase
{
    public function testConstructor(): void
    {
        $handler = new Skip('value');
        self::assertSame('value', $handler->value);
    }
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Event;

use PHPSharkTank\Anonymizer\Event\PreAnonymizeEvent;
use PHPUnit\Framework\TestCase;

class PreAnonymizeTest extends TestCase
{
    public function testTerminated(): void
    {
        $value = new \stdClass();
        $event = new PreAnonymizeEvent($value);

        self::assertFalse($event->isTerminated());
        $event->terminate();
        self::assertTrue($event->isTerminated());
    }
}

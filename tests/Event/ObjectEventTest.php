<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Event;

use PHPSharkTank\Anonymizer\Event\ObjectEvent;
use PHPUnit\Framework\TestCase;

class ObjectEventTest extends TestCase
{
    public function testGetObject(): void
    {
        $value = new \stdClass();
        $event = new ObjectEvent($value);

        self::assertSame($value, $event->getObject());
    }

    public function testStopPropagation(): void
    {
        $value = new \stdClass();
        $event = new ObjectEvent($value);

        self::assertFalse($event->isPropagationStopped());
        $event->stopPropagation();
        self::assertTrue($event->isPropagationStopped());
    }
}

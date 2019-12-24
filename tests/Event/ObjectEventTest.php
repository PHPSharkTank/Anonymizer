<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Tests\Event;

use PHPSharkTank\AnonymizeBundle\Event\ObjectEvent;
use PHPUnit\Framework\TestCase;

class ObjectEventTest extends TestCase
{
    public function testGetObject()
    {
        $value = new \stdClass();
        $event = new ObjectEvent($value);

        self::assertSame($value, $event->getObject());
    }
}

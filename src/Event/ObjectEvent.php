<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ObjectEvent extends Event
{
    private $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }
}

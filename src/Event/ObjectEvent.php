<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class ObjectEvent implements StoppableEventInterface
{
    private $object;

    private $propagationStopped = false;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}

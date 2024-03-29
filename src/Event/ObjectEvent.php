<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class ObjectEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(
        private readonly object $object,
    ) {}

    public function getObject(): object
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

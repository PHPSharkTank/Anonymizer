<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Event;

class PreAnonymizeEvent extends ObjectEvent
{
    private $terminate = false;

    public function isTerminated(): bool
    {
        return $this->terminate;
    }

    public function terminate(): void
    {
        $this->terminate = true;
    }
}

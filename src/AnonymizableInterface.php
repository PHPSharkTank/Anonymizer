<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer;

interface AnonymizableInterface
{
    public function isAnonymizable(): bool;
}

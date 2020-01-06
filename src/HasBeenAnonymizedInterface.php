<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer;

interface HasBeenAnonymizedInterface
{
    public function beenAnonymized(): void;
}

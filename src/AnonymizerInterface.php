<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer;

interface AnonymizerInterface
{
    public function process($value): void;
}

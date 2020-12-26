<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer;

interface AnonymizerInterface
{
    public function process(object $value): void;
}

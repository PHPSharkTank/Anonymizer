<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Anonymizer;

interface AnonymizerInterface
{
    public function process($value): void;
}

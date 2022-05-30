<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class Skip
{
    public function __construct(
        public string $value,
    ) {}
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class Skip
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Type
{
    public string $value;
    public array $options;

    public function __construct(string $value = 'text', array $options = [])
    {
        $this->value = $value;
        $this->options = $options;
    }
}

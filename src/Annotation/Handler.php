<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Handler
{
    public function __construct(
        public string $value = 'text',
        public array $options = [],
    ) {}
}

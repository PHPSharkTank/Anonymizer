<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Handler
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public string $value = 'text',
        public array $options = [],
    ) {}
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

interface HandlerInterface
{
    public function getName(): string;

    /**
     * @param array<string, mixed> $options
     */
    public function process(mixed $value, array $options): mixed;
}

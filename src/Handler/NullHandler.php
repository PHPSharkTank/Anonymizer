<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

class NullHandler implements HandlerInterface
{
    public function getName(): string
    {
        return 'null';
    }

    /**
     * @param array<string, mixed> $options
     */
    public function process(mixed $value, array $options): mixed
    {
        return null;
    }
}

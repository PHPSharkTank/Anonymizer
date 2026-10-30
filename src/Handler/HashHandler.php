<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

use PHPSharkTank\Anonymizer\Exception\LogicException;

class HashHandler implements HandlerInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function process(mixed $value, array $options): string
    {
        $currentValue = $options['currentValue'] ?? null;

        if (null !== $currentValue && !\is_scalar($currentValue) && !$currentValue instanceof \Stringable) {
            throw new LogicException(\sprintf('The %s handler can only hash scalar values, %s given.', $this->name, get_debug_type($currentValue)));
        }

        return hash($this->name, (string) $currentValue);
    }
}

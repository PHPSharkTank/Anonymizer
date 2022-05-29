<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

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

    public function process(mixed $value, array $options): string
    {
        return hash($this->name, $options['currentValue']);
    }
}

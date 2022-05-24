<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

use Faker\Generator;

class FakerHandler implements HandlerInterface
{
    private Generator $generator;

    private string $name;

    public function __construct(Generator $generator, string $name)
    {
        $this->generator = $generator;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(object $object, array $options): mixed
    {
        unset($options['currentValue']);
        return $this->generator->format($this->name, $options);
    }
}

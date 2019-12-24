<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Handler;

use Faker\Generator;

class FakerHandler implements HandlerInterface
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var string
     */
    private $name;

    public function __construct(Generator $generator, string $name)
    {
        $this->generator = $generator;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process($object, array $options)
    {
        return $this->generator->format($this->name);
    }
}

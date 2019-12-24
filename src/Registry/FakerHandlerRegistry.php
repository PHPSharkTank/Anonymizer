<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Registry;

use PHPSharkTank\AnonymizeBundle\Handler\FakerHandler;
use PHPSharkTank\AnonymizeBundle\Handler\HandlerInterface;
use PHPSharkTank\AnonymizeBundle\Exception\RuntimeException;
use Faker\Generator;

class FakerHandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function get(string $name): HandlerInterface
    {
        $fakerHandler = new FakerHandler($this->generator, $name);

        foreach ($this->generator->getProviders() as $provider) {
            if (method_exists($provider, $name)) {
                return $fakerHandler;
            }
        }

        throw new RuntimeException(sprintf('Handler %s was not registered before.', $name));
    }

    public function register(HandlerInterface $handler): void
    {
        throw new \BadMethodCallException('Not implemented!');
    }

    public function unregister(string $name): void
    {
        throw new \BadMethodCallException('Not implemented!');
    }
}

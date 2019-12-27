<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Registry;

use Faker\Generator;
use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Handler\FakerHandler;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;

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

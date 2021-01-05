<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Registry;

use function iter\toArray;
use PHPSharkTank\Anonymizer\Exception\HandlerNotFoundException;
use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;

class ChainHandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var HandlerRegistryInterface[]
     */
    private array $registries;

    public function __construct(iterable $registries)
    {
        $this->registries = toArray($registries);
    }

    public function get(string $name): HandlerInterface
    {
        foreach ($this->registries as $registry) {
            try {
                return $registry->get($name);
            } catch (RuntimeException $e) {
                continue;
            }
        }

        throw new HandlerNotFoundException(sprintf('No matching handler found for %s in registry', $name));
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

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Registry;

use PHPSharkTank\Anonymizer\Exception\RegistryNotFoundException;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;
use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use function iter\toArray;

class ChainHandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var HandlerRegistryInterface[]
     */
    private $registries;

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

        throw new RegistryNotFoundException(sprintf('No matching registry found for %s', $name));
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

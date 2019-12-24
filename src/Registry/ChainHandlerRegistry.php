<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Registry;

use PHPSharkTank\AnonymizeBundle\Handler\HandlerInterface;
use PHPSharkTank\AnonymizeBundle\Exception\RuntimeException;
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

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Registry;

use PHPSharkTank\Anonymizer\Handler\HandlerInterface;
use PHPSharkTank\Anonymizer\Exception\LogicException;
use PHPSharkTank\Anonymizer\Exception\RuntimeException;

final class HandlerRegistry implements HandlerRegistryInterface
{
    /** @var array<string, HandlerInterface> */
    private $handlers = [];

    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $this->register($handler);
        }
    }

    public function get(string $name): HandlerInterface
    {
        if (false === array_key_exists($name, $this->handlers)) {
            throw new RuntimeException(sprintf('Handler %s was not registered before.', $name));
        }

        return $this->handlers[$name];
    }

    public function register(HandlerInterface $handler): void
    {
        $name = $handler->getName();

        if (array_key_exists($name, $this->handlers)) {
            throw new LogicException(sprintf('%s already taken as identifier', $name));
        }

        $this->handlers[$name] = $handler;
    }

    public function unregister(string $name): void
    {
        if (false === array_key_exists($name, $this->handlers)) {
            throw new LogicException(sprintf('Handler %s was not registered before.', $name));
        }

        unset($this->handlers[$name]);
    }
}

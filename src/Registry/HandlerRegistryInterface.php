<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Registry;

use PHPSharkTank\AnonymizeBundle\Handler\HandlerInterface;

interface HandlerRegistryInterface
{
    public function get(string $name): HandlerInterface;

    public function register(HandlerInterface $handler): void;

    public function unregister(string $name): void;
}

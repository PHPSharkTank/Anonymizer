<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Registry;

use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Handler\HashHandler;
use PHPSharkTank\Anonymizer\Handler\HandlerInterface;

class HashHandlerRegistry implements HandlerRegistryInterface
{
    public function get(string $name): HandlerInterface
    {
        $hashHandler = new HashHandler($name);

        if (in_array(needle: $name, haystack: hash_hmac_algos())) {
            return $hashHandler;
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

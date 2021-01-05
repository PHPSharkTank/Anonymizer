<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

class NullHandler implements HandlerInterface
{
    public function getName(): string
    {
        return 'null';
    }

    public function process(object $object, array $options): mixed
    {
        return null;
    }
}

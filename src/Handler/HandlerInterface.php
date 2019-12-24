<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Handler;

interface HandlerInterface
{
    public function getName(): string;

    public function process($object, array $options);
}

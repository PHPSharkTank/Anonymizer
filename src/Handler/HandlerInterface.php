<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

interface HandlerInterface
{
    public function getName(): string;

    public function process($object, array $options);
}

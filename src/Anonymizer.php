<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer;

use PHPSharkTank\Anonymizer\Visitor\GraphNavigatorInterface;

final class Anonymizer implements AnonymizerInterface
{
    public function __construct(
        private readonly GraphNavigatorInterface $graphNavigator,
    ) {}

    public function process(object $value): void
    {
        $this->graphNavigator->visit($value);
    }
}

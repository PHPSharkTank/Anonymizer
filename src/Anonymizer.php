<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer;

use PHPSharkTank\Anonymizer\Visitor\GraphNavigatorInterface;

final class Anonymizer implements AnonymizerInterface
{
    /**
     * @var GraphNavigatorInterface
     */
    private $graphNavigator;

    public function __construct(GraphNavigatorInterface $graphNavigator)
    {
        $this->graphNavigator = $graphNavigator;
    }

    public function process(object $value): void
    {
        $this->graphNavigator->visit($value);
    }
}

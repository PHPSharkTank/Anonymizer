<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Visitor;

/**
 * This class implements the visitor pattern and
 * is responsible for the iteration over the object graph.
 * It will recursively iterate over the all values.
 *
 * Please keep in mind, that currently no handling against
 * circular reference issues exists!
 */
interface GraphNavigatorInterface
{
    /**
     * Starts visit the value.
     *
     * @param mixed $value
     */
    public function visit($value): void;
}

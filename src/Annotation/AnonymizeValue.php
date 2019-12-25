<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation\Target("PROPERTY")
 */
class AnonymizeValue extends Annotation
{
    public $type = 'text';

    public $options = [];
}

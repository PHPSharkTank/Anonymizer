<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation\Target("PROPERTY")
 */
class Type extends Annotation
{
    public $value = 'text';

    public $options = [];
}

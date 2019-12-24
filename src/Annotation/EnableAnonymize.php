<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation\Target("CLASS")
 */
class EnableAnonymize extends Annotation
{
}

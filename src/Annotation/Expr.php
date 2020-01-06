<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation()
 * @Annotation\Target({"CLASS", "PROPERTY"})
 */
class Expr extends Annotation
{
}

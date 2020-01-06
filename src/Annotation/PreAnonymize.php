<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation\Target("METHOD")
 */
class PreAnonymize extends Annotation
{
}

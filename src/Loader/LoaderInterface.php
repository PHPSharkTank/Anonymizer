<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Loader;

use PHPSharkTank\AnonymizeBundle\Metadata\ClassMetadataInfo;

interface LoaderInterface
{
    public function getMetadataFor(string $className): ClassMetadataInfo;
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;

interface LoaderInterface
{
    public function getMetadataFor(string $className): ClassMetadataInfo;
}

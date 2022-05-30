<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;

interface LoaderInterface
{
    /**
     * @throws MetadataNotFoundException
     */
    public function getMetadataFor(string $className): ClassMetadataInfo;
}

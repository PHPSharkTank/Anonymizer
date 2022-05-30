<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\ExclusionStrategy;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;

interface StrategyInterface
{
    public function shouldSkipObject(object $object, ClassMetadataInfo $metadataInfo): bool;

    public function shouldSkipProperty(object $object, PropertyMetadata $metadata): bool;
}

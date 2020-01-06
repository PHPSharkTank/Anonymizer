<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\ExclusionStrategy;

use PHPSharkTank\Anonymizer\AnonymizableInterface;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;

final class DefaultExclusionStrategy implements StrategyInterface
{
    public function shouldSkipObject($object, ClassMetadataInfo $metadataInfo): bool
    {
        if (!$object instanceof AnonymizableInterface) {
            return false;
        }


        return false === $object->isAnonymizable();
    }

    public function shouldSkipProperty($object, PropertyMetadata $metadata): bool
    {
        return false;
    }
}

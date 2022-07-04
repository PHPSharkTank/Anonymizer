<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\ExclusionStrategy;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;

final class ChainExclusionStrategy implements StrategyInterface
{
    /**
     * @var StrategyInterface[]
     */
    private array $strategies = [];

    public function __construct(iterable $strategies = [])
    {
        foreach ($strategies as $strategy) {
            $this->add($strategy);
        }
    }

    public function add(StrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    public function shouldSkipObject(object $object, ClassMetadataInfo $metadataInfo): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->shouldSkipObject(...func_get_args())) {
                return true;
            }
        }

        return false;
    }

    public function shouldSkipProperty(object $object, PropertyMetadata $metadata): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->shouldSkipProperty(...func_get_args())) {
                return true;
            }
        }

        return false;
    }
}

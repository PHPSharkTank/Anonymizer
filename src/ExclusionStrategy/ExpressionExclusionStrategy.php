<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\ExclusionStrategy;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionExclusionStrategy implements StrategyInterface
{
    private ExpressionLanguage $expression;

    public function __construct(ExpressionLanguage $expression)
    {
        $this->expression = $expression;
    }

    public function shouldSkipObject(object $object, ClassMetadataInfo $metadataInfo): bool
    {
        if (0 === strlen($metadataInfo->expr)) {
            return false;
        }

        return (bool) $this->expression->evaluate($metadataInfo->expr, ['obj' => $object]);
    }

    public function shouldSkipProperty(object $object, PropertyMetadata $metadata): bool
    {
        if (0 === strlen($metadata->expr)) {
            return false;
        }

        return (bool) $this->expression->evaluate($metadata->expr, [
            'obj' => $object,
            'property' => $metadata->getName(),
            'value' => $metadata->getValue($object),
        ]);
    }
}

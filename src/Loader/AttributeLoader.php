<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Attribute\EnableAnonymize;
use PHPSharkTank\Anonymizer\Attribute\Handler;
use PHPSharkTank\Anonymizer\Attribute\PostAnonymize;
use PHPSharkTank\Anonymizer\Attribute\PreAnonymize;
use PHPSharkTank\Anonymizer\Attribute\Skip;
use PHPSharkTank\Anonymizer\Exception\LogicException;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;

final class AttributeLoader implements LoaderInterface
{
    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $metadata = new ClassMetadataInfo($className);
        $reflectionClass = $metadata->reflection;

        $attributes = $reflectionClass->getAttributes(EnableAnonymize::class, \ReflectionAttribute::IS_INSTANCEOF);

        if (0 === count($attributes)) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        $exprAttributes = $reflectionClass->getAttributes(Skip::class, \ReflectionAttribute::IS_INSTANCEOF);

        if (count($exprAttributes)) {
            /** @var Skip $skip */
            $skip = $exprAttributes[0]->newInstance();
            $metadata->expr = $skip->value;
        }

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyAttributes = $property->getAttributes(Handler::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (0 === count($propertyAttributes)) {
                continue;
            }

            /** @var Handler $handler */
            $handler = $propertyAttributes[0]->newInstance();
            $propertyMetadata = new PropertyMetadata($className, $property->getName(), $handler->value);
            $propertyMetadata->setOptions($handler->options);
            $metadata->addPropertyMetadata($propertyMetadata);

            $exprAttributes = $property->getAttributes(Skip::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (count($exprAttributes)) {
                /** @var Skip $skip */
                $skip = $exprAttributes[0]->newInstance();
                $propertyMetadata->expr = $skip->value;
            }
        }

        foreach ($reflectionClass->getMethods() as $method) {
            $preMethodAttributes = $method->getAttributes(PreAnonymize::class, \ReflectionAttribute::IS_INSTANCEOF);
            $postMethodAttributes = $method->getAttributes(PostAnonymize::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (count($preMethodAttributes)) {
                if ($method->isPublic()) {
                    $metadata->preAnonymizeable[] = $method->getName();
                } else {
                    throw new LogicException(sprintf('You can\'t define a #[PreAnonymize] attribute on a non public method.'));
                }
            }

            if (count($postMethodAttributes)) {
                if ($method->isPublic()) {
                    $metadata->postAnonymizeable[] = $method->getName();
                } else {
                    throw new LogicException(sprintf('You can\'t define a #[PostAnonymize] attribute on a non public method.'));
                }
            }
        }

        return $metadata;
    }
}

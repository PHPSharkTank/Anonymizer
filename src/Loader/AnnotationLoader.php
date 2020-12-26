<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Annotation\EnableAnonymize;
use PHPSharkTank\Anonymizer\Annotation\PostAnonymize;
use PHPSharkTank\Anonymizer\Annotation\PreAnonymize;
use PHPSharkTank\Anonymizer\Annotation\Skip;
use PHPSharkTank\Anonymizer\Annotation\Type;
use PHPSharkTank\Anonymizer\Exception\LogicException;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;

final class AnnotationLoader implements LoaderInterface
{
    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $metadata = new ClassMetadataInfo($className);
        $reflectionClass = $metadata->reflection;

        $annotation = $reflectionClass->getAttributes(EnableAnonymize::class, \ReflectionAttribute::IS_INSTANCEOF);

        if (0 === count($annotation)) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        $exprAnnotation = $reflectionClass->getAttributes(Skip::class, \ReflectionAttribute::IS_INSTANCEOF);

        if (count($exprAnnotation)) {
            /** @var Skip $skip */
            $skip = $exprAnnotation[0]->newInstance();
            $metadata->expr = $skip->value;
        }

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyAnnotation = $property->getAttributes(Type::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (0 === count($propertyAnnotation)) {
                continue;
            }

            /** @var Type $type */
            $type = $propertyAnnotation[0]->newInstance();
            $propertyMetadata = new PropertyMetadata($className, $property->getName(), $type->value);
            $propertyMetadata->setOptions($type->options);
            $metadata->addPropertyMetadata($propertyMetadata);

            $exprAnnotation = $property->getAttributes(Skip::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (count($exprAnnotation)) {
                /** @var Skip $skip */
                $skip = $exprAnnotation[0]->newInstance();
                $propertyMetadata->expr = $skip->value;
            }
        }

        foreach ($reflectionClass->getMethods() as $method) {
            $preMethodAnnotation = $method->getAttributes(PreAnonymize::class, \ReflectionAttribute::IS_INSTANCEOF);
            $postMethodAnnotation = $method->getAttributes(PostAnonymize::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (count($preMethodAnnotation)) {
                if ($method->isPublic()) {
                    $metadata->preAnonymizeable[] = $method->getName();
                } else {
                    throw new LogicException(sprintf('You can\'t define a #[PreAnonymize] annotation on a non public method.'));
                }
            }

            if (count($postMethodAnnotation)) {
                if ($method->isPublic()) {
                    $metadata->postAnonymizeable[] = $method->getName();
                } else {
                    throw new LogicException(sprintf('You can\'t define a #[PostAnonymize] annotation on a non public method.'));
                }
            }
        }

        return $metadata;
    }
}

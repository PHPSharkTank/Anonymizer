<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use Doctrine\Common\Annotations\Reader;
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
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $metadata = new ClassMetadataInfo($className);

        /** @var EnableAnonymize|null $annotation */
        $annotation = $this->reader->getClassAnnotation($metadata->reflection, EnableAnonymize::class);

        if ($annotation) {
            $metadata->enabled = true;
        }

        $exprAnnotation = $this->reader->getClassAnnotation($metadata->reflection, Skip::class);
        if ($exprAnnotation instanceof Skip) {
            $metadata->expr = $exprAnnotation->value;
        }

        foreach ($metadata->reflection->getProperties() as $property) {
            $propertyAnnotation = $this->reader->getPropertyAnnotation($property, Type::class);

            if (!$propertyAnnotation instanceof Type) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($className, $property->getName(), $propertyAnnotation->value);
            $propertyMetadata->setOptions($propertyAnnotation->options);
            $metadata->addPropertyMetadata($propertyMetadata);

            $exprAnnotation = $this->reader->getPropertyAnnotation($property, Skip::class);
            if ($exprAnnotation instanceof Skip) {
                $propertyMetadata->expr = $exprAnnotation->value;
            }
        }

        foreach ($metadata->reflection->getMethods() as $method) {
            if ($preMethodAnnotation = $this->reader->getMethodAnnotation($method, PreAnonymize::class)) {
                if (!$method->isPublic()) {
                    throw new LogicException(sprintf('You can\'t define a @PreAnonymize annotation on a non public method.'));
                }

                $metadata->preAnonymizeable[] = $method->getName();
            }
            if ($preMethodAnnotation = $this->reader->getMethodAnnotation($method, PostAnonymize::class)) {
                if (!$method->isPublic()) {
                    throw new LogicException(sprintf('You can\'t define a @PostAnonymize annotation on a non public method.'));
                }

                $metadata->postAnonymizeable[] = $method->getName();
            }
        }

        return $metadata;
    }
}

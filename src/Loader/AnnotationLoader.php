<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Loader;

use PHPSharkTank\AnonymizeBundle\Annotation\AnonymizeValue;
use PHPSharkTank\AnonymizeBundle\Annotation\EnableAnonymize;
use PHPSharkTank\AnonymizeBundle\Metadata\ClassMetadataInfo;
use PHPSharkTank\AnonymizeBundle\Metadata\PropertyMetadata;
use PHPSharkTank\AnonymizeBundle\Exception\MetadataNotFoundException;
use Doctrine\Common\Annotations\Reader;

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

        $annotation = $this->reader->getClassAnnotation($metadata->reflection, EnableAnonymize::class);

        if (null === $annotation) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        foreach ($metadata->reflection->getProperties() as $property) {
            $propertyAnnotation = $this->reader->getPropertyAnnotation($property, AnonymizeValue::class);
            if (!$propertyAnnotation instanceof AnonymizeValue) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($className, $property->getName(), $propertyAnnotation->type);
            $propertyMetadata->setOptions($propertyAnnotation->options);
            $metadata->addPropertyMetadata($propertyMetadata);
        }

        return $metadata;
    }
}

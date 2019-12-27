<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use Doctrine\Common\Annotations\Reader;
use PHPSharkTank\Anonymizer\Annotation\AnonymizeValue;
use PHPSharkTank\Anonymizer\Annotation\EnableAnonymize;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\MethodMetadata;
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

        foreach ($metadata->reflection->getMethods() as $method) {
            $methodAnnotations = $this->reader->getMethodAnnotations($method);
            foreach ($methodAnnotations as $methodAnnotation) {
                $methodMetadata = new MethodMetadata($className, $method->getName(), get_class($methodAnnotation));
                $metadata->addMethodMetadata($methodMetadata);
            }
        }

        return $metadata;
    }
}

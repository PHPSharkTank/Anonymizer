<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use Doctrine\Common\Annotations\Reader;
use PHPSharkTank\Anonymizer\Annotation\Expr;
use PHPSharkTank\Anonymizer\Annotation\Type;
use PHPSharkTank\Anonymizer\Annotation\EnableAnonymize;
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

        /** @var EnableAnonymize $annotation */
        $annotation = $this->reader->getClassAnnotation($metadata->reflection, EnableAnonymize::class);

        if (null === $annotation) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        if ($exprAnnotation = $this->reader->getClassAnnotation($metadata->reflection, Expr::class)) {
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

            if ($exprAnnotation = $this->reader->getPropertyAnnotation($property, Expr::class)) {
                $metadata->expr = $exprAnnotation->value;
            }
        }

        return $metadata;
    }
}

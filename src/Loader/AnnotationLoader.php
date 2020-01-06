<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use Doctrine\Common\Annotations\Reader;
use PHPSharkTank\Anonymizer\Annotation\EnableAnonymize;
use PHPSharkTank\Anonymizer\Annotation\Expr;
use PHPSharkTank\Anonymizer\Annotation\PostAnonymize;
use PHPSharkTank\Anonymizer\Annotation\PreAnonymize;
use PHPSharkTank\Anonymizer\Annotation\Type;
use PHPSharkTank\Anonymizer\Exception\LogicException;
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

        /** @var EnableAnonymize|null $annotation */
        $annotation = $this->reader->getClassAnnotation($metadata->reflection, EnableAnonymize::class);

        if (null === $annotation) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        $exprAnnotation = $this->reader->getClassAnnotation($metadata->reflection, Expr::class);
        if ($exprAnnotation instanceof Expr) {
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

            $exprAnnotation = $this->reader->getPropertyAnnotation($property, Expr::class);
            if ($exprAnnotation instanceof Expr) {
                $metadata->expr = $exprAnnotation->value;
            }
        }

        foreach ($metadata->reflection->getMethods() as $method) {
            if ($methodAnnotation = $this->reader->getMethodAnnotation($method, Type::class)) {
                $methodMetadata = new MethodMetadata($className, $method->getName());
                $metadata->addMethodMetadata($methodMetadata);
            }

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

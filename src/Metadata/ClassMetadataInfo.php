<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

use PHPSharkTank\Anonymizer\Exception\LogicException;

class ClassMetadataInfo
{
    /**
     * @var class-string
     */
    public string $className;

    /**
     * @var array<string, PropertyMetadata>
     */
    public array $propertyMetadata = [];

    /**
     * @var string[]
     */
    public array $preAnonymizeable = [];

    /**
     * @var string[]
     */
    public array $postAnonymizeable = [];

    public \ReflectionClass $reflection;

    public string $expr = '';

    public function __construct(string $className)
    {
        if (!class_exists($className)) {
            throw new LogicException(sprintf('%s is not a vaid class', $className));
        }

        $this->className = $className;

        $this->reflection = new \ReflectionClass($this->className);
    }

    public function addPropertyMetadata(PropertyMetadata $metadata): void
    {
        $this->propertyMetadata[$metadata->getName()] = $metadata;
    }

    public function getPropertyMetadata(): array
    {
        return $this->propertyMetadata;
    }

    public function merge(ClassMetadataInfo $metadataInfo): void
    {
        foreach ($metadataInfo->propertyMetadata as $name => $metadata) {
            $this->propertyMetadata[$name] = $metadata;
        }
        $this->preAnonymizeable = array_merge($this->preAnonymizeable, $metadataInfo->preAnonymizeable);
        $this->postAnonymizeable = array_merge($this->postAnonymizeable, $metadataInfo->postAnonymizeable);

        if ('' !== $metadataInfo->expr) {
            $this->expr = $metadataInfo->expr;
        }
    }

    public function __sleep(): array
    {
        return [
            'className',
            'propertyMetadata',
            'expr',
            'preAnonymizeable',
            'postAnonymizeable',
        ];
    }

    public function __wakeup(): void
    {
        $this->reflection = new \ReflectionClass($this->className);
    }
}

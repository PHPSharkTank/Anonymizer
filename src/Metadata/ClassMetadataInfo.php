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
     * @var array<string>
     */
    public array $preAnonymizeable = [];

    /**
     * @var array<string>
     */
    public array $postAnonymizeable = [];

    /**
     * @var \ReflectionClass<object>
     */
    public \ReflectionClass $reflection;

    public string $expr = '';

    public function __construct(string $className)
    {
        if (!class_exists($className)) {
            throw new LogicException(\sprintf('%s is not a vaid class', $className));
        }

        $this->className = $className;

        $this->reflection = new \ReflectionClass($this->className);
    }

    public function addPropertyMetadata(PropertyMetadata $metadata): void
    {
        $this->propertyMetadata[$metadata->getName()] = $metadata;
    }

    /**
     * @return array<string, PropertyMetadata>
     */
    public function getPropertyMetadata(): array
    {
        return $this->propertyMetadata;
    }

    public function merge(self $metadataInfo): void
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

    /**
     * @return array{className: class-string, propertyMetadata: array<string, PropertyMetadata>, expr: string, preAnonymizeable: array<string>, postAnonymizeable: array<string>}
     */
    public function __serialize(): array
    {
        return [
            'className' => $this->className,
            'propertyMetadata' => $this->propertyMetadata,
            'expr' => $this->expr,
            'preAnonymizeable' => $this->preAnonymizeable,
            'postAnonymizeable' => $this->postAnonymizeable,
        ];
    }

    /**
     * @param array{className: class-string, propertyMetadata: array<string, PropertyMetadata>, expr: string, preAnonymizeable: array<string>, postAnonymizeable: array<string>} $data
     */
    public function __unserialize(array $data): void
    {
        $this->className = $data['className'];
        $this->propertyMetadata = $data['propertyMetadata'];
        $this->expr = $data['expr'];
        $this->preAnonymizeable = $data['preAnonymizeable'];
        $this->postAnonymizeable = $data['postAnonymizeable'];

        $this->reflection = new \ReflectionClass($this->className);
    }
}

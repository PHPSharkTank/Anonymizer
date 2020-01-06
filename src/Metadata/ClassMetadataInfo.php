<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

class ClassMetadataInfo
{
    /** @var string */
    public $className;

    /** @var array<string, PropertyMetadata> */
    public $propertyMetadata = [];

    /** @var array<MethodMetadata> */
    public $methodMetadata = [];

    /**
     * @var string[]
     */
    public $preAnonymizeable = [];

    /**
     * @var string[]
     */
    public $postAnonymizeable = [];

    public $reflection;

    public function __construct(string $className)
    {
        $this->className = $className;

        $this->reflection = new \ReflectionClass($this->className);
    }

    public function addPropertyMetadata(PropertyMetadata $metadata): void
    {
        $this->propertyMetadata[$metadata->getName()] = $metadata;
    }

    public function addMethodMetadata(MethodMetadata $metadata): void
    {
        $this->methodMetadata[] = $metadata;
    }

    public function getPropertyMetadata(): array
    {
        return $this->propertyMetadata;
    }

    public function getMethodMetadata(): array
    {
        return $this->methodMetadata;
    }

    public function __sleep()
    {
        return [
            'className',
            'propertyMetadata',
            'methodMetadata',
        ];
    }

    public function __wakeup()
    {
        $this->reflection = new \ReflectionClass($this->className);
    }
}

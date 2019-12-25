<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

class ClassMetadataInfo
{
    public $className;

    /** @var array<string, PropertyMetadata> */
    public $propertyMetadata = [];

    public $reflection;

    public function __construct($className)
    {
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

    public function __sleep()
    {
        return [
            'className',
            'propertyMetadata',
        ];
    }

    public function __wakeup()
    {
        $this->reflection = new \ReflectionClass($this->className);
    }
}

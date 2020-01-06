<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

class ClassMetadataInfo
{
    /**
     * @var string
     */
    public $className;

    /**
     * @var array<string, PropertyMetadata>
     */
    public $propertyMetadata = [];

    /**
     * @var string[]
     */
    public $preAnonymizeable = [];

    /**
     * @var string[]
     */
    public $postAnonymizeable = [];

    /**
     * @var \ReflectionClass
     */
    public $reflection;

    /**
     * @var string
     */
    public $expr = '';

    public function __construct(string $className)
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
            'expr',
        ];
    }

    public function __wakeup()
    {
        $this->reflection = new \ReflectionClass($this->className);
    }
}

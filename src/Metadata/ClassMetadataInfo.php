<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

use PHPSharkTank\Anonymizer\Exception\LogicException;

class ClassMetadataInfo
{
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

    /**
     * @var bool
     */
    public $enabled = false;

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

    public function __sleep(): array
    {
        return [
            'className',
            'propertyMetadata',
            'expr',
            'preAnonymizeable',
            'postAnonymizeable',
            'enabled',
        ];
    }

    public function __wakeup(): void
    {
        $this->reflection = new \ReflectionClass($this->className);
    }
}

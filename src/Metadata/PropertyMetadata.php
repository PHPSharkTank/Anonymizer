<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

class PropertyMetadata
{
    private $reflection;

    private $name;

    private $className;

    private $type;

    private $options = [];

    public function __construct(string $class, string $propertyName, string $type = 'text')
    {
        $this->className = $class;
        $this->name = $propertyName;
        $this->type = $type;

        $this->reflection = new \ReflectionProperty($this->className, $propertyName);
        $this->reflection->setAccessible(true);
    }

    public function setValue($object, $value): void
    {
        $this->reflection->setValue($object, $value);
    }

    public function getValue($object)
    {
        return $this->reflection->getValue($object);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function __sleep()
    {
        return [
            'name',
            'className',
            'type',
            'options',
        ];
    }

    public function __wakeup()
    {
        $this->reflection = new \ReflectionProperty($this->className, $this->name);
        $this->reflection->setAccessible(true);
    }
}

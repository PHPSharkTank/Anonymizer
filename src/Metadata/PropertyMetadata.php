<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

/**
 * @internal
 */
class PropertyMetadata
{
    private \ReflectionProperty $reflection;

    private string $name;

    private string $className;

    private string $type;

    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    public string $expr = '';

    public function __construct(string $class, string $propertyName, string $type = 'text')
    {
        $this->className = $class;
        $this->name = $propertyName;
        $this->type = $type;

        $this->reflection = new \ReflectionProperty($this->className, $propertyName);
        $this->reflection->setAccessible(true);
    }

    public function setValue(object $object, mixed $value): void
    {
        $this->reflection->setValue($object, $value);
    }

    public function getValue(object $object): mixed
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

    public function __sleep(): array
    {
        return [
            'name',
            'className',
            'type',
            'options',
            'expr',
        ];
    }

    public function __wakeup(): void
    {
        $this->reflection = new \ReflectionProperty($this->className, $this->name);
        $this->reflection->setAccessible(true);
    }
}

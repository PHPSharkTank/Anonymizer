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

    private string $handler;

    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    public string $expr = '';

    public function __construct(string $class, string $propertyName, string $handler = 'text')
    {
        $this->className = $class;
        $this->name = $propertyName;
        $this->handler = $handler;

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

    public function getHandler(): string
    {
        return $this->handler;
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
            'handler',
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

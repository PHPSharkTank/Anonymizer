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

    /**
     * @var class-string
     */
    private string $className;

    private string $handler;

    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    public string $expr = '';

    /**
     * @param class-string $class
     */
    public function __construct(string $class, string $propertyName, string $handler = 'text')
    {
        $this->className = $class;
        $this->name = $propertyName;
        $this->handler = $handler;

        $this->reflection = new \ReflectionProperty($this->className, $propertyName);
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

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array{name: string, className: class-string, handler: string, options: array<string, mixed>, expr: string}
     */
    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'className' => $this->className,
            'handler' => $this->handler,
            'options' => $this->options,
            'expr' => $this->expr,
        ];
    }

    /**
     * @param array{name: string, className: class-string, handler: string, options: array<string, mixed>, expr: string} $data
     */
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->className = $data['className'];
        $this->handler = $data['handler'];
        $this->options = $data['options'];
        $this->expr = $data['expr'];

        $this->reflection = new \ReflectionProperty($this->className, $this->name);
    }
}

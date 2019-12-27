<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

class MethodMetadata
{
    private $reflection;

    private $name;

    private $className;

    private $annotationClass;

    public function __construct(string $class, string $methodName, string $annotationClass)
    {
        $this->className = $class;
        $this->name = $methodName;
        $this->annotationClass = $annotationClass;

        $this->reflection = new \ReflectionMethod($this->className, $methodName);
        $this->reflection->setAccessible(true);
    }

    public function invoke($object): void
    {
        $this->reflection->invoke($object);
    }

    public function getAnnotatinoClass(): string
    {
        return $this->annotationClass;
    }

    public function __sleep()
    {
        return [
            'name',
            'className',
            'annotationClass',
        ];
    }

    public function __wakeup()
    {
        $this->reflection = new \ReflectionMethod($this->className, $this->name);
        $this->reflection->setAccessible(true);
    }
}

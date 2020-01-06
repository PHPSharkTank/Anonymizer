<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Metadata;

class MethodMetadata
{
    private $reflection;

    private $name;

    private $className;

    public function __construct(string $class, string $methodName)
    {
        $this->className = $class;
        $this->name = $methodName;

        $this->reflection = new \ReflectionMethod($this->className, $methodName);
    }

    public function __sleep()
    {
        return [
            'name',
            'className',
        ];
    }

    public function __wakeup()
    {
        $this->reflection = new \ReflectionMethod($this->className, $this->name);
    }
}

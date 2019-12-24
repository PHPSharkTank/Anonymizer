<?php

declare(strict_types=1);

namespace PHPSharkTank\AnonymizeBundle\Visitor;

use PHPSharkTank\AnonymizeBundle\Loader\LoaderInterface;
use PHPSharkTank\AnonymizeBundle\Metadata\PropertyMetadata;
use PHPSharkTank\AnonymizeBundle\Exception\MetadataNotFoundException;
use PHPSharkTank\AnonymizeBundle\Registry\HandlerRegistryInterface;

final class GraphNavigator implements GraphNavigatorInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var HandlerRegistryInterface
     */
    private $registry;

    /**
     * @var \SplStack
     */
    private $stack;

    public function __construct(LoaderInterface $loader, HandlerRegistryInterface $registry)
    {
        $this->loader = $loader;
        $this->registry = $registry;

        $this->stack = new \SplStack();
    }

    public function visit($value): void
    {
        if (is_iterable($value)) {
            $this->visitIterable($value);
        } elseif (is_object($value)) {
            $this->visitObject($value);
        }
    }

    private function visitObject($value)
    {
        try {
            $classMetadata = $this->loader->getMetadataFor(get_class($value));
        } catch (MetadataNotFoundException $e) {
            return;
        }

        $this->stack->push($classMetadata);

        foreach ($classMetadata->getPropertyMetadata() as $metadata) {
            $this->visitProperty($value, $metadata);
        }

        $this->stack->pop();
    }

    private function visitIterable($value)
    {
        foreach ($value as $item) {
            $this->visit($item);
        }
    }

    private function visitProperty($value, PropertyMetadata $metadata)
    {
        $newValue = $metadata->getValue($value);

        if (is_iterable($newValue) || is_object($newValue)) {
            $this->visit($newValue);

            return;
        }

        $registry = $this->registry->get($metadata->getType());
        $newValue = $registry->process($value, $metadata->getOptions());
        $metadata->setValue($value, $newValue);
    }
}
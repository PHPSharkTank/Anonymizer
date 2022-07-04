<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Visitor;

use PHPSharkTank\Anonymizer\Event\PostAnonymizeEvent;
use PHPSharkTank\Anonymizer\Event\PreAnonymizeEvent;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\ExclusionStrategy\ChainExclusionStrategy;
use PHPSharkTank\Anonymizer\ExclusionStrategy\StrategyInterface;
use PHPSharkTank\Anonymizer\Loader\LoaderInterface;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use PHPSharkTank\Anonymizer\Registry\HandlerRegistryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class GraphNavigator implements GraphNavigatorInterface
{
    private \SplStack $stack;

    public function __construct(
        private readonly LoaderInterface $loader,
        private readonly HandlerRegistryInterface $registry,
        private readonly StrategyInterface $exclusionStrategy = new ChainExclusionStrategy([]),
        private readonly ?EventDispatcherInterface $dispatcher = null,
    ) {
        $this->stack = new \SplStack();
    }

    public function visit(mixed $value): void
    {
        if (is_iterable($value)) {
            $this->visitIterable($value);
        } elseif (is_object($value)) {
            $this->visitObject($value);
        }
    }

    private function visitObject(object $object): void
    {
        try {
            $classMetadata = $this->loader->getMetadataFor(get_class($object));
        } catch (MetadataNotFoundException) {
            return;
        }

        if ($this->exclusionStrategy->shouldSkipObject($object, $classMetadata)) {
            return;
        }

        foreach ($classMetadata->preAnonymizeable as $methodName) {
            $object->{$methodName}();
        }

        if (null !== $this->dispatcher) {
            $event = new PreAnonymizeEvent($object);
            $this->dispatcher->dispatch($event);

            if ($event->isTerminated()) {
                return;
            }
        }

        $this->stack->push($classMetadata);

        foreach ($classMetadata->getPropertyMetadata() as $metadata) {
            if ($this->exclusionStrategy->shouldSkipProperty($object, $metadata)) {
                continue;
            }

            $this->visitProperty($object, $metadata);
        }

        foreach ($classMetadata->postAnonymizeable as $methodName) {
            $object->{$methodName}();
        }

        $this->dispatcher?->dispatch(new PostAnonymizeEvent($object));

        $this->stack->pop();
    }

    private function visitIterable(iterable $value): void
    {
        foreach ($value as $item) {
            $this->visit($item);
        }
    }

    private function visitProperty(object $object, PropertyMetadata $metadata): void
    {
        $currentValue = $metadata->getValue($object);

        if (is_iterable($currentValue) || is_object($currentValue)) {
            $this->visit($currentValue);

            return;
        }

        $handler = $this->registry->get($metadata->getHandler());
        $newValue = $handler->process($object, array_merge($metadata->getOptions(), ['currentValue' => $currentValue]));
        $metadata->setValue($object, $newValue);
    }
}

<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Visitor;

use PHPSharkTank\Anonymizer\Event\PostAnonymizeEvent;
use PHPSharkTank\Anonymizer\Event\PreAnonymizeEvent;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\ExclusionStrategy\StrategyInterface;
use PHPSharkTank\Anonymizer\HasBeenAnonymizedInterface;
use PHPSharkTank\Anonymizer\Loader\LoaderInterface;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use PHPSharkTank\Anonymizer\Registry\HandlerRegistryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class GraphNavigator implements GraphNavigatorInterface
{
    private LoaderInterface $loader;

    private HandlerRegistryInterface $registry;

    private \SplStack $stack;

    private EventDispatcherInterface $dispatcher;

    private StrategyInterface $exclusionStrategy;

    public function __construct(
        LoaderInterface $loader,
        HandlerRegistryInterface $registry,
        StrategyInterface $exclusionStrategy,
        EventDispatcherInterface $dispatcher
    ) {
        $this->loader = $loader;
        $this->registry = $registry;

        $this->stack = new \SplStack();
        $this->dispatcher = $dispatcher;
        $this->exclusionStrategy = $exclusionStrategy;
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

        $event = new PreAnonymizeEvent($object);

        foreach ($classMetadata->preAnonymizeable as $methodName) {
            $object->{$methodName}($event);
        }

        $this->dispatcher->dispatch($event);

        if ($event->isTerminated() || $this->exclusionStrategy->shouldSkipObject($object, $classMetadata)) {
            return;
        }

        $this->dispatcher->dispatch($event = new PreAnonymizeEvent($object));

        $this->stack->push($classMetadata);

        foreach ($classMetadata->getPropertyMetadata() as $metadata) {
            if ($this->exclusionStrategy->shouldSkipProperty($object, $metadata)) {
                continue;
            }

            $this->visitProperty($object, $metadata);
        }

        foreach ($classMetadata->postAnonymizeable as $methodName) {
            $object->{$methodName}($event);
        }

        $this->dispatcher->dispatch(new PostAnonymizeEvent($object));

        if ($object instanceof HasBeenAnonymizedInterface) {
            $object->beenAnonymized();
        }

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

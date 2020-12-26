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

    private function visitObject(object $value): void
    {
        try {
            $classMetadata = $this->loader->getMetadataFor(get_class($value));
            if (!$classMetadata->enabled) {
                throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $classMetadata->className));
            }
        } catch (MetadataNotFoundException $e) {
            return;
        }

        $event = new PreAnonymizeEvent($value);

        foreach ($classMetadata->preAnonymizeable as $methodName) {
            $value->{$methodName}($event);
        }

        $this->dispatcher->dispatch($event);

        if ($event->isTerminated() || $this->exclusionStrategy->shouldSkipObject($value, $classMetadata)) {
            return;
        }

        $this->dispatcher->dispatch($event = new PreAnonymizeEvent($value));

        $this->stack->push($classMetadata);

        foreach ($classMetadata->getPropertyMetadata() as $metadata) {
            if ($this->exclusionStrategy->shouldSkipProperty($value, $metadata)) {
                continue;
            }

            $this->visitProperty($value, $metadata);
        }

        foreach ($classMetadata->postAnonymizeable as $methodName) {
            $value->{$methodName}($event);
        }

        $this->dispatcher->dispatch(new PostAnonymizeEvent($value));

        if ($value instanceof HasBeenAnonymizedInterface) {
            $value->beenAnonymized();
        }

        $this->stack->pop();
    }

    private function visitIterable(iterable $value): void
    {
        foreach ($value as $item) {
            $this->visit($item);
        }
    }

    private function visitProperty(mixed $value, PropertyMetadata $metadata): void
    {
        $newValue = $metadata->getValue($value);

        if (is_iterable($newValue) || is_object($newValue)) {
            $this->visit($newValue);

            return;
        }

        $registry = $this->registry->get($metadata->getType());
        $newValue = $registry->process($value, array_merge($metadata->getOptions(), ['currentValue' => $newValue]));
        $metadata->setValue($value, $newValue);
    }
}

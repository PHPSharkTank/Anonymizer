<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\EventSubscriber;

use PHPSharkTank\Anonymizer\Annotation\PostAnonymize;
use PHPSharkTank\Anonymizer\Annotation\PreAnonymize;
use PHPSharkTank\Anonymizer\Event\PostAnonymizeEvent;
use PHPSharkTank\Anonymizer\Event\PreAnonymizeEvent;
use PHPSharkTank\Anonymizer\Loader\LoaderInterface;
use PHPSharkTank\Anonymizer\Metadata\MethodMetadata;

class LifecycleCallbackSubscriber
{
    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function onPreAnonymize(PreAnonymizeEvent $event): void
    {
        $methodes = $this->getInvokableMethods($event->getObject(), PreAnonymize::class);
        $this->invokeMethodes($methodes, $event->getObject());
    }

    public function onPostAnonymize(PostAnonymizeEvent $event): void
    {
        $methodes = $this->getInvokableMethods($event->getObject(), PostAnonymize::class);
        $this->invokeMethodes($methodes, $event->getObject());
    }

    private function getInvokableMethods($object, string $event): array
    {
        $methodes = [];
        $classMetadata = $this->loader->getMetadataFor(get_class($object));

        foreach ($classMetadata->getMethodMetadata() as $methodMetadata) {
            if ($event === $methodMetadata->getAnnotatinoClass()) {
                $methodes[] = $methodMetadata;
            }
        }

        return $methodes;
    }

    /**
     * @param array<MethodMetadata> $methodes
     */
    private function invokeMethodes(array $methodes, $object): void
    {
        /** @var MethodMetadata $methode */
        foreach ($methodes as $methode) {
            $methode->invoke($object);
        }
    }
}

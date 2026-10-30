<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use Psr\Cache\CacheItemPoolInterface;

final class CachingLoader implements LoaderInterface
{
    private LoaderInterface $delegate;

    private CacheItemPoolInterface $pool;

    public function __construct(LoaderInterface $delegate, CacheItemPoolInterface $pool)
    {
        $this->delegate = $delegate;
        $this->pool = $pool;
    }

    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $item = $this->pool->getItem(md5($className));
        if ($item->isHit()) {
            $metadata = $item->get();

            if ($metadata instanceof ClassMetadataInfo) {
                return $metadata;
            }
        }

        $metadata = $this->delegate->getMetadataFor($className);
        $item->set($metadata);
        $this->pool->save($item);

        return $metadata;
    }
}

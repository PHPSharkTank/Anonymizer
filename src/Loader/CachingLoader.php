<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use Psr\Cache\CacheItemPoolInterface;

final class CachingLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $delegate;
    /**
     * @var CacheItemPoolInterface
     */
    private $pool;

    public function __construct(LoaderInterface $delegate, CacheItemPoolInterface $pool)
    {
        $this->delegate = $delegate;
        $this->pool = $pool;
    }

    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $item = $this->pool->getItem(md5($className));
        if ($item->isHit()) {
            return $item->get();
        }

        try {
            $metadata = $this->delegate->getMetadataFor($className);
            $item->set($metadata);
            $this->pool->save($item);

            return $metadata;
        } catch (MetadataNotFoundException $e) {
            throw $e;
        }
    }
}

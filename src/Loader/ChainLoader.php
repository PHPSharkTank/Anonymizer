<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;

class ChainLoader implements LoaderInterface
{
    public function __construct(
        private readonly iterable $loaders,
    ) {}

    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $metadata = new ClassMetadataInfo($className);
        $findLoader = false;

        /** @var LoaderInterface $loader */
        foreach ($this->loaders as $loader) {
            try {
                $metadata->merge($loader->getMetadataFor($className));

                $findLoader = true;
            } catch (MetadataNotFoundException) {
                continue;
            }
        }

        if (!$findLoader) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        return $metadata;
    }
}

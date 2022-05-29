<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader implements LoaderInterface
{
    public function __construct(
        private array $paths
    ) {}

    public function addYamlPath(string $path): void
    {
        $this->paths[] = $path;
    }

    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $metadataConfig = [];
        $metadata = new ClassMetadataInfo($className);

        foreach ($this->paths as $path) {
            if (file_exists($path) && $content = file_get_contents($path)) {
                /** @var array $fileContent */
                $fileContent = Yaml::parse($content);
                $metadataConfig = array_merge_recursive($metadataConfig, $fileContent);
            }
        }
        if (!array_key_exists($className, $metadataConfig)) {
            throw new MetadataNotFoundException(sprintf('The class %s is not enabled for anonymization', $className));
        }

        if (array_key_exists('skip', $metadataConfig[$className])) {
            $metadata->expr = $metadataConfig[$className]['skip'];
        }

        foreach ($metadataConfig[$className]['properties'] ?? [] as $property => $settings) {
            $propertyMetadata = new PropertyMetadata($className, $property, $settings['handler']);
            $propertyMetadata->setOptions($settings['options'] ?? []);
            $metadata->addPropertyMetadata($propertyMetadata);
        }

        foreach ($metadataConfig[$className]['methods'] ?? [] as $method => $settings) {
            if (array_key_exists('preAnonymize', $settings) && $settings['preAnonymize']) {
                if (!in_array($method, $metadata->preAnonymizeable)) {
                    $metadata->preAnonymizeable[] = $method;
                }
            }

            if (array_key_exists('postAnonymize', $settings) && $settings['postAnonymize']) {
                if (!in_array($method, $metadata->postAnonymizeable)) {
                    $metadata->postAnonymizeable[] = $method;
                }
            }
        }

        return $metadata;
    }
}

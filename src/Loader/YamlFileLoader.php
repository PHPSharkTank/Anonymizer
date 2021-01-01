<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $delegate;

    /**
     * @var string[]
     */
    private $paths = [];

    public function __construct(LoaderInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    public function addYamlPath(string $path): void
    {
        $this->paths[] = $path;
    }

    public function getMetadataFor(string $className): ClassMetadataInfo
    {
        $metadataConfig = [];
        $metadata = $this->delegate->getMetadataFor($className);

        foreach ($this->paths as $path) {
            if (file_exists($path) && $content = file_get_contents($path)) {
                $fileContent = Yaml::parse($content);
                $metadataConfig = array_merge_recursive($metadataConfig, $fileContent);
            }
        }
        if(!array_key_exists($className, $metadataConfig)) {
            return $metadata;
        }

        $metadata->enabled = true;
        if (array_key_exists('enabled', $metadataConfig[$className])) {
            $metadata->enabled = (bool)$metadataConfig[$className]['enabled'];
        }

        if (array_key_exists('skip', $metadataConfig[$className])) {
            $metadata->expr = $metadataConfig[$className]['skip'];
        }

        foreach ($metadataConfig[$className]['properties'] ?: [] as $property => $settings) {
            $propertyMetadata = new PropertyMetadata($className, $property, $settings['type']);
            $propertyMetadata->setOptions($settings['options'] ?: []);
            $metadata->addPropertyMetadata($propertyMetadata);
        }

        foreach ($metadataConfig[$className]['methods'] ?: [] as $method => $settings) {
            if (array_key_exists('preAnonymize', $settings) && $settings['preAnonymize']) {
                if (!in_array($method, $metadata->preAnonymizeable)) {
                    $metadata->preAnonymizeable[] = $method;
                }
            } else if (array_key_exists('preAnonymize', $settings) && !$settings['preAnonymize']) {
                unset($metadata->preAnonymizeable[array_search($method, $metadata->preAnonymizeable, true)] );
            }

            if (array_key_exists('postAnonymize', $settings) && $settings['postAnonymize']) {
                if (!in_array($method, $metadata->postAnonymizeable)) {
                    $metadata->postAnonymizeable[] = $method;
                }
            } else if (array_key_exists('postAnonymize', $settings) && !$settings['postAnonymize']) {
                unset($metadata->postAnonymizeable[array_search($method, $metadata->postAnonymizeable, true)] );
            }
        }

        return $metadata;
    }
}

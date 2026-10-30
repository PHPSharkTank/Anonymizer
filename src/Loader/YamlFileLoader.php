<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Loader;

use PHPSharkTank\Anonymizer\Exception\LogicException;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPSharkTank\Anonymizer\Metadata\PropertyMetadata;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader implements LoaderInterface
{
    /**
     * @param array<string> $paths
     */
    public function __construct(
        private array $paths,
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
                $fileContent = Yaml::parse($content);

                if (\is_array($fileContent)) {
                    $metadataConfig = array_merge_recursive($metadataConfig, $fileContent);
                }
            }
        }

        if (!\array_key_exists($className, $metadataConfig)) {
            throw new MetadataNotFoundException(\sprintf('The class %s is not enabled for anonymization', $className));
        }

        $classConfig = $this->toArray($metadataConfig[$className], $className);

        if (\array_key_exists('skip', $classConfig)) {
            $metadata->expr = $this->toString($classConfig['skip'], $className.'.skip');
        }

        foreach ($this->toArray($classConfig['properties'] ?? [], $className.'.properties') as $property => $settings) {
            $path = $className.'.properties.'.$property;
            $settings = $this->toArray($settings, $path);

            $propertyMetadata = new PropertyMetadata(
                $metadata->className,
                (string) $property,
                $this->toString($settings['handler'] ?? null, $path.'.handler'),
            );
            $propertyMetadata->setOptions($this->toOptions($settings['options'] ?? [], $path.'.options'));
            $metadata->addPropertyMetadata($propertyMetadata);
        }

        foreach ($this->toArray($classConfig['methods'] ?? [], $className.'.methods') as $method => $settings) {
            $method = (string) $method;
            $settings = $this->toArray($settings, $className.'.methods.'.$method);

            if (($settings['preAnonymize'] ?? false) && !\in_array($method, $metadata->preAnonymizeable, true)) {
                $metadata->preAnonymizeable[] = $method;
            }

            if (($settings['postAnonymize'] ?? false) && !\in_array($method, $metadata->postAnonymizeable, true)) {
                $metadata->postAnonymizeable[] = $method;
            }
        }

        return $metadata;
    }

    /**
     * @return array<mixed>
     */
    private function toArray(mixed $value, string $path): array
    {
        if (!\is_array($value)) {
            throw new LogicException(\sprintf('Invalid anonymizer configuration at "%s": expected a map, got %s.', $path, get_debug_type($value)));
        }

        return $value;
    }

    private function toString(mixed $value, string $path): string
    {
        if (!\is_string($value)) {
            throw new LogicException(\sprintf('Invalid anonymizer configuration at "%s": expected a string, got %s.', $path, get_debug_type($value)));
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function toOptions(mixed $value, string $path): array
    {
        $options = [];

        foreach ($this->toArray($value, $path) as $key => $option) {
            $options[(string) $key] = $option;
        }

        return $options;
    }
}

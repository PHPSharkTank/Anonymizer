<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Loader;

use PHPSharkTank\Anonymizer\Exception\LogicException;
use PHPSharkTank\Anonymizer\Exception\MetadataNotFoundException;
use PHPSharkTank\Anonymizer\Loader\YamlFileLoader;
use PHPUnit\Framework\TestCase;

class YamlFileLoaderTest extends TestCase
{
    /**
     * @var array<string>
     */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
    }

    public function testGetMetadataFor(): void
    {
        $loader = new YamlFileLoader([$this->writeYaml(<<<'YAML'
            PHPSharkTank\Anonymizer\Tests\Loader\YamlUser:
                skip: 'obj.name == ""'
                properties:
                    name:
                        handler: faker
                        options:
                            locale: de_DE
                    email:
                        handler: 'null'
                methods:
                    before:
                        preAnonymize: true
                    after:
                        postAnonymize: true
            YAML)]);

        $metadata = $loader->getMetadataFor(YamlUser::class);

        self::assertSame('obj.name == ""', $metadata->expr);
        self::assertSame(['before'], $metadata->preAnonymizeable);
        self::assertSame(['after'], $metadata->postAnonymizeable);

        $properties = $metadata->getPropertyMetadata();
        self::assertSame(['name', 'email'], array_keys($properties));
        self::assertSame('faker', $properties['name']->getHandler());
        self::assertSame(['locale' => 'de_DE'], $properties['name']->getOptions());
        self::assertSame('null', $properties['email']->getHandler());
        self::assertSame([], $properties['email']->getOptions());
    }

    public function testGetMetadataForMergesMultipleFiles(): void
    {
        $loader = new YamlFileLoader([$this->writeYaml(<<<'YAML'
            PHPSharkTank\Anonymizer\Tests\Loader\YamlUser:
                properties:
                    name:
                        handler: faker
            YAML)]);
        $loader->addYamlPath($this->writeYaml(<<<'YAML'
            PHPSharkTank\Anonymizer\Tests\Loader\YamlUser:
                properties:
                    email:
                        handler: hash
            YAML));

        $properties = $loader->getMetadataFor(YamlUser::class)->getPropertyMetadata();

        self::assertSame(['name', 'email'], array_keys($properties));
    }

    public function testGetMetadataForUnknownClass(): void
    {
        $loader = new YamlFileLoader([$this->writeYaml('{}')]);

        $this->expectException(MetadataNotFoundException::class);

        $loader->getMetadataFor(YamlUser::class);
    }

    public function testGetMetadataForMissingHandler(): void
    {
        $loader = new YamlFileLoader([$this->writeYaml(<<<'YAML'
            PHPSharkTank\Anonymizer\Tests\Loader\YamlUser:
                properties:
                    name:
                        options: []
            YAML)]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid anonymizer configuration at "PHPSharkTank\Anonymizer\Tests\Loader\YamlUser.properties.name.handler": expected a string, got null.');

        $loader->getMetadataFor(YamlUser::class);
    }

    public function testGetMetadataForInvalidProperties(): void
    {
        $loader = new YamlFileLoader([$this->writeYaml(<<<'YAML'
            PHPSharkTank\Anonymizer\Tests\Loader\YamlUser:
                properties: name
            YAML)]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('expected a map, got string');

        $loader->getMetadataFor(YamlUser::class);
    }

    private function writeYaml(string $content): string
    {
        $file = tempnam(sys_get_temp_dir(), 'anonymizer');
        self::assertIsString($file);
        file_put_contents($file, $content);

        return $this->files[] = $file;
    }
}

class YamlUser
{
    public string $name = '';

    public string $email = '';

    public function before(): void
    {
    }

    public function after(): void
    {
    }
}

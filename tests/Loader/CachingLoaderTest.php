<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Loader;

use PHPSharkTank\Anonymizer\Exception\RuntimeException;
use PHPSharkTank\Anonymizer\Loader\CachingLoader;
use PHPSharkTank\Anonymizer\Loader\LoaderInterface;
use PHPSharkTank\Anonymizer\Metadata\ClassMetadataInfo;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingLoaderTest extends TestCase
{
    use ProphecyTrait;

    private CachingLoader $loader;

    private LoaderInterface|ObjectProphecy $delegate;

    private CacheItemPoolInterface|ObjectProphecy $pool;

    protected function setUp(): void
    {
        $this->delegate = $this->prophesize(LoaderInterface::class);
        $this->pool = $this->prophesize(CacheItemPoolInterface::class);
        $this->loader = new CachingLoader($this->delegate->reveal(), $this->pool->reveal());
    }

    public function testGetMetadataForWithCache(): void
    {
        $metadata = new ClassMetadataInfo('stdClass');

        $item = $this->prophesize(CacheItemInterface::class);
        $item->isHit()->willReturn(true);
        $item->get()->willReturn($metadata);

        $this->pool->getItem('09a15e9660c1ebc6f429d818825ce0c6')->willReturn($item->reveal());

        $this->delegate->getMetadataFor('stdClass')
            ->shouldNotBeCalled();

        self::assertSame($metadata, $this->loader->getMetadataFor('stdClass'));
    }

    public function testGetMetadataForWithoutCache(): void
    {
        $metadata = new ClassMetadataInfo('stdClass');

        $item = $this->prophesize(CacheItemInterface::class);
        $item->isHit()->willReturn(false);
        $item->set($metadata)->shouldBeCalled();

        $this->pool->getItem('09a15e9660c1ebc6f429d818825ce0c6')->willReturn($item->reveal());
        $this->pool->save($item->reveal())->shouldBeCalled();

        $this->delegate->getMetadataFor('stdClass')
            ->willReturn($metadata);

        self::assertSame($metadata, $this->loader->getMetadataFor('stdClass'));
    }

    public function testGetMetadataException(): void
    {
        $this->expectException(RuntimeException::class);

        $metadata = new ClassMetadataInfo('stdClass');

        $item = $this->prophesize(CacheItemInterface::class);
        $item->isHit()->willReturn(false);
        $item->set($metadata)->shouldNotBeCalled();

        $this->pool->getItem('09a15e9660c1ebc6f429d818825ce0c6')->willReturn($item->reveal());

        $this->delegate->getMetadataFor('stdClass')
            ->willThrow(new RuntimeException());

        $this->loader->getMetadataFor('stdClass');
    }
}

<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Cache\Service;

use Generator;
use MichaelKeiluweit\WarmUp\Cache\DataType\SeoItem as SeoItemDataType;
use MichaelKeiluweit\WarmUp\Cache\Infrastructure\SeoItem;
use MichaelKeiluweit\WarmUp\Cache\Service\BuildTemplateCache;
use MichaelKeiluweit\WarmUp\Shared\Infrastructure\ExternalResource;
use OxidEsales\Eshop\Core\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressIndicator;


#[CoversClass(BuildTemplateCache::class)]
#[UsesClass(SeoItemDataType::class)]
class BuildTemplateCacheTest extends TestCase
{
    public function testExecute(): void
    {
        $seoItemInfrastructure = $this->createStub(SeoItem::class);
        $seoItemInfrastructure->method('getAllUrls')->willReturn($this->provideSeoItemDataTypeGenerator());

        $config = $this->createStub(Config::class);
        $config->method('getShopUrl')->willReturn('http://domain/');

        $resourceInterface = $this->createMock(ExternalResource::class);
        $resourceInterface->expects($this->exactly(2))->method('poke');

        $progressIndicator = $this->createMock(ProgressIndicator::class);
        $progressIndicator->expects($this->any())->method('advance');

        $buildTemplateCache = new BuildTemplateCache(
            $seoItemInfrastructure, $config, $resourceInterface
        );

        $buildTemplateCache->execute($progressIndicator);
    }

    public function provideSeoItemDataTypeGenerator(): Generator
    {
        yield from [
            new SeoItemDataType('url', 0),
            new SeoItemDataType('url1', 1)
        ];
    }
}

<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Cache\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Generator;
use MichaelKeiluweit\WarmUp\Cache\DataType\SeoItem as SeoItemDataType;
use MichaelKeiluweit\WarmUp\Cache\Infrastructure\SeoItem;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeoItem::class)]
#[UsesClass(SeoItemDataType::class)]
class SeoItemTest extends TestCase
{
    public function testGetAllUrls(): void
    {
        function iterableResult(): Generator {
            yield from [
                ['oxstdurl' => 'url0', 'oxlang' => 0],
            ];
        }

        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('iterateAssociative')->willReturn(iterableResult());

        $queryBuilder = $this->createStub(QueryBuilder::class);
        $queryBuilder->method('getConnection')->willReturn($connectionStub);

        $queryBuilderFactoryStub = $this->createStub(QueryBuilderFactoryInterface::class);
        $queryBuilderFactoryStub->method('create')->willReturn($queryBuilder);


        $infrastructure = new SeoItem($queryBuilderFactoryStub);

        $this->assertInstanceOf(Generator::class, $infrastructure->getAllUrls());

        foreach ($infrastructure->getAllUrls() as $url) {
            $this->assertInstanceOf(SeoItemDataType::class, $url);
            $this->assertEquals(new SeoItemDataType('url0', 0), $url);
        }
    }
}

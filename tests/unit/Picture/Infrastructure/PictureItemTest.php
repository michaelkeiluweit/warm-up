<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Picture\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Generator;
use MichaelKeiluweit\WarmUp\Picture\Infrastructure\PictureItem;
use MichaelKeiluweit\WarmUp\Shared\Factory\ShopObject;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

use PHPUnit\Framework\TestCase;

#[CoversClass(PictureItem::class)]
class PictureItemTest extends TestCase
{
    public function testGetAllProducts(): void
    {
        function iterableResult(): Generator {
            yield from [
                ['oxid' => 't665'],
            ];
        }

        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('iterateAssociative')->willReturn(iterableResult());

        $queryBuilder = $this->createStub(QueryBuilder::class);
        $queryBuilder->method('getConnection')->willReturn($connectionStub);

        $queryBuilderFactoryStub = $this->createStub(QueryBuilderFactoryInterface::class);
        $queryBuilderFactoryStub->method('create')->willReturn($queryBuilder);

        $ShopObject = $this->createMock(BaseModel::class);
        $ShopObject->expects($this->once())->method('load');

        $shopObjectFactoryStub = $this->createStub(ShopObject::class);
        $shopObjectFactoryStub->method('createArticle')->willReturn($ShopObject);

        $infrastructure = new PictureItem(
            $queryBuilderFactoryStub,
            $shopObjectFactoryStub
        );

        $this->assertInstanceOf(Generator::class, $infrastructure->getAllProducts());

        foreach ($infrastructure->getAllProducts() as $product) {
            $this->assertInstanceOf(BaseModel::class, $product);
        }
    }
}

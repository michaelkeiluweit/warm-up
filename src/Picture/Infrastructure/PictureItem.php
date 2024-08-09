<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Picture\Infrastructure;

use Generator;
use MichaelKeiluweit\WarmUp\Picture\Factory\ShopObject;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class PictureItem
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private ShopObject $shopObjectFactory,
    ) {}

    public function getAllProducts(): Generator
    {
        $connection = $this->queryBuilderFactory->create()->getConnection();
        $query = 'select oxid from oxarticles where oxshopid = :id';

        foreach ($connection->iterateAssociative($query, ['id' => 1]) as $row) {
            $product = $this->shopObjectFactory->create(Article::class);
            $product->load($row['oxid']);
            yield $product;
        }
    }
}

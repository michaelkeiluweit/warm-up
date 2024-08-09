<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Cache\Infrastructure;

use Generator;
use MichaelKeiluweit\WarmUp\Cache\DataType\SeoItem as SeoItemDataType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class SeoItem
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory
    ) {}

    public function getAllUrls(): Generator
    {
        $connection = $this->queryBuilderFactory->create()->getConnection();
        $query = 'select oxstdurl, oxlang from oxseo where oxshopid = :id';

        foreach ($connection->iterateAssociative($query, [':id' => 1]) as $row) {
            yield new SeoItemDataType($row['oxstdurl'], $row['oxlang']);
        }
    }
}

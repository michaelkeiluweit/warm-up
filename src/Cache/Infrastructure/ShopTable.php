<?php
declare(strict_types=1);


namespace MichaelKeiluweit\WarmUp\Cache\Infrastructure;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class ShopTable
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory
    ) {}

    public function getAllNames(): array
    {
        $data = $this
            ->queryBuilderFactory
            ->create()
            ->getConnection()
            ->executeQuery(
                "select 
                    *
                from
                    information_schema.tables
                where 
                    table_type = 'base table'
                and
                    table_name like 'ox%'"
            )
        ->fetchAllAssociative();


        $names = [];
        foreach ($data as $item) {
            $names[] = $item['TABLE_NAME'];
        }

        return $names;
    }
}

## About

This command is about to pre-fill the cache by calling the cache-items beforehand.  
The mission of this project is to have the directory `tmp` filled automatically as much as possible so that a request 
from a customer doesn't add anything more, but just benefits from the provided cache files.

### Template Cache
Every `oxstdurl` is called to motivate the template engine to compile the page, which leads to a pre-compiled 
template cache.

### Pictures
All different kinds of pictures are generated from the main picture.

## Installation
```shell
composer require michaelkeiluweit/warm-up
```

## Usage
```shell
./vendor/bin/oe-console mk:warm-up
```

See `./vendor/bin/oe-console mk:warm-up --help` for optional switches. 

## Todos
- try catch iterateAssociative
- doctrine default log? docu says: set to null to increase performance
- database structure cache files generator
- Multishop compatibility
- Currently only product pictures are generated

# Nice to know

## Doubled composer requirements
The module needs the QueryBuilder, provided by the shop framework, to get the database connection.
While running the unit tests in the context of the shop framework it's all fine. But I want to generate the coverage 
report, which isn't possible via shop framework context, since the file phpunit.xml doesn't have the required parameters.
That's why I have to create an own phpunit.xml file within my module and execute the tests in the context
of the module. When I run the unit tests in the context of the module, the QueryBuilder isn't locateable. So I must add 
the doctrine/dbal package to the dev requirements of my module. Also, the QueryBuilderFactoryInterface must be included, 
which requires the oxideshop-ce package.
Currently, the composer.json file contains this require-dev section (which feels like overhead):
```json
"require-dev": {
    "phpunit/phpunit": "^11",
    "doctrine/dbal": "^2",
    "oxid-esales/oxideshop-ce": "dev-b-7.1.x",
}
```
As you can see, I've used phpunit 11, which is technically not compatible to the phpunit version the shop is using.
That's not so bad, since there is no connection between the shop framework tests and my personal tests. It becomes maybe
difficult if someone wants to run all tests at once. And the required PHP version could be a problem.

I have to make sure to have the very same doctrine/dbal package as the shop, since the module builds on top of the
framework and uses its functionality. So the module could become incompatible to a new shop version, despite it's all
compatible, but the used dependencies could be outdated.

And still, after I've required oxideshop-ce in my project, I can't test the method MichaelKeiluweit\WarmUp\Picture\Factory\ShopObject::create, 
since the function startProfile can't be found. When I replace the function oxNew by UtilsObject::getInstance()->oxNew, 
then an error appears that the file config.inc.php can't be found.

## Performance test: Request resources

### curl
```php
$saveTime = [];

for ($i = 0; $i < 1000; ++$i) {
    $start = microtime(true);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://google.com/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    $saveTime[] = microtime(true) - $start;
}

$reduce = array_reduce($saveTime, function ($carry, $item) {
    return $carry + $item;
});

var_dump($reduce / 1000);
```
```shell
float(0.0665782573223114)
```

### file_get_contents
```php
$saveTime = [];

for ($i = 0; $i < 1000; ++$i) {

    $start = microtime(true);

    file_get_contents('https://google.com/');

    $saveTime[] = microtime(true) - $start;
}

$reduce = array_reduce($saveTime, function ($carry, $item) {
    return $carry + $item;
});

var_dump($reduce / 1000);
```
```shell
float(0.37306804990768433)
```


## Performance test: Big data query

### Specs

- https://github.com/OXID-eSales/docker-eshop-sdk-recipes/blob/master/shop/b-7.1.x-components.sh
- mysql Ver 14.14 Distrib 5.7.42, for Linux (x86_64)
- PHP 8.2.21
- OXID eShop Enterprise Edition 7.1.1
- doctrine/dbal: 2.13.9
- 5.8 million products
- 13.1 million seo entries

Between each test the database container was restarted to clear the cached information. To monitor that, the docker
desktop extension [Resource usage](https://hub.docker.com/extensions/docker/resource-usage-extension) was used.

Every test code (except test #1) was embedded in an oxideshop-component code and executed by calling `./vendor/bin/oe-console m:c:w`:
```php
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
        // code snippets
    }
}

```

### Test #1 - iterate dbal 4
This test was executed by a standalone script without involving the shop framework to test dbal 4.
That might explain the 2 MB less PHP memory in comparison to the other framework involving tests.
```
PHP memory: 2 mb
MySQL memory: 138 mb
It took me 58s
```
```php
$connection = DriverManager::getConnection($connectionParams);
$query = 'select oxstdurl, oxlang from oxseo where oxshopid = :id';

foreach ($connection->iterateAssociative($query, ['id' => 1]) as $row) {
    yield new SeoItemDataType($row['oxstdurl'], $row['oxlang']);
}
```


### Test #2 - iterate dbal 2
Please note the colon at the named parameter. In dbal 4 it's not allowed.
```
PHP memory: 4 mb
MySQL memory: 143 mb
It took me 61s
```
```php
$connection = $this->queryBuilderFactory->create()->getConnection();
$query = 'select oxstdurl, oxlang from oxseo where oxshopid = :id';

foreach ($connection->iterateAssociative($query, [':id' => 1]) as $row) {
    yield new SeoItemDataType($row['oxstdurl'], $row['oxlang']);
}
```

### Test #3 - resultSet
https://docs.oxid-esales.com/developer/en/6.0/update/eshop_from_53_to_6/database.html
```
PHP memory: 1.1 gb
MySQL memory: 183 mb
It took me 68s.
```
```php
$query = 'select oxstdurl, oxlang from oxseo where oxshopid = :id';
$resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query, [':id' => 1]);

if ($resultSet != false && $resultSet->count() > 0) {
    while (!$resultSet->EOF) {
        $row = $resultSet->getFields();
        yield new SeoItemDataType($row[0], $row[1]);
        $resultSet->fetchRow();
    }
}
```

### Test #4 - offset
That doesn't work well since InnoDB doesn't store the information in its meta table how many rows per table are existing.
MyISAM on the other hand, does: https://stackoverflow.com/a/6274941. OXID eShop uses InnoDB on all tables,
so `select count(*)` has a bad performance.
The execution time might could have been tweaked by increasing the offset, but it's only a guess which offset would
work the best in comparison to memory usage and execution time on the real installations out there.
```
PHP memory: 4 mb
MySQL memory: 551 mb
It took me 39m 56s.
```
```php
$queryBuilder = $this->queryBuilderFactory->create();
$rawData = $queryBuilder
    ->select('count(*) as amount')
    ->from('oxseo')
    ->setMaxResults(1);

$amount = $rawData->execute()->fetchOne();

for ($offset = 0; $offset < $amount; $offset+=100000) {
    $rawData = $queryBuilder
        ->select('oxstdurl, oxlang')
        ->from('oxseo')
        ->where('oxshopid = 1')
        ->setFirstResult($offset)
        ->setMaxResults(100000)
        ->execute();

    $objects = [];

    foreach ($rawData->fetchAllAssociative() as $row) {
        $objects[] = new SeoItemDataType($row['oxstdurl'], $row['oxlang']);
    }

    yield $objects;
}
```

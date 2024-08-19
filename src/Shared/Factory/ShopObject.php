<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Shared\Factory;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Core\UtilsObject;

class ShopObject
{
    public function create(string $fullQualifiedNamespace, ...$arguments): object
    {
        return UtilsObject::getInstance()->oxNew($fullQualifiedNamespace, $arguments);
    }

    /**
     * @template T of object
     * @param class-string<T> $fullQualifiedNamespace
     * @return T
     */
    public function createFromRegistry(string $fullQualifiedNamespace): object
    {
        return $this->create(Registry::class)::get($fullQualifiedNamespace);
    }

    public function createArticle(...$arguments)
    {
        return $this->create(Article::class, ...$arguments);
    }

    public function createBaseModel(...$arguments)
    {
        return $this->create(BaseModel::class, ...$arguments);
    }

    public function createUtils()
    {
        return $this->createFromRegistry(Utils::class);
    }
}

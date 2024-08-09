<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Picture\Factory;

use OxidEsales\EshopCommunity\Core\UtilsObject;

class ShopObject
{
    public function create(string $fullQualifiedNamespace, ...$arguments): object
    {
        return UtilsObject::getInstance()->oxNew($fullQualifiedNamespace, $arguments);
    }
}

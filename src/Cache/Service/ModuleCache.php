<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Cache\Service;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;

class ModuleCache
{
    /**
     * Generates:
     * Primary:
     * 1. tmp/absolute_module_paths.txt
     * 2. tmp/modules/1/module_class_extensions.txt
     * 3. tmp/modules/1/controllers.txt
     * Secondary:
     * 1. tmp/container/container_cache_shop_1.php
     */
    public function execute(): void
    {
        $activeModulesDataProvider = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ActiveModulesDataProviderBridgeInterface::class)
        ;

        $activeModulesDataProvider->getClassExtensions();
        $activeModulesDataProvider->getModulePaths();
        $activeModulesDataProvider->getControllers();
    }
}

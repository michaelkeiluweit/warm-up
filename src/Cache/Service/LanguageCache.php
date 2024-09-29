<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Cache\Service;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;

class LanguageCache
{
    /**
     * Generates (examples):
     * 1. tmp/oxeec_langcache_0_0_1_apex_default.txt
     * 2. tmp/oxeec_langcache_0_1_1_apex_default.txt
     */
    public function execute(): void
    {
        foreach (Registry::getLang()->getActiveShopLanguageIds() as $id => $abbreviation) {
            (oxNew(Language::class))->translateString('', $id);
        }
    }
}

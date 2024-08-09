<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Cache\Service;

use MichaelKeiluweit\WarmUp\Cache\Infrastructure\SeoItem;
use MichaelKeiluweit\WarmUp\Shared\Infrastructure\ExternalResource;
use OxidEsales\Eshop\Core\Config;

class BuildTemplateCache
{
    public function __construct(
        private readonly SeoItem $seoItem,
        private readonly Config $config,
        private readonly ExternalResource $io,
    ) {}

    public function execute()
    {
        /** @var SeoItem $seoItem */
        foreach ($this->seoItem->getAllUrls() as $seoItem) {
            $url = $this->config->getShopUrl($seoItem->getLang()) . $seoItem->getUrl();
            $this->io->poke($url);
        }
    }
}

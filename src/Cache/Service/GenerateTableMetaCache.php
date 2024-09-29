<?php
declare(strict_types=1);


namespace MichaelKeiluweit\WarmUp\Cache\Service;

use MichaelKeiluweit\WarmUp\Cache\Infrastructure\ShopTable;
use MichaelKeiluweit\WarmUp\Shared\Contract\ProgressIndicatorable;
use MichaelKeiluweit\WarmUp\Shared\Factory\ShopObject;
use Symfony\Component\Console\Helper\ProgressIndicator;

class GenerateTableMetaCache implements ProgressIndicatorable
{
    public function __construct(
        private ShopTable $shopTable,
        private ShopObject $shopObject
    ) {}

    public function execute(ProgressIndicator $progressIndicator): void
    {
        $names = $this->shopTable->getAllNames();

        foreach ($names as $name) {
            $model = $this->shopObject->createBaseModel();
            $model->init($name, true);

            $utils = $this->shopObject->createUtils();
            $utils->commitFileCache();

            $progressIndicator->advance();
        }
    }
}

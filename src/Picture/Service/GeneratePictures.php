<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Picture\Service;

use MichaelKeiluweit\WarmUp\Picture\Infrastructure\PictureItem;
use MichaelKeiluweit\WarmUp\Shared\Infrastructure\ExternalResource;
use OxidEsales\Eshop\Application\Model\Article;

class GeneratePictures
{
    public function __construct(
        private readonly PictureItem $item,
        private readonly ExternalResource $io,
    ) {}

    public function execute(): void
    {
        $relevantPictureGalleryItems = [
            'Pics',
            'Icons',
        ];

        /** @var Article $product */
        foreach ($this->item->getAllProducts() as $product) {
            /*
             * [Pics|Icons] => Array (
             *     [1] => http://localhost.local/out/pictures/generated/product/1/800_600_75/t-shirt_damen_4a.png
             * )
             */
            foreach ($relevantPictureGalleryItems as $item) {
                foreach ($product->getPictureGallery()[$item] as $url) {
                    $this->io->poke($url);
                }
            }

            /*
             * Suddenly a wild format appears.
             * [ZoomPics] => Array (
             *     [1] => Array (
             *         [id] => 1
             *         [file] => http://localhost.local/out/pictures/generated/product/1/1200_1200_75/t-shirt_damen_4a.png
             *     )
             * )
             */
            foreach ($product->getPictureGallery()['ZoomPics'] as $associativeSubArray) {
                $this->io->poke($associativeSubArray['file']);
            }
        }
    }
}

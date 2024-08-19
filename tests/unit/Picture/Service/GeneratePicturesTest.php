<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Picture\Service;

use Generator;
use MichaelKeiluweit\WarmUp\Picture\Infrastructure\PictureItem;
use MichaelKeiluweit\WarmUp\Picture\Service\GeneratePictures;
use MichaelKeiluweit\WarmUp\Shared\Infrastructure\ExternalResource;
use OxidEsales\Eshop\Application\Model\Article;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressIndicator;

#[CoversClass(GeneratePictures::class)]
#[UsesClass(Article::class)]
class GeneratePicturesTest extends TestCase
{
    public function testExecute()
    {
        function iterableResult($product): Generator {
            yield from [$product];
        }

        $product = $this->createMock(Article::class);
        $product->expects($this->exactly(3))->method('getPictureGallery')->willReturn([
            'Pics' => ['testurl'],
            'Icons' => ['testurl'],
            'ZoomPics' => [['file' => 'testurl']]
        ]);

        $pictureItem = $this->createMock(PictureItem::class);
        $pictureItem->expects($this->once())->method('getAllProducts')->willReturn(
            iterableResult($product)
        );

        $externalResource = $this->createMock(ExternalResource::class);
        $externalResource->expects($this->exactly(3))->method('poke');

        $progressIndicator = $this->createMock(ProgressIndicator::class);
        $progressIndicator->expects($this->any())->method('advance');

        $generatePictures = new GeneratePictures(
            $pictureItem,
            $externalResource
        );

        $generatePictures->execute($progressIndicator);
    }
}

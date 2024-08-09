<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Cache\DataType;

use MichaelKeiluweit\WarmUp\Cache\DataType\SeoItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeoItem::class)]
final class SeoItemTest extends TestCase
{
    public function testGetter(): void
    {
        $seoItem = new SeoItem('url', 0);

        $this->assertEquals('url', $seoItem->getUrl());
        $this->assertEquals(0, $seoItem->getLang());
    }
}

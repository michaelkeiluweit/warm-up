<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Shared\Infrastructure;

use MichaelKeiluweit\WarmUp\Shared\Infrastructure\CurlFacade;
use MichaelKeiluweit\WarmUp\Shared\Infrastructure\ExternalResource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExternalResource::class)]
class ExternalResourceTest extends TestCase
{
    public function testPoke()
    {
        $curl = $this->createMock(CurlFacade::class);
        $curl->expects($this->once())->method('initialize')->willReturnSelf();
        $curl->expects($this->atLeastOnce())->method('setOption')->willReturnSelf();
        $curl->expects($this->once())->method('execute')->willReturnSelf();
        $curl->expects($this->once())->method('close');

        $externalResource = new ExternalResource($curl);
        $externalResource->poke('url');
    }
}

<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Shared\Infrastructure;

use CurlHandle;
use MichaelKeiluweit\WarmUp\Shared\Infrastructure\CurlFacade;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function curl_getinfo;

#[CoversClass(CurlFacade::class)]
class CurlFacadeTest extends TestCase
{
    public function testInitialize(): void
    {
        $curlFacadeMock = $this->createMock(CurlFacade::class);
        $curlFacadeMock->expects($this->once())->method('initialize')->willReturnSelf();

        $this->assertInstanceOf(CurlFacade::class, $curlFacadeMock->initialize());


        $curlFacade = new CurlFacade();
        $curlFacade->initialize();

        $this->assertInstanceOf(CurlHandle::class, $curlFacade->getCurlHandle());
    }

    public function testSetOption(): void
    {
        $curlFacade = new CurlFacade();
        $curlFacade->initialize();
        $curlFacade->setOption(CURLOPT_URL, 'test');

        $this->assertEquals('test', curl_getinfo($curlFacade->getCurlHandle())['url']);
    }

    public function testExecute(): void
    {
        $curlFacadeMock = $this->createMock(CurlFacade::class);
        $curlFacadeMock->expects($this->once())->method('execute')->willReturnSelf();
        $curlFacadeMock->initialize();

        $this->assertInstanceOf(CurlFacade::class, $curlFacadeMock->execute());


        $c = new CurlFacade();
        $c->initialize();
        $c->setOption(CURLOPT_RETURNTRANSFER, true);
        $c->setOption(CURLOPT_URL, 'https://www.google.com/');

        $this->assertEquals(0, curl_getinfo($c->getCurlHandle(), CURLINFO_HTTP_CODE));

        $c->execute();
        $c->close();

        $this->assertEquals(200, curl_getinfo($c->getCurlHandle(), CURLINFO_HTTP_CODE));
    }
}

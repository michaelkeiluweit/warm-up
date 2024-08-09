<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Shared\Infrastructure;

use CurlHandle;

class CurlFacade
{
    private CurlHandle $curlHandle;

    public function initialize(): self
    {
        $this->curlHandle = curl_init();
        return $this;
    }

    public function setOption(int $option, mixed $value): self
    {
        curl_setopt($this->curlHandle, $option, $value);
        return $this;
    }

    public function execute(): self
    {
        curl_exec($this->curlHandle);
        return $this;
    }

    public function close(): void
    {
        curl_close($this->curlHandle);
    }

    public function getCurlHandle(): CurlHandle
    {
        return $this->curlHandle;
    }
}

<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Shared\Infrastructure;

class ExternalResource
{
    public function __construct(
        private CurlFacade $curlFacade
    ) {}

    /**
     * Requests a resource but ignores the response.
     */
    public function poke(string $url): void
    {
        $this->curlFacade
            ->initialize()
            ->setOption(CURLOPT_URL, $url)
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->execute()
            ->close();
    }
}

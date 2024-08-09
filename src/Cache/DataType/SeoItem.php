<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Cache\DataType;

class SeoItem
{
    public function __construct(
        private string $url,
        private int $lang,
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLang(): int
    {
        return $this->lang;
    }
}

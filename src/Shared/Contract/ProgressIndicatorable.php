<?php

namespace MichaelKeiluweit\WarmUp\Shared\Contract;

use Symfony\Component\Console\Helper\ProgressIndicator;

interface ProgressIndicatorable
{
    public function execute(ProgressIndicator $progressIndicator): void;
}

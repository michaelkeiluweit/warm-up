<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Tests\Unit\Shared\Command;

use MichaelKeiluweit\WarmUp\Shared\Command\WarmUpCommand;
use MichaelKeiluweit\WarmUp\Cache\Service\BuildTemplateCache;
use MichaelKeiluweit\WarmUp\Picture\Service\GeneratePictures;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(WarmUpCommand::class)]
class WarmUpCommandTest extends TestCase
{
    public function testRun(): void
    {
        $buildTemplateCache = $this->createMock(BuildTemplateCache::class);
        $buildTemplateCache->expects($this->once())->method('execute');

        $generatePictures = $this->createMock(GeneratePictures::class);
        $generatePictures->expects($this->once())->method('execute');

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->any())->method('getOption')->willReturn(false);

        $command = new WarmUpCommand($buildTemplateCache, $generatePictures);
        $command->run($input, $this->createMock(OutputInterface::class));
    }
}

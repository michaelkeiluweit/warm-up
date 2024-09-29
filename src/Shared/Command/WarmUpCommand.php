<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Shared\Command;

use MichaelKeiluweit\WarmUp\Cache\Service\BuildTemplateCache;
use MichaelKeiluweit\WarmUp\Cache\Service\LanguageCache;
use MichaelKeiluweit\WarmUp\Cache\Service\ModuleCache;
use MichaelKeiluweit\WarmUp\Picture\Service\GeneratePictures;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class WarmUpCommand extends Command
{
    private const OPTION_WITHOUT_TEMPLATES = 'without-templates';
    private const OPTION_WITHOUT_PICTURES = 'without-pictures';
    private const OPTION_WITHOUT_MODULES_CACHE = 'without-modules-cache';
    private const OPTION_WITHOUT_LANGUAGE = 'without-language';

    public function __construct(
        private readonly BuildTemplateCache $buildTemplateCache,
        private readonly GeneratePictures $generatePictures,
        private readonly ModuleCache $moduleCache,
        private readonly LanguageCache $languageCache,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('mk:warm-up')
            ->setDescription('Pre compiles templates, generates pictures and database structure files.')
            ->setHelp('')
            ->addOption(
                WarmUpCommand::OPTION_WITHOUT_TEMPLATES,
                null,
                InputOption::VALUE_NONE,
                'Deactivates the compiling of the templates.'
            )
            ->addOption(
                WarmUpCommand::OPTION_WITHOUT_PICTURES,
                null,
                InputOption::VALUE_NONE,
                'Deactivates the generation of the images.'
            )
            ->addOption(
                WarmUpCommand::OPTION_WITHOUT_MODULES_CACHE,
                null,
                InputOption::VALUE_NONE,
                'Deactivates the generation of module cache files. E.g. tmp/modules/1/absolute_module_paths.txt'
            )
            ->addOption(
                WarmUpCommand::OPTION_WITHOUT_LANGUAGE,
                null,
                InputOption::VALUE_NONE,
                'Deactivates the generation of language cache files. E.g. tmp/oxeec_langcache_0_0_1_apex_default.txt'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->compileTemplates($input, $output);
        $this->generatePictures($input, $output);
        $this->generateModuleCache($input, $output);
        $this->generateLanguageCache($input, $output);

        return Command::SUCCESS;
    }

    protected function compileTemplates(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_TEMPLATES)) {
            $output->write('Compiling templates...');
            $this->buildTemplateCache->execute();
            $output->writeln(' done!');
        }
    }

    protected function generatePictures(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_PICTURES)) {
            $output->write('Generating pictures...');
            $this->generatePictures->execute();
            $output->writeln(' done!');
        }
    }

    protected function generateModuleCache(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_MODULES_CACHE)) {
            $output->write('Generating module cache files...');
            $this->moduleCache->execute();
            $output->writeln(' done!');
        }
    }

    protected function generateLanguageCache(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_LANGUAGE)) {
            $output->write('Generating language cache files...');
            $this->languageCache->execute();
            $output->writeln(' done!');
        }
    }
}

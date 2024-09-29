<?php

declare(strict_types=1);

namespace MichaelKeiluweit\WarmUp\Shared\Command;

use MichaelKeiluweit\WarmUp\Cache\Service\BuildTemplateCache;
use MichaelKeiluweit\WarmUp\Cache\Service\GenerateTableMetaCache;
use MichaelKeiluweit\WarmUp\Cache\Service\LanguageCache;
use MichaelKeiluweit\WarmUp\Cache\Service\ModuleCache;
use MichaelKeiluweit\WarmUp\Picture\Service\GeneratePictures;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class WarmUpCommand extends Command
{
    private const OPTION_WITHOUT_TEMPLATES = 'without-templates';
    private const OPTION_WITHOUT_PICTURES = 'without-pictures';
    private const OPTION_WITHOUT_TABLE_META = 'without-table-meta';
    private const OPTION_WITHOUT_MODULES_CACHE = 'without-modules-cache';
    private const OPTION_WITHOUT_LANGUAGE = 'without-language';

    public function __construct(
        private readonly BuildTemplateCache $buildTemplateCache,
        private readonly GeneratePictures $generatePictures,
        private readonly GenerateTableMetaCache $generateTableMetaCache,
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
                WarmUpCommand::OPTION_WITHOUT_TABLE_META,
                null,
                InputOption::VALUE_NONE,
                'Deactivates the generation of the table meta information cache.'
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
        $output->writeln('<comment>Depending on the number of objects, this may take some time.</comment>');

        $this->generateModuleCache($input, $output);
        $this->generateTableMetaCache($input, $output);
        $this->compileTemplates($input, $output);
        $this->generatePictures($input, $output);
        $this->generateLanguageCache($input, $output);

        return Command::SUCCESS;
    }

    protected function generateModuleCache(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_MODULES_CACHE)) {
            $progressIndicator = new ProgressIndicator($output);
            $progressIndicator->start('<info>Generating module cache files...</info>');
            $this->moduleCache->execute();
            $progressIndicator->finish('<info>Generating module cache files... done!</info>');
        }
    }

    protected function generateTableMetaCache(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_TABLE_META)) {
            $progressIndicator = new ProgressIndicator($output);
            $progressIndicator->start('<info>Generating table meta cache...</info>');
            $this->generateTableMetaCache->execute($progressIndicator);
            $progressIndicator->finish('<info>Generating table meta cache... done!</info>');
        }
    }

    protected function compileTemplates(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_TEMPLATES)) {
            $progressIndicator = new ProgressIndicator($output);
            $progressIndicator->start('<info>Compiling templates...</info>');
            $this->buildTemplateCache->execute($progressIndicator);
            $progressIndicator->finish('<info>Compiling templates... done!</info>');
        }
    }

    protected function generatePictures(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_PICTURES)) {
            $progressIndicator = new ProgressIndicator($output);
            $progressIndicator->start('<info>Generating pictures...</info>');
            $this->generatePictures->execute($progressIndicator);
            $progressIndicator->finish('<info>Generating pictures... done!</info>');
        }
    }

    protected function generateLanguageCache(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption(WarmUpCommand::OPTION_WITHOUT_LANGUAGE)) {
            $progressIndicator = new ProgressIndicator($output);
            $progressIndicator->start('<info>Generating language cache files...</info>');
            $this->languageCache->execute();
            $progressIndicator->finish('<info>Generating language cache files... done!</info>');
        }
    }
}

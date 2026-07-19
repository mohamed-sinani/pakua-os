<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PakuaOS\UI\Theme;
use PakuaOS\Downloader\Downloader;

final class DownloadCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('download')
            ->setDescription('Download a file by URL')
            ->setAliases(['dl', 'get'])
            ->addArgument('url', InputArgument::REQUIRED, 'Download URL')
            ->addArgument('name', InputArgument::OPTIONAL, 'Output filename')
            ->addOption('os', null, InputOption::VALUE_NONE, 'Save to Operating Systems folder')
            ->addOption('programs', null, InputOption::VALUE_NONE, 'Save to Programs folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = $input->getArgument('url');
        $name = $input->getArgument('name') ?? basename(parse_url($url, PHP_URL_PATH) ?: 'download');

        $category = null;
        if ($input->getOption('os')) $category = 'os';
        if ($input->getOption('programs')) $category = 'programs';

        $output->writeln("\n  " . Theme::bold('Starting download...'));

        $dl = new Downloader();
        $path = $dl->download($url, $name, null, 'sha256', $category);

        if ($path) {
            $output->writeln(Theme::success("\n  Download complete!") . "\n");
            return Command::SUCCESS;
        }

        $output->writeln(Theme::error("\n  Download failed!") . "\n");
        return Command::FAILURE;
    }
}

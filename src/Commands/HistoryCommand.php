<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PakuaOS\UI\Theme;
use PakuaOS\UI\Table;
use PakuaOS\Database\Database;

final class HistoryCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('history')
            ->setDescription('Show download history')
            ->setAliases(['ls', 'list']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $db = Database::instance();
        $all = $db->getAllDownloads();

        usort($all, fn($a, $b) => ($b['created_at'] ?? '') <=> ($a['created_at'] ?? ''));
        $all = array_slice($all, 0, 20);

        $rows = [];
        foreach ($all as $row) {
            $status = match ($row['status'] ?? '') {
                'completed'  => Theme::success('✓ Ready'),
                'failed'     => Theme::error('✗ Failed'),
                'downloading'=> Theme::info('↓ Downloading'),
                'paused'     => Theme::warning('● Paused'),
                'resumable'  => Theme::warning('↻ Resumable'),
                'queued'     => Theme::dim('○ Queued'),
                default      => Theme::info($row['status'] ?? '?'),
            };

            $size = $row['file_size'] ?? ($row['downloaded'] ?? 0);
            $sizeStr = $size > 0 ? \PakuaOS\UI\ProgressBar::formatBytes($size) : '—';

            $rows[] = [
                (string)($row['id'] ?? '?'),
                Theme::bold(mb_substr($row['name'] ?? '', 0, 34)),
                $status,
                $sizeStr,
            ];
        }

        if (empty($rows)) {
            $output->writeln("\n  " . Theme::dim('No download history.'));
            return Command::SUCCESS;
        }

        $output->writeln("\n" . Theme::bold("  Download History"));
        Table::render(['#', 'Package', 'Status', 'Size'], $rows, [5, 34, 14, 12]);
        return Command::SUCCESS;
    }
}

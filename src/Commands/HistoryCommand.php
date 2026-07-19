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
                'completed' => Theme::success('✓ Done'),
                'failed'    => Theme::error('✗ Failed'),
                'paused'    => Theme::warning('● Paused'),
                'queued'    => Theme::dim('○ Queued'),
                default     => Theme::info($row['status'] ?? '?'),
            };

            $rows[] = [
                (string)($row['id'] ?? '?'),
                Theme::dim(mb_substr($row['name'] ?? '', 0, 34)),
                Theme::dim($row['created_at'] ?? ''),
                $status,
            ];
        }

        if (empty($rows)) {
            $output->writeln("\n  " . Theme::dim('No download history.'));
            return Command::SUCCESS;
        }

        $output->writeln("\n" . Theme::bold("  Download History"));
        Table::render(['ID', 'Name', 'Date', 'Status'], $rows, [6, 35, 22, 12]);
        return Command::SUCCESS;
    }
}

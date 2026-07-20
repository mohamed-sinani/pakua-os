<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PakuaOS\UI\Theme;
use PakuaOS\UI\Table;
use PakuaOS\UI\Menu;
use PakuaOS\UI\Spinner;
use PakuaOS\UI\ProgressBar;
use PakuaOS\Search\SearchEngine;

final class SearchCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('search')
            ->setDescription('Search for operating systems or software')
            ->setAliases(['find', 's'])
            ->addArgument('query', InputArgument::OPTIONAL, 'Search query');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $input->getArgument('query') ?? '';

        echo "\n";
        echo Theme::header('SEARCH') . "\n\n";

        $spinner = new Spinner('Searching packages...');
        $spinner->start();

        $providersQueried = [];
        $engine = new SearchEngine();
        $results = $engine->search($query, function (string $provider) use (&$providersQueried, $spinner) {
            $providersQueried[] = $provider;
            $spinner->updateMessage('Searching: ' . $provider);
        });

        $spinner->stop('Found ' . count($results) . ' matching packages.');

        if (empty($results)) {
            echo "\n";
            echo "  " . Theme::errorBox("Package not found.") . "\n";
            echo "\n";
            echo "  " . Theme::dim("Suggestions:") . "\n";
            echo "  " . Theme::dim("pakua search vscode") . "\n";
            echo "  " . Theme::dim("pakua search code") . "\n";
            echo "  " . Theme::dim("pakua search visual studio code") . "\n";
            echo "\n";
            return Command::SUCCESS;
        }

        echo "\n";
        echo Theme::separator("Search Results") . "\n\n";

        $rows = [];
        foreach ($results as $i => $r) {
            $rows[] = [
                Theme::cyan((string)($i + 1)),
                Theme::bold($r['name']),
                $r['platform'],
                $r['type'],
                isset($r['asset_size']) ? ProgressBar::formatBytes($r['asset_size']) : Theme::dim('—'),
                $r['verified'] ? Theme::success('✓') : Theme::dim('—'),
            ];
        }

        Table::render(
            ['#', 'Name', 'Platform', 'Type', 'Size', 'Sec'],
            $rows,
            [4, 30, 18, 14, 10, 6]
        );

        $choice = Menu::prompt("Enter number to view details (0 to go back)");
        $idx = (int)$choice - 1;

        if ($idx >= 0 && $idx < count($results)) {
            $r = $results[$idx];
            echo "\n";
            echo Theme::separator($r['name'] . ' Details') . "\n";
            echo "  " . Theme::bold(Theme::cyan('Name')) . ':       ' . Theme::bold($r['name']) . "\n";
            echo "  " . Theme::bold(Theme::cyan('Version')) . ':    ' . ($r['version'] ?? 'latest') . "\n";
            echo "  " . Theme::bold(Theme::cyan('Platform')) . ':   ' . $r['platform'] . "\n";
            echo "  " . Theme::bold(Theme::cyan('Type')) . ':       ' . $r['type'] . "\n";
            echo "  " . Theme::bold(Theme::cyan('Source')) . ':     ' . ($r['source'] ?? '') . "\n";
            if (isset($r['publisher'])) {
                echo "  " . Theme::bold(Theme::cyan('Publisher')) . ':  ' . $r['publisher'] . "\n";
            }
            if (isset($r['asset_size'])) {
                echo "  " . Theme::bold(Theme::cyan('Size')) . ':       ' . \PakuaOS\UI\ProgressBar::formatBytes($r['asset_size']) . "\n";
            }
            echo "  " . Theme::bold(Theme::cyan('URL')) . ':        ' . $r['url'] . "\n";
            echo "  " . Theme::bold(Theme::cyan('Verified')) . ':  ' . ($r['verified'] ? Theme::success('✓ Verified') : Theme::warning('Unverified — check before installing')) . "\n";
            echo "\n";

            if (Menu::confirm("Start download?")) {
                $cat = $r['category'] ?? null;
                $dlCategory = in_array($cat, ['linux', 'windows', 'macos']) ? 'os' : 'programs';
                $dl = new \PakuaOS\Downloader\Downloader();
                $dl->download($r['url'], $r['name'] . ' ' . $r['platform'], null, 'sha256', $dlCategory);
            }
        }

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PakuaOS\UI\Theme;
use PakuaOS\Search\SearchEngine;
use PakuaOS\Downloader\Downloader;
use PakuaOS\UI\Menu;

final class MenuCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('menu')
            ->setDescription('Launch interactive menu')
            ->setHidden();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Theme::banner();

        $engine = new SearchEngine();
        $dl = new Downloader();

        while (true) {
            $choice = Menu::select('What do you want?', [
                ['label' => 'Operating Systems', 'desc' => 'Find ISOs for Windows, Linux, macOS'],
                ['label' => 'Software Setup',    'desc' => 'Find installers and packages'],
                ['label' => 'Search Everything', 'desc' => 'Search all categories at once'],
                ['label' => 'Download by URL',   'desc' => 'Direct download from any URL'],
                ['label' => 'My Downloads',      'desc' => 'View download history'],
                ['label' => 'Settings',          'desc' => 'Configure PakuaOS'],
            ], false);

            match ($choice) {
                0 => $this->handleOS($engine),
                1 => $this->handleSoftware($engine),
                2 => $this->handleSearch($engine),
                3 => $this->handleDirectDownload($dl),
                4 => $this->handleHistory(),
                5 => $this->handleSettings(),
                default => null,
            };

            if ($choice === 5) break; // Exit from settings
        }

        return Command::SUCCESS;
    }

    private function handleOS(SearchEngine $engine): void
    {
        $cat = Menu::select('Select OS Type', [
            ['label' => 'Linux',   'desc' => 'Ubuntu, Debian, Fedora, Arch, Kali, Mint...'],
            ['label' => 'Windows', 'desc' => 'Windows 11, 10, Server'],
            ['label' => 'macOS',   'desc' => 'Sequoia, Sonoma, Ventura'],
        ]);

        $category = match ($cat) {
            0 => 'linux', 1 => 'windows', 2 => 'macos', default => 'linux',
        };

        $results = $engine->searchCategory($category);
        $this->displayResults($results, 'os');
    }

    private function handleSoftware(SearchEngine $engine): void
    {
        $query = Menu::prompt('Search software');
        $results = $engine->searchCategory('software', $query);
        $this->displayResults($results, 'programs');
    }

    private function handleSearch(SearchEngine $engine): void
    {
        $query = Menu::prompt('Search everything');
        $results = $engine->search($query);
        $this->displayResults($results, null);
    }

    private function handleDirectDownload(Downloader $dl): void
    {
        $url = Menu::prompt('Enter download URL');
        $name = Menu::prompt('Filename', basename(parse_url($url, PHP_URL_PATH) ?: 'download'));
        $dl->download($url, $name);
    }

    private function handleHistory(): void
    {
        $db = \PakuaOS\Database\Database::instance();
        $all = $db->getAllDownloads();
        usort($all, fn($a, $b) => ($b['created_at'] ?? '') <=> ($a['created_at'] ?? ''));
        $all = array_slice($all, 0, 15);

        echo "\n";
        echo Theme::bold("  Download History") . "\n\n";
        $count = 0;
        foreach ($all as $row) {
            $status = match ($row['status'] ?? '') {
                'completed' => Theme::success('✓'),
                'failed'    => Theme::error('✗'),
                'paused'    => Theme::warning('●'),
                default     => Theme::dim('○'),
            };
            printf("  %s %s — %s — %s\n", $status, $row['name'] ?? '', $row['created_at'] ?? '', \PakuaOS\UI\ProgressBar::formatBytes((int)($row['file_size'] ?? 0)));
            $count++;
        }
        if ($count === 0) echo "  " . Theme::dim("No downloads yet.") . "\n";
        echo "\n";

        Menu::prompt('Press Enter to continue');
    }

    private function handleSettings(): void
    {
        $db = \PakuaOS\Database\Database::instance();

        $choice = Menu::select('Settings', [
            ['label' => 'Set download directory', 'desc' => $db->setting('download_dir', '~/Downloads')],
            ['label' => 'View stats',             'desc' => 'Total downloads, size, etc.'],
        ], true);

        match ($choice) {
            0 => function () use ($db) {
                $dir = Menu::prompt('Download directory', $db->setting('download_dir', '~/Downloads'));
                $db->setSetting('download_dir', $dir);
                echo Theme::success("\n  Download directory set to: {$dir}\n\n");
            },
            1 => function () use ($db) {
                $completed = $db->getDownloadsByStatus('completed');
                $count = count($completed);
                $totalSize = 0;
                foreach ($completed as $d) $totalSize += (int)($d['file_size'] ?? 0);
                echo "\n  " . Theme::bold("Stats") . "\n";
                echo "  Total downloads: " . Theme::cyan((string)$count) . "\n";
                echo "  Total size: " . Theme::cyan(\PakuaOS\UI\ProgressBar::formatBytes($totalSize)) . "\n\n";
            },
            null => null,
        };
    }

    private function displayResults(array $results, ?string $downloadCategory): void
    {
        if (empty($results)) {
            echo "\n  " . Theme::error('No results found.') . "\n";
            return;
        }

        $rows = [];
        foreach ($results as $i => $r) {
            $rows[] = [
                Theme::cyan((string)($i + 1)),
                Theme::bold($r['name']),
                $r['platform'],
                $r['type'],
                $r['verified'] ? Theme::success('✓') : Theme::dim('—'),
            ];
        }

        echo "\n";
        \PakuaOS\UI\Table::render(
            ['#', 'Name', 'Platform', 'Type', 'Sec'],
            $rows,
            [4, 30, 18, 14, 6]
        );

        $choice = Menu::prompt("\n  Enter number to download (0 to go back)");
        $idx = (int)$choice - 1;

        if ($idx >= 0 && $idx < count($results)) {
            $r = $results[$idx];

            echo "\n";
            echo \PakuaOS\UI\Theme::separator("Security Check") . "\n";
            echo "  \033[36mPublisher:\033[0m  " . ($r['publisher'] ?? $r['source']) . "\n";
            echo "  \033[36mSource:\033[0m     {$r['source']}\n";
            echo "  \033[36mVerified:\033[0m  " . ($r['verified'] ? Theme::success('✓ Verified') : Theme::warning('Unverified')) . "\n";

            if (Menu::confirm("\n  Start download?")) {
                $dl = new Downloader();
                $dl->download($r['url'], $r['name'] . ' ' . $r['platform'], null, 'sha256', $downloadCategory);
            }
        }
    }
}

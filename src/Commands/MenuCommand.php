<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PakuaOS\UI\Theme;
use PakuaOS\UI\Table;
use PakuaOS\UI\Menu;
use PakuaOS\UI\Spinner;
use PakuaOS\UI\ProgressBar;
use PakuaOS\Search\SearchEngine;
use PakuaOS\Downloader\Downloader;

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
        echo Theme::header('STARTING') . "\n\n";

        echo "  " . Theme::green('✔') . ' ' . Theme::dim('Connected to repositories.') . "\n\n";

        $engine = new SearchEngine();
        $dl = new Downloader();

        while (true) {
            $choice = Menu::select('What do you want?', [
                ['label' => 'Operating Systems', 'desc' => 'Find ISOs — pick distro, version, arch'],
                ['label' => 'Software Setup',    'desc' => 'Search ANY app — unlimited from web'],
                ['label' => 'Search Everything', 'desc' => 'Search all sources at once'],
                ['label' => 'Download by URL',   'desc' => 'Direct download from any URL'],
                ['label' => 'My Downloads',      'desc' => 'View download history'],
                ['label' => 'Settings',          'desc' => 'Configure PakuaOS'],
            ], false);

            match ($choice) {
                0 => $this->handleOS($engine),
                1 => $this->handleSoftware($engine),
                2 => $this->handleEverything($engine),
                3 => $this->handleDirectDownload($dl),
                4 => $this->handleHistory(),
                5 => $this->handleSettings(),
                default => null,
            };

            if ($choice === 5) break;
        }

        return Command::SUCCESS;
    }

    // ─── Operating Systems ──────────────────────────────────────────────

    private function handleOS(SearchEngine $engine): void
    {
        // Step 1: Pick OS family
        $cat = Menu::select('Select OS Family', [
            ['label' => 'Linux',   'desc' => 'Ubuntu, Debian, Fedora, Arch, Kali, Mint, openSUSE...'],
            ['label' => 'Windows', 'desc' => 'Windows 11, 10, Server'],
            ['label' => 'macOS',   'desc' => 'Sequoia, Sonoma, Ventura'],
        ]);

        $category = match ($cat) {
            0 => 'linux', 1 => 'windows', 2 => 'macos', default => 'linux',
        };

        $spinner = new Spinner('Searching ' . $category . '...');
        $spinner->start();
        $allResults = $engine->searchCategory($category, '', function (string $p) use ($spinner) {
            $spinner->updateMessage('Searching: ' . $p);
        });
        $spinner->stop('Found ' . count($allResults) . ' results.');

        // Step 2: Group by distro name
        $groups = [];
        foreach ($allResults as $r) {
            $groupName = $this->extractDistroName($r['name']);
            $groups[$groupName][] = $r;
        }

        // Step 3: Pick distro
        $distroNames = array_keys($groups);
        $distroOptions = [];
        foreach ($distroNames as $dn) {
            $count = count($groups[$dn]);
            $distroOptions[] = ['label' => $dn, 'desc' => "{$count} version(s) available"];
        }

        $distroIdx = Menu::select("Select {$category} Distribution", $distroOptions);
        if ($distroIdx === null || ($distroIdx === 0 && count($distroOptions) === 0)) return;

        $chosenDistro = $distroNames[$distroIdx] ?? null;
        if (!$chosenDistro || !isset($groups[$chosenDistro])) return;

        $distroResults = $groups[$chosenDistro];

        // Step 4: Pick version
        $versions = [];
        foreach ($distroResults as $r) {
            $ver = $r['version'] ?? 'latest';
            $versions[$ver][] = $r;
        }

        $verNames = array_keys($versions);
        if (count($verNames) > 1) {
            $verOptions = [];
            foreach ($verNames as $vn) {
                $archs = array_unique(array_map(fn($r) => $r['platform'], $versions[$vn]));
                $verOptions[] = ['label' => $vn, 'desc' => 'Arch: ' . implode(', ', $archs)];
            }
            $verOptions[] = ['label' => 'Latest', 'desc' => 'Use the newest version'];

            $verIdx = Menu::select('Select Version', $verOptions);

            if ($verIdx === count($verOptions) - 1) {
                $chosenVer = $verNames[0];
            } else {
                $chosenVer = $verNames[$verIdx] ?? $verNames[0];
            }
        } else {
            $chosenVer = $verNames[0];
        }

        $versionResults = $versions[$chosenVer];

        // Step 5: Pick architecture
        if (count($versionResults) > 1) {
            $archOptions = [];
            foreach ($versionResults as $r) {
                $archOptions[] = ['label' => $r['platform'], 'desc' => $r['type']];
            }

            $archIdx = Menu::select('Select Architecture', $archOptions);
            $final = $versionResults[$archIdx];
        } else {
            $final = $versionResults[0];
        }

        $this->showDetailsAndDownload($final, 'os');
    }

    // ─── Software ───────────────────────────────────────────────────────

    private function handleSoftware(SearchEngine $engine): void
    {
        $query = Menu::prompt('Search for any software');
        if ($query === '') return;

        echo "\n";

        $spinner = new Spinner('Searching packages...');
        $spinner->start();

        $results = $engine->searchSoftware($query, function (string $p) use ($spinner) {
            $spinner->updateMessage('Searching: ' . $p);
        });

        $spinner->stop('Found ' . count($results) . ' packages.');
        echo "\n";

        $this->displayResults($results, 'programs');
    }

    // ─── Search Everything ─────────────────────────────────────────────

    private function handleEverything(SearchEngine $engine): void
    {
        $query = Menu::prompt('Search everything');
        if ($query === '') return;

        echo "\n";

        $spinner = new Spinner('Searching all sources...');
        $spinner->start();

        $results = $engine->search($query, function (string $p) use ($spinner) {
            $spinner->updateMessage('Searching: ' . $p);
        });

        $spinner->stop('Found ' . count($results) . ' results.');
        echo "\n";

        $this->displayResults($results, null);
    }

    // ─── Direct Download ───────────────────────────────────────────────

    private function handleDirectDownload(Downloader $dl): void
    {
        $url = Menu::prompt('Enter download URL');
        if ($url === '') return;
        $name = Menu::prompt('Filename', basename(parse_url($url, PHP_URL_PATH) ?: 'download'));

        $category = Menu::confirm('Save to Operating Systems folder?') ? 'os' : 'programs';
        $dl->download($url, $name, null, 'sha256', $category);
    }

    // ─── History ───────────────────────────────────────────────────────

    private function handleHistory(): void
    {
        $db = \PakuaOS\Database\Database::instance();
        $all = $db->getAllDownloads();
        usort($all, fn($a, $b) => ($b['created_at'] ?? '') <=> ($a['created_at'] ?? ''));
        $all = array_slice($all, 0, 15);

        echo "\n";
        echo Theme::separator("Download History") . "\n\n";

        if (empty($all)) {
            echo "  " . Theme::dim("No download history.") . "\n\n";
            Menu::prompt('Press Enter to continue');
            return;
        }

        $rows = [];
        foreach ($all as $row) {
            $status = match ($row['status'] ?? '') {
                'completed' => Theme::success('completed'),
                'failed'    => Theme::error('failed'),
                'paused'    => Theme::warning('paused'),
                default     => Theme::dim('queued'),
            };
            $rows[] = [
                Theme::cyan((string)($row['id'] ?? '—')),
                Theme::bold(mb_substr($row['name'] ?? '', 0, 30)),
                $row['created_at'] ?? '',
                $status,
            ];
        }

        Table::render(
            ['ID', 'Name', 'Date', 'Status'],
            $rows,
            [5, 32, 20, 14]
        );

        echo "\n";
        Menu::prompt('Press Enter to continue');
    }

    // ─── Settings ──────────────────────────────────────────────────────

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
                echo "\n" . Theme::successBox("Download directory set to: {$dir}") . "\n\n";
            },
            1 => function () use ($db) {
                $completed = $db->getDownloadsByStatus('completed');
                $count = count($completed);
                $totalSize = 0;
                foreach ($completed as $d) $totalSize += (int)($d['file_size'] ?? 0);
                echo "\n";
                echo Theme::separator("Stats") . "\n";
                echo "  " . Theme::bold('Total downloads') . ': ' . Theme::cyan((string)$count) . "\n";
                echo "  " . Theme::bold('Total size') . ':      ' . Theme::cyan(ProgressBar::formatBytes($totalSize)) . "\n\n";
            },
            null => null,
        };
    }

    // ─── Helpers ───────────────────────────────────────────────────────

    private function extractDistroName(string $fullName): string
    {
        return preg_replace('/\s+\d.*/', '', $fullName) ?: $fullName;
    }

    private function displayResults(array $results, ?string $downloadCategory): void
    {
        if (empty($results)) {
            echo "\n  " . Theme::errorBox("Package not found.") . "\n";
            echo "\n  " . Theme::dim("Suggestions:") . "\n";
            echo "  " . Theme::dim("pakua search vscode") . "\n";
            echo "  " . Theme::dim("pakua search code") . "\n";
            echo "  " . Theme::dim("pakua search visual studio code") . "\n";
            echo "\n";
            return;
        }

        $total = count($results);
        echo Theme::dim("  Found {$total} result(s)") . "\n\n";

        // Show max 25 results
        $shown = array_slice($results, 0, 25);

        $rows = [];
        foreach ($shown as $i => $r) {
            $source = $r['provider'] ?? $r['source'] ?? '';
            $stars = isset($r['stars']) ? " ★{$r['stars']}" : '';
            $size = isset($r['asset_size']) ? ' ' . ProgressBar::formatBytes($r['asset_size']) : '';

            $rows[] = [
                Theme::cyan(str_pad((string)($i + 1), 3)),
                Theme::bold(mb_substr($r['name'], 0, 28)),
                $r['platform'],
                $r['type'],
                Theme::dim($source . $stars),
            ];
        }

        Table::render(
            ['#', 'Name', 'Platform', 'Type', 'Source'],
            $rows,
            [5, 30, 16, 16, 22]
        );

        if ($total > 25) {
            echo Theme::dim("  ... and " . ($total - 25) . " more results") . "\n";
        }

        $choice = Menu::prompt("\n  Enter number to download (0 to go back)");
        $idx = (int)$choice - 1;

        if ($idx >= 0 && $idx < count($results)) {
            $this->showDetailsAndDownload($results[$idx], $downloadCategory);
        }
    }

    private function showDetailsAndDownload(array $r, ?string $downloadCategory): void
    {
        echo "\n";
        echo Theme::separator($r['name'] ?? 'Download') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Name')) . ':       ' . Theme::bold($r['name'] ?? '') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Version')) . ':    ' . ($r['version'] ?? 'latest') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Platform')) . ':   ' . ($r['platform'] ?? '') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Type')) . ':       ' . ($r['type'] ?? '') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Source')) . ':     ' . ($r['source'] ?? '') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Publisher')) . ':  ' . ($r['publisher'] ?? $r['source'] ?? '') . "\n";
        if (!empty($r['desc'])) {
            echo "  " . Theme::bold(Theme::cyan('Info')) . ':       ' . mb_substr($r['desc'], 0, 60) . "\n";
        }
        if (isset($r['asset_size'])) {
            echo "  " . Theme::bold(Theme::cyan('Size')) . ':       ' . ProgressBar::formatBytes($r['asset_size']) . "\n";
        }
        echo "  " . Theme::bold(Theme::cyan('URL')) . ':        ' . ($r['url'] ?? '') . "\n";
        echo "  " . Theme::bold(Theme::cyan('Verified')) . ':  ' . (($r['verified'] ?? false) ? Theme::success('✓ Verified') : Theme::warning('Unverified — check before installing')) . "\n";
        echo "\n";

        if (Menu::confirm("Start download?")) {
            $dl = new Downloader();
            $name = ($r['name'] ?? 'download') . ' ' . ($r['platform'] ?? '');
            $dl->download($r['url'], $name, null, 'sha256', $downloadCategory);
        }
    }
}

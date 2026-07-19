<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PakuaOS\UI\Theme;
use PakuaOS\UI\Table;
use PakuaOS\UI\Menu;
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

        $allResults = $engine->searchCategory($category);

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
        if ($distroIdx === null || $distroIdx === 0 && count($distroOptions) === 0) return;

        $chosenDistro = $distroNames[$distroIdx] ?? null;
        if (!$chosenDistro || !isset($groups[$chosenDistro])) return;

        $distroResults = $groups[$chosenDistro];

        // Step 4: Pick version (group by version)
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

            echo "\n";
            $verIdx = Menu::select('Select Version', $verOptions);

            if ($verIdx === count($verOptions) - 1) {
                // Latest = first version in list
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

        // Step 6: Show details + confirm
        $this->showDetailsAndDownload($final, 'os');
    }

    // ─── Software (Limitless) ──────────────────────────────────────────

    private function handleSoftware(SearchEngine $engine): void
    {
        $query = Menu::prompt('Search for any software');
        if ($query === '') return;

        echo "\n  " . Theme::dim("Searching curated database + GitHub + Chocolatey + Snap Store + Web...") . "\n\n";

        $results = $engine->searchSoftware($query);

        $this->displayResults($results, 'programs');
    }

    // ─── Search Everything ─────────────────────────────────────────────

    private function handleEverything(SearchEngine $engine): void
    {
        $query = Menu::prompt('Search everything');
        if ($query === '') return;

        echo "\n  " . Theme::dim("Searching all sources...") . "\n\n";

        $results = $engine->search($query);
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

    // ─── Helpers ───────────────────────────────────────────────────────

    private function extractDistroName(string $fullName): string
    {
        // "Ubuntu 24.04.2 LTS ..." → "Ubuntu"
        // "Debian 12 ..." → "Debian"
        return preg_replace('/\s+\d.*/', '', $fullName) ?: $fullName;
    }

    private function displayResults(array $results, ?string $downloadCategory): void
    {
        if (empty($results)) {
            echo "\n  " . Theme::error('No results found.') . "\n";
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
            $size = isset($r['asset_size']) ? ' ' . \PakuaOS\UI\ProgressBar::formatBytes($r['asset_size']) : '';

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
        echo "  \033[36mName:\033[0m       " . ($r['name'] ?? '') . "\n";
        echo "  \033[36mVersion:\033[0m    " . ($r['version'] ?? 'latest') . "\n";
        echo "  \033[36mPlatform:\033[0m   " . ($r['platform'] ?? '') . "\n";
        echo "  \033[36mType:\033[0m       " . ($r['type'] ?? '') . "\n";
        echo "  \033[36mSource:\033[0m     " . ($r['source'] ?? '') . "\n";
        echo "  \033[36mPublisher:\033[0m  " . ($r['publisher'] ?? $r['source'] ?? '') . "\n";
        if (!empty($r['desc'])) {
            echo "  \033[36mInfo:\033[0m       " . mb_substr($r['desc'], 0, 60) . "\n";
        }
        echo "  \033[36mURL:\033[0m        " . ($r['url'] ?? '') . "\n";
        echo "  \033[36mVerified:\033[0m  " . (($r['verified'] ?? false) ? Theme::success('✓ Verified') : Theme::warning('Unverified — check before installing')) . "\n";
        echo "\n";

        if (Menu::confirm("  Start download?")) {
            $dl = new Downloader();
            $name = ($r['name'] ?? 'download') . ' ' . ($r['platform'] ?? '');
            $dl->download($r['url'], $name, null, 'sha256', $downloadCategory);
        }
    }
}

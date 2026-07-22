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

        $this->detectOrphanedDownloads();

        $engine = new SearchEngine();
        $dl = new Downloader();

        while (true) {
            $choice = Menu::select('What do you want?', [
                ['label' => 'Operating Systems', 'desc' => 'Find ISOs — pick distro, version, arch'],
                ['label' => 'Download by URL',   'desc' => 'Direct download from any URL'],
                ['label' => 'My Downloads',      'desc' => 'View download history'],
                ['label' => 'Settings',          'desc' => 'Configure PakuaOS'],
            ], false);

            match ($choice) {
                0 => $this->handleOS($engine),
                1 => $this->handleDirectDownload($dl),
                2 => $this->handleHistory(),
                3 => $this->handleSettings(),
                default => null,
            };

            if ($choice === 3) break;
        }

        return Command::SUCCESS;
    }

    // ─── Operating Systems ──────────────────────────────────────────────

    private function handleOS(SearchEngine $engine): void
    {
        // Step 1: Pick OS family
        while (true) {
            $cat = Menu::select('Select OS Family', [
                ['label' => 'Linux',   'desc' => 'Ubuntu, Debian, Fedora, Arch, Kali, Mint, openSUSE...'],
                ['label' => 'Windows', 'desc' => 'Windows 11, 10, Server'],
                ['label' => 'macOS',   'desc' => 'Sequoia, Sonoma, Ventura'],
            ]);

            if ($cat === null) return;

            $category = match ($cat) {
                0 => 'linux', 1 => 'windows', 2 => 'macos', default => 'linux',
            };

            $spinner = new Spinner('Searching ' . $category . '...');
            $spinner->start();
            $allResults = $engine->searchCategory('operating_systems', '', function (string $p) use ($spinner) {
                $spinner->updateMessage('Searching: ' . $p);
            });
            $allResults = array_values(array_filter($allResults, fn($r) => ($r['category'] ?? '') === $category));
            $spinner->stop('Found ' . count($allResults) . ' results.');

            // Step 2: Group by distro name
            $groups = [];
            $groupLabels = [];
            $groupDescs = [];
            foreach ($allResults as $r) {
                $groupName = !empty($r['distro_label']) ? $r['distro_label'] : $this->extractDistroName($r['name']);
                $groups[$groupName][] = $r;
                if (!empty($r['distro_label'])) $groupLabels[$groupName] = $r['distro_label'];
                if (!empty($r['distro_desc']))  $groupDescs[$groupName]  = $r['distro_desc'];
            }

            // Step 3: Pick distro
            while (true) {
                $distroNames = array_keys($groups);
                $distroOptions = [];
                foreach ($distroNames as $dn) {
                    $count = count($groups[$dn]);
                    $label = $groupLabels[$dn] ?? $dn;
                    $desc  = $groupDescs[$dn]  ?? "{$count} version(s) available";
                    $distroOptions[] = ['label' => $label, 'desc' => $desc];
                }

                $distroIdx = Menu::select("Select {$category} Distribution", $distroOptions);
                if ($distroIdx === null) break; // back to OS family

                $chosenDistro = $distroNames[$distroIdx] ?? null;
                if (!$chosenDistro || !isset($groups[$chosenDistro])) break;

                $distroResults = $groups[$chosenDistro];

                // Step 4: Pick version
                $versions = [];
                foreach ($distroResults as $r) {
                    $ver = $r['version'] ?? 'latest';
                    $versions[$ver][] = $r;
                }

                $verNames = array_keys($versions);

                while (true) {
                    if (count($verNames) > 1) {
                        $verOptions = [];
                        foreach ($verNames as $vn) {
                            $archs = array_unique(array_map(fn($r) => $r['platform'], $versions[$vn]));
                            $verOptions[] = ['label' => $vn, 'desc' => 'Arch: ' . implode(', ', $archs)];
                        }
                        $verOptions[] = ['label' => 'Latest', 'desc' => 'Use the newest version'];

                        $verIdx = Menu::select('Select Version', $verOptions);
                        if ($verIdx === null) break; // back to distro

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
                    $final = $this->pickArchitecture($versionResults);
                    if ($final === null) break; // back to version

                    $this->showDetailsAndDownload($final, 'os');
                    break; // done, exit version loop
                }
            }
        }
    }

    // ─── Direct Download ───────────────────────────────────────────────

    private function handleDirectDownload(Downloader $dl): void
    {
        $url = Menu::prompt('Enter download URL');
        if ($url === '') return;
        $name = Menu::prompt('Filename', basename(parse_url($url, PHP_URL_PATH) ?: 'download'));

        $dl->download($url, $name, null, 'sha256', 'os');
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
                'completed'  => Theme::success('✓ Ready'),
                'failed'     => Theme::error('✗ Failed'),
                'downloading'=> Theme::info('↓ Downloading'),
                'paused'     => Theme::warning('● Paused'),
                'resumable'  => Theme::warning('↻ Resumable'),
                default      => Theme::dim('○ Queued'),
            };

            if (in_array($row['status'] ?? '', ['resumable', 'downloading', 'paused'])) {
                $fileSize = (int)($row['file_size'] ?? 0);
                $downloaded = (int)($row['downloaded'] ?? 0);
                if ($fileSize > 0 && $downloaded > 0) {
                    $pct = min((int)(($downloaded / $fileSize) * 100), 99);
                    $status = Theme::warning("↓ {$pct}%");
                } elseif ($downloaded > 0) {
                    $status = Theme::warning('↓ ' . \PakuaOS\UI\ProgressBar::formatBytes($downloaded));
                }
            }

            $size = $row['file_size'] ?? ($row['downloaded'] ?? 0);
            $sizeStr = $size > 0 ? \PakuaOS\UI\ProgressBar::formatBytes($size) : '—';
            $rows[] = [
                Theme::cyan((string)($row['id'] ?? '—')),
                Theme::bold(mb_substr($row['name'] ?? '', 0, 30)),
                $status,
                $sizeStr,
            ];
        }

        Table::render(
            ['#', 'Package', 'Status', 'Size'],
            $rows,
            [5, 34, 14, 12]
        );

        $resumable = array_values(array_filter($all, fn($d) => in_array($d['status'] ?? '', ['resumable', 'downloading'])));
        if (!empty($resumable)) {
            echo "\n";
            $idx = Menu::select('Resume Download', array_map(fn($d) => [
                'label' => mb_substr($d['name'] ?? '', 0, 36),
                'desc' => 'Continue downloading',
            ], $resumable), true);

            if ($idx !== null) {
                $dlRec = $resumable[$idx];
                $dl = new Downloader();
                $dl->download(
                    $dlRec['url'],
                    $dlRec['name'],
                    $dlRec['hash_value'] ?: null,
                    $dlRec['hash_type'] ?: 'sha256',
                    $dlRec['category'] ?: null
                );
            }
        } else {
            echo "\n";
            Menu::prompt('Press Enter to continue');
        }
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

    // ─── Architecture Detection ─────────────────────────────────────────

    private function detectArch(): string
    {
        $machine = php_uname('m');
        return match (true) {
            str_starts_with($machine, 'x86_64'), str_starts_with($machine, 'amd64') => 'x64',
            str_starts_with($machine, 'aarch64'), str_starts_with($machine, 'arm64') => 'arm64',
            str_starts_with($machine, 'armv7l') => 'armhf',
            str_contains($machine, 'i686'), str_contains($machine, 'i386') => 'i386',
            default => $machine,
        };
    }

    private function archLabel(string $arch): string
    {
        return match ($arch) {
            'amd64', 'x64', 'x86_64' => 'x86_64 (Intel / AMD)',
            'arm64', 'aarch64'       => 'ARM64 (Apple Silicon / Raspberry Pi 4+)',
            'armhf'                  => 'ARM (Raspberry Pi 2/3)',
            'i386'                   => 'x86 (32-bit)',
            default                  => $arch,
        };
    }

    private function resolveArch(array $versionResults, string $targetArch): ?array
    {
        $aliases = [
            'x64'     => ['amd64', 'x64', 'x86_64'],
            'amd64'   => ['amd64', 'x64', 'x86_64'],
            'x86_64'  => ['amd64', 'x64', 'x86_64'],
            'arm64'   => ['arm64', 'aarch64'],
            'aarch64' => ['arm64', 'aarch64'],
            'armhf'   => ['armhf'],
            'i386'    => ['i386'],
        ];
        $candidates = $aliases[$targetArch] ?? [$targetArch];

        foreach ($versionResults as $r) {
            $p = strtolower($r['platform'] ?? '');
            if (in_array($p, $candidates)) return $r;
        }
        return null;
    }

    private function pickArchitecture(array $versionResults): ?array
    {
        if (count($versionResults) === 1) return $versionResults[0];

        $detected = $this->detectArch();

        $target = Menu::select('Architecture', [
            ['label' => 'This PC',   'desc' => 'Auto-detect: ' . $this->archLabel($detected)],
            ['label' => 'Other PC',  'desc' => 'Choose architecture manually'],
        ]);

        if ($target === null) return null;

        if ($target === 0) {
            $match = $this->resolveArch($versionResults, $detected);
            if ($match) return $match;
            echo "\n  " . Theme::warning("No match for {$detected}, showing all options...") . "\n\n";
        }

        $archOptions = [];
        foreach ($versionResults as $r) {
            $archOptions[] = ['label' => $this->archLabel($r['platform']), 'desc' => $r['type']];
        }
        $archIdx = Menu::select('Select Architecture', $archOptions);
        if ($archIdx === null) return null;
        return $versionResults[$archIdx];
    }

    // ─── Helpers ───────────────────────────────────────────────────────

    private function extractDistroName(string $fullName): string
    {
        return preg_replace('/\s+\d.*/', '', $fullName) ?: $fullName;
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
            $fallbackUrls = $r['fallback_urls'] ?? [];
            $dl->download($r['url'], $name, null, 'sha256', $downloadCategory, $fallbackUrls);
        }
    }

    private function detectOrphanedDownloads(): void
    {
        $db = \PakuaOS\Database\Database::instance();
        $resumable = $db->getDownloadsByStatus('resumable');
        $downloading = $db->getDownloadsByStatus('downloading');

        $all = array_merge($resumable, $downloading);
        $orphans = [];
        foreach ($all as $dl) {
            $partPath = ($dl['file_path'] ?? '') . '.part';
            $filePath = $dl['file_path'] ?? '';
            if (file_exists($partPath) || file_exists($filePath . '.part')) {
                $orphans[] = $dl;
            } elseif (file_exists($filePath)) {
                $db->updateDownload($dl['id'], ['status' => 'completed']);
            } else {
                $db->updateDownload($dl['id'], ['status' => 'failed']);
            }
        }

        if (empty($orphans)) return;

        echo Theme::warning("⚡ Found " . count($orphans) . " interrupted download(s):") . "\n\n";

        foreach ($orphans as $i => $dl) {
            $partPath = $dl['file_path'] ?? '';
            if (!file_exists($partPath)) $partPath .= '.part';
            $partialSize = file_exists($partPath) ? filesize($partPath) : 0;
            echo "  " . Theme::cyan((string)($i + 1)) . ". " . Theme::bold($dl['name'] ?? 'unknown');
            echo " — " . Theme::dim(ProgressBar::formatBytes($partialSize) . " downloaded");
            echo " — " . Theme::dim($dl['url'] ?? '') . "\n";
        }

        echo "\n";
        if (Menu::confirm("Resume these downloads?")) {
            $dlEngine = new Downloader();
            foreach ($orphans as $dl) {
                echo "\n";
                $dlEngine->download(
                    $dl['url'],
                    $dl['name'],
                    $dl['hash_value'] ?: null,
                    $dl['hash_type'] ?: 'sha256',
                    $dl['category'] ?: null
                );
            }
        } else {
            echo "  " . Theme::dim("Skipped. Downloads remain resumable.") . "\n\n";
        }
    }
}

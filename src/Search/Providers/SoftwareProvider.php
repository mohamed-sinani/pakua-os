<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class SoftwareProvider implements Provider
{
    private array $software = [
        // Browsers
        'firefox' => [
            'name' => 'Mozilla Firefox', 'category' => 'browser',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://download.mozilla.org/?product=firefox-latest&os=win64&lang=en-US',
                    'source' => 'Mozilla Official', 'verified' => true, 'publisher' => 'Mozilla Foundation',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://download.mozilla.org/?product=firefox-latest&os=linux64&lang=en-US',
                    'source' => 'Mozilla Official', 'verified' => true, 'publisher' => 'Mozilla Foundation',
                ],
                'linux-arm64' => [
                    'platform' => 'Linux ARM64', 'type' => 'Package (.deb)',
                    'url' => 'https://download.mozilla.org/?product=firefox-latest&os=linux64&lang=en-US',
                    'source' => 'Mozilla Official', 'verified' => true, 'publisher' => 'Mozilla Foundation',
                ],
                'macos' => [
                    'platform' => 'macOS Universal', 'type' => 'DMG',
                    'url' => 'https://download.mozilla.org/?product=firefox-latest&os=osx&lang=en-US',
                    'source' => 'Mozilla Official', 'verified' => true, 'publisher' => 'Mozilla Foundation',
                ],
            ],
        ],
        'chrome' => [
            'name' => 'Google Chrome', 'category' => 'browser',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://dl.google.com/chrome/install/latest/chrome_installer.exe',
                    'source' => 'Google Official', 'verified' => true, 'publisher' => 'Google LLC',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb',
                    'source' => 'Google Official', 'verified' => true, 'publisher' => 'Google LLC',
                ],
                'macos' => [
                    'platform' => 'macOS', 'type' => 'DMG',
                    'url' => 'https://dl.google.com/chrome/mac/universal/GGRO/googlechrome.dmg',
                    'source' => 'Google Official', 'verified' => true, 'publisher' => 'Google LLC',
                ],
            ],
        ],
        'edge' => [
            'name' => 'Microsoft Edge', 'category' => 'browser',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://msedge.sf.dl.delivery.mp.microsoft.com/filestreamingservice/files/12382b2b-0255-401c-8e45-46cd2a84446e/MicrosoftEdgeEnterpriseX64.exe',
                    'source' => 'Microsoft Official', 'verified' => true, 'publisher' => 'Microsoft Corporation',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://packages.microsoft.com/repos/edge/pool/main/m/microsoft-edge-stable/microsoft-edge-stable_131.0.2903.86-1_amd64.deb',
                    'source' => 'Microsoft Official', 'verified' => true, 'publisher' => 'Microsoft Corporation',
                ],
            ],
        ],
        'brave' => [
            'name' => 'Brave Browser', 'category' => 'browser',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://brave-download-1.s3.brave.com/BraveBrowserSetup.exe',
                    'source' => 'Brave Official', 'verified' => true, 'publisher' => 'Brave Software',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://brave-browser-apt-release.s3.brave.com/brave-browser-release_amd64.deb',
                    'source' => 'Brave Official', 'verified' => true, 'publisher' => 'Brave Software',
                ],
            ],
        ],
        // Development
        'vscode' => [
            'name' => 'Visual Studio Code', 'category' => 'development',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-archive',
                    'source' => 'Microsoft Official', 'verified' => true, 'publisher' => 'Microsoft Corporation',
                ],
                'linux-x64-deb' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://code.visualstudio.com/sha/download?build=stable&os=linux-deb-x64',
                    'source' => 'Microsoft Official', 'verified' => true, 'publisher' => 'Microsoft Corporation',
                ],
                'linux-x64-rpm' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.rpm)',
                    'url' => 'https://code.visualstudio.com/sha/download?build=stable&os=linux-rpm-x64',
                    'source' => 'Microsoft Official', 'verified' => true, 'publisher' => 'Microsoft Corporation',
                ],
                'macos-arm64' => [
                    'platform' => 'macOS ARM64', 'type' => 'DMG',
                    'url' => 'https://code.visualstudio.com/sha/download?build=stable&os=darwin-arm64',
                    'source' => 'Microsoft Official', 'verified' => true, 'publisher' => 'Microsoft Corporation',
                ],
            ],
        ],
        'androidstudio' => [
            'name' => 'Android Studio', 'category' => 'development',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://redirector.gvt1.com/edgedl/android/studio/install/2024.3.1.14/android-studio-2024.3.1.14-windows.exe',
                    'source' => 'Google Official', 'verified' => true, 'publisher' => 'Google LLC',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.tar.gz)',
                    'url' => 'https://redirector.gvt1.com/edgedl/android/stide/2024.3.1.14/android-studio-2024.3.1.14-linux.tar.gz',
                    'source' => 'Google Official', 'verified' => true, 'publisher' => 'Google LLC',
                ],
                'macos-arm64' => [
                    'platform' => 'macOS ARM64', 'type' => 'DMG',
                    'url' => 'https://redirector.gvt1.com/edgedl/android/studio/install/2024.3.1.14/android-studio-2024.3.1.14-mac_arm.dmg',
                    'source' => 'Google Official', 'verified' => true, 'publisher' => 'Google LLC',
                ],
            ],
        ],
        'docker' => [
            'name' => 'Docker Desktop', 'category' => 'development',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://desktop.docker.com/win/main/amd64/Docker%20Desktop%20Installer.exe',
                    'source' => 'Docker Official', 'verified' => true, 'publisher' => 'Docker Inc.',
                ],
                'macos-arm64' => [
                    'platform' => 'macOS ARM64', 'type' => 'DMG',
                    'url' => 'https://desktop.docker.com/mac/main/arm64/Docker.dmg',
                    'source' => 'Docker Official', 'verified' => true, 'publisher' => 'Docker Inc.',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://download.docker.com/linux/ubuntu/dists/noble/pool/stable/amd64/docker-desktop-amd64.deb',
                    'source' => 'Docker Official', 'verified' => true, 'publisher' => 'Docker Inc.',
                ],
            ],
        ],
        'git' => [
            'name' => 'Git', 'category' => 'development',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://github.com/git-for-windows/git/releases/latest/download/Git-2.47.1-64-bit.exe',
                    'source' => 'GitHub Release', 'verified' => true, 'publisher' => 'Git Community',
                ],
                'linux-x64-deb' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://github.com/git-for-windows/git/releases/latest/download/git_2.47.1-1_amd64.deb',
                    'source' => 'GitHub Release', 'verified' => true, 'publisher' => 'Git Community',
                ],
                'macos' => [
                    'platform' => 'macOS', 'type' => 'Package (.pkg)',
                    'url' => 'https://sourceforge.net/projects/git-osx-installer/files/git-2.47.1-intel-universal-mavericks.dmg/download',
                    'source' => 'SourceForge', 'verified' => true, 'publisher' => 'Git Community',
                ],
            ],
        ],
        'nodejs' => [
            'name' => 'Node.js', 'category' => 'development',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'MSI Installer',
                    'url' => 'https://nodejs.org/dist/v22.12.0/node-v22.12.0-x64.msi',
                    'source' => 'Node.js Official', 'verified' => true, 'publisher' => 'Node.js Foundation',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Binary (.tar.xz)',
                    'url' => 'https://nodejs.org/dist/v22.12.0/node-v22.12.0-linux-x64.tar.xz',
                    'source' => 'Node.js Official', 'verified' => true, 'publisher' => 'Node.js Foundation',
                ],
                'macos-arm64' => [
                    'platform' => 'macOS ARM64', 'type' => 'Package (.pkg)',
                    'url' => 'https://nodejs.org/dist/v22.12.0/node-v22.12.0.pkg',
                    'source' => 'Node.js Official', 'verified' => true, 'publisher' => 'Node.js Foundation',
                ],
            ],
        ],
        // Security
        'wireshark' => [
            'name' => 'Wireshark', 'category' => 'security',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://www.wireshark.org/download/win64/Wireshark-4.4.3-x64.exe',
                    'source' => 'Wireshark Official', 'verified' => true, 'publisher' => 'Wireshark Foundation',
                ],
                'linux-x64-deb' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://www.wireshark.org/download/src/wireshark-4.4.3.tar.xz',
                    'source' => 'Wireshark Official', 'verified' => true, 'publisher' => 'Wireshark Foundation',
                ],
            ],
        ],
        'nmap' => [
            'name' => 'Nmap', 'category' => 'security',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://nmap.org/dist/nmap-7.95-setup.exe',
                    'source' => 'Nmap Official', 'verified' => true, 'publisher' => 'Nmap Project',
                ],
                'linux-x64' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://nmap.org/dist/nmap-7.95-1.x86_64.rpm',
                    'source' => 'Nmap Official', 'verified' => true, 'publisher' => 'Nmap Project',
                ],
            ],
        ],
        // Productivity
        'libreoffice' => [
            'name' => 'LibreOffice', 'category' => 'productivity',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'MSI Installer',
                    'url' => 'https://download.documentfoundation.org/libreoffice/stable/25.2.2/win/x86_64/LibreOffice_25.2.2_Win_x86-64.msi',
                    'source' => 'LibreOffice Official', 'verified' => true, 'publisher' => 'The Document Foundation',
                ],
                'linux-x64-deb' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://download.documentfoundation.org/libreoffice/stable/25.2.2/deb/x86_64/LibreOffice_25.2.2_Linux_x86-64_deb.tar.gz',
                    'source' => 'LibreOffice Official', 'verified' => true, 'publisher' => 'The Document Foundation',
                ],
                'macos' => [
                    'platform' => 'macOS', 'type' => 'DMG',
                    'url' => 'https://download.documentfoundation.org/libreoffice/stable/25.2.2/mac/x86_64/LibreOffice_25.2.2_MacOS_x86-64.dmg',
                    'source' => 'LibreOffice Official', 'verified' => true, 'publisher' => 'The Document Foundation',
                ],
            ],
        ],
        // Utilities
        'vlc' => [
            'name' => 'VLC Media Player', 'category' => 'utilities',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://get.videolan.org/vlc/3.0.21/win64/vlc-3.0.21-win64.exe',
                    'source' => 'VideoLAN Official', 'verified' => true, 'publisher' => 'VideoLAN',
                ],
                'linux-x64-deb' => [
                    'platform' => 'Linux x64', 'type' => 'Package (.deb)',
                    'url' => 'https://download.videolan.org/pub/videolan/vlc/3.0.21/linux64/vlc-3.0.21-linux-x64.tar.xz',
                    'source' => 'VideoLAN Official', 'verified' => true, 'publisher' => 'VideoLAN',
                ],
            ],
        ],
        '7zip' => [
            'name' => '7-Zip', 'category' => 'utilities',
            'versions' => [
                'windows-x64' => [
                    'platform' => 'Windows x64', 'type' => 'Installer (.exe)',
                    'url' => 'https://www.7-zip.org/a/7z2409-x64.exe',
                    'source' => '7-Zip Official', 'verified' => true, 'publisher' => 'Igor Pavlov',
                ],
            ],
        ],
    ];

    public function getName(): string { return 'Software'; }
    public function getCategory(): string { return 'software'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $results = [];
        $query = strtolower($query);

        foreach ($this->software as $key => $app) {
            if ($query !== '' && !str_contains($key, $query) && !str_contains(strtolower($app['name']), $query)) {
                continue;
            }

            foreach ($app['versions'] as $platformKey => $info) {
                $results[] = [
                    'name'      => $app['name'],
                    'version'   => $platformKey,
                    'platform'  => $info['platform'],
                    'type'      => $info['type'],
                    'url'       => $info['url'],
                    'source'    => $info['source'],
                    'verified'  => $info['verified'],
                    'hash_type' => null,
                    'category'  => $app['category'],
                    'provider'  => $this->getName(),
                    'publisher' => $info['publisher'] ?? 'Unknown',
                ];
            }
        }
        return $results;
    }
}

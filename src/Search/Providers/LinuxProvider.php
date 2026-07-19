<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class LinuxProvider implements Provider
{
    private array $distros = [
        'ubuntu' => [
            'name' => 'Ubuntu',
            'versions' => [
                '24.04.2 LTS "Noble Numbat"' => [
                    'architectures' => ['amd64', 'arm64', 'armhf'],
                    'types' => ['Desktop', 'Server', 'Minimal'],
                    'url_pattern' => 'https://releases.ubuntu.com/{version}/ubuntu-{version}-desktop-amd64.iso',
                    'source' => 'Official Ubuntu Mirror',
                    'verified' => true,
                ],
                '22.04.5 LTS "Jammy Jellyfish"' => [
                    'architectures' => ['amd64', 'arm64', 'armhf'],
                    'types' => ['Desktop', 'Server'],
                    'url_pattern' => 'https://releases.ubuntu.com/22.04/ubuntu-22.04.5-desktop-amd64.iso',
                    'source' => 'Official Ubuntu Mirror',
                    'verified' => true,
                ],
            ],
        ],
        'debian' => [
            'name' => 'Debian',
            'versions' => [
                'Debian 12 "Bookworm"' => [
                    'architectures' => ['amd64', 'arm64', 'armhf', 'i386'],
                    'types' => ['Netinst', 'DVD', 'Live'],
                    'url_pattern' => 'https://cdimage.debian.org/debian-cd/current/amd64/iso-cd/debian-12.11.0-amd64-netinst.iso',
                    'source' => 'Official Debian Mirror',
                    'verified' => true,
                ],
            ],
        ],
        'fedora' => [
            'name' => 'Fedora',
            'versions' => [
                'Fedora 42 Workstation' => [
                    'architectures' => ['x86_64', 'aarch64'],
                    'types' => ['Workstation', 'Server', 'Spin'],
                    'url_pattern' => 'https://download.fedoraproject.org/pub/fedora/linux/releases/42/Workstation/x86_64/iso/Fedora-Workstation-x86_64-42.iso',
                    'source' => 'Official Fedora Mirror',
                    'verified' => true,
                ],
                'Fedora 41 Workstation' => [
                    'architectures' => ['x86_64', 'aarch64'],
                    'types' => ['Workstation', 'Server'],
                    'url_pattern' => 'https://download.fedoraproject.org/pub/fedora/linux/releases/41/Workstation/x86_64/iso/Fedora-Workstation-x86_64-41.iso',
                    'source' => 'Official Fedora Mirror',
                    'verified' => true,
                ],
            ],
        ],
        'arch' => [
            'name' => 'Arch Linux',
            'versions' => [
                'Arch Linux (Latest)' => [
                    'architectures' => ['x86_64'],
                    'types' => ['Base ISO', 'Base (no drivers)'],
                    'url_pattern' => 'https://geo.mirror.pkgbuild.com/iso/latest/archlinux-x86_64.iso',
                    'source' => 'Official Arch Linux Mirror',
                    'verified' => true,
                ],
            ],
        ],
        'kali' => [
            'name' => 'Kali Linux',
            'versions' => [
                'Kali 2025.1' => [
                    'architectures' => ['amd64', 'arm64'],
                    'types' => ['Installer', 'NetInstaller', 'Live'],
                    'url_pattern' => 'https://cdimage.kali.org/kali-2025.1/kali-linux-2025.1-installer-amd64.iso',
                    'source' => 'Official Kali Mirror',
                    'verified' => true,
                ],
            ],
        ],
        'mint' => [
            'name' => 'Linux Mint',
            'versions' => [
                'Linux Mint 22.1 "Xia"' => [
                    'architectures' => ['amd64'],
                    'types' => ['Cinnamon', 'MATE', 'Xfce'],
                    'url_pattern' => 'https://mirror.cs.uchicago.edu/linuxmint-cd/stable/22.1/linuxmint-22.1-cinnamon-64bit.iso',
                    'source' => 'Official Linux Mint Mirror',
                    'verified' => true,
                ],
            ],
        ],
        'opensuse' => [
            'name' => 'openSUSE',
            'versions' => [
                'openSUSE Leap 15.6' => [
                    'architectures' => ['x86_64', 'aarch64'],
                    'types' => ['DVD', 'Network'],
                    'url_pattern' => 'https://download.opensuse.org/distribution/leap/15.6/iso/openSUSE-Leap-15.6-DVD-x86_64-Media.iso',
                    'source' => 'Official openSUSE Mirror',
                    'verified' => true,
                ],
            ],
        ],
    ];

    public function getName(): string { return 'Linux Distributions'; }
    public function getCategory(): string { return 'operating_systems'; }

    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $results = [];
        $query = strtolower($query);

        foreach ($this->distros as $key => $distro) {
            if ($query === '' || str_contains($key, $query) || str_contains(strtolower($distro['name']), $query)) {
                foreach ($distro['versions'] as $version => $info) {
                    foreach ($info['architectures'] as $arch) {
                        $results[] = [
                            'name'        => $distro['name'] . ' ' . $version,
                            'version'     => $version,
                            'platform'    => $arch,
                            'type'        => implode(' / ', $info['types']),
                            'url'         => $info['url_pattern'],
                            'source'      => $info['source'],
                            'verified'    => $info['verified'],
                            'hash_type'   => 'SHA256',
                            'category'    => 'linux',
                            'provider'    => $this->getName(),
                        ];
                    }
                }
            }
        }
        return $results;
    }
}

<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class MacOSProvider implements Provider
{
    public function getName(): string { return 'macOS'; }
    public function getCategory(): string { return 'operating_systems'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $versions = [
            [
                'name' => 'macOS Sequoia 15',
                'architectures' => ['arm64', 'x64'],
                'source' => 'Mac App Store',
            ],
            [
                'name' => 'macOS Sonoma 14',
                'architectures' => ['arm64', 'x64'],
                'source' => 'Mac App Store',
            ],
            [
                'name' => 'macOS Ventura 13',
                'architectures' => ['arm64', 'x64'],
                'source' => 'Mac App Store',
            ],
        ];

        $results = [];
        $query = strtolower($query);

        foreach ($versions as $v) {
            if ($query !== '' && !str_contains(strtolower($v['name']), $query)) continue;
            foreach ($v['architectures'] as $arch) {
                $results[] = [
                    'name'      => $v['name'],
                    'version'   => $v['name'],
                    'platform'  => $arch === 'arm64' ? 'Apple Silicon (ARM64)' : 'Intel (x64)',
                    'type'      => 'IPSW Restore',
                    'url'       => 'https://support.apple.com/guide/mac-help/',
                    'source'    => $v['source'],
                    'verified'  => false,
                    'manual'    => true,
                    'desc'      => 'Distributed via the Mac App Store — requires an Apple ID; PakuaOS cannot fetch IPSW files directly.',
                    'hash_type' => 'SHA256',
                    'category'  => 'macos',
                    'provider'  => $this->getName(),
                ];
            }
        }
        return $results;
    }
}

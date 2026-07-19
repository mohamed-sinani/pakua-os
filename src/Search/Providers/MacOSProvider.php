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
                'source' => 'Apple Official',
                'verified' => true,
            ],
            [
                'name' => 'macOS Sonoma 14',
                'architectures' => ['arm64', 'x64'],
                'source' => 'Apple Official',
                'verified' => true,
            ],
            [
                'name' => 'macOS Ventura 13',
                'architectures' => ['arm64', 'x64'],
                'source' => 'Apple Official',
                'verified' => true,
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
                    'url'       => 'https://developer.apple.com/services-account/QH2U3MM9CB/downloadws/listData.action',
                    'source'    => $v['source'],
                    'verified'  => $v['verified'],
                    'hash_type' => 'SHA256',
                    'category'  => 'macos',
                    'provider'  => $this->getName(),
                ];
            }
        }
        return $results;
    }
}

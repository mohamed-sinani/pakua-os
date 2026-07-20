<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class WindowsProvider implements Provider
{
    public function getName(): string { return 'Windows'; }
    public function getCategory(): string { return 'operating_systems'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $query = strtolower($query);
        $versions = [
            [
                'name'  => 'Windows 11 25H2',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/dbazure/888969d5-f34g-4e03-ac9d-1f9786c66749/26200.6584.250915-1905.25h2_ge_release_svc_refresh_CLIENT_CONSUMER_x64FRE_en-us.iso',
                'size'  => '7.2 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
                'distro_label' => 'Windows desktop',
                'distro_desc'  => 'For laptop and desktops',
                'fallback_urls' => [
                    'https://archive.org/download/win1125h2_26200.4946/en-us_windows_11_consumer_editions_version_25h2_updated_2025_x64_dvd.iso',
                    'https://archive.org/download/win1125h2_26200.4946/en-us_windows_11_consumer_editions_version_25h2_x64_dvd.iso',
                ],
            ],
            [
                'name'  => 'Windows 11 23H2',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/akfm/medias/MBF2.191029.1546-23H2/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Win11_23H2_English_x64.iso',
                'size'  => '6.3 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
                'distro_label' => 'Windows desktop',
                'distro_desc'  => 'For laptop and desktops',
                'fallback_urls' => [
                    'https://archive.org/download/win-11-23h2/Win11_23H2_English_x64.iso',
                ],
            ],
            [
                'name'  => 'Windows 10 22H2',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/dbazure/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Win10_22H2_English_x64.iso',
                'size'  => '5.8 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
                'distro_label' => 'Windows desktop',
                'distro_desc'  => 'For laptop and desktops',
                'fallback_urls' => [
                    'https://archive.org/download/windows-10-22h2-x64-english/en-us_windows_10_22h2_updated_may_2023_x64_dvd_8ae93bf4.iso',
                ],
            ],
            [
                'name'  => 'Windows Server 2022',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/sg/download/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Server2022.iso',
                'size'  => '4.9 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
                'distro_label' => 'Windows Server',
                'distro_desc'  => 'For server use',
                'fallback_urls' => [
                    'https://archive.org/download/WindowsServer2022_RTM/en-us_windows_server_2022_x64_dvd_620d7eac.iso',
                ],
            ],
        ];

        $results = [];
        foreach ($versions as $v) {
            if ($query !== '' && !str_contains(strtolower($v['name']), $query)) continue;
            foreach ($v['architectures'] as $arch) {
                $results[] = [
                    'name'          => $v['name'],
                    'version'       => $v['name'],
                    'platform'      => $arch,
                    'type'          => 'ISO',
                    'url'           => $v['url'],
                    'source'        => $v['source'],
                    'verified'      => $v['verified'],
                    'hash_type'     => 'SHA256',
                    'category'      => 'windows',
                    'provider'      => $this->getName(),
                    'distro_label'  => $v['distro_label'] ?? '',
                    'distro_desc'   => $v['distro_desc'] ?? '',
                    'fallback_urls' => $v['fallback_urls'] ?? [],
                ];
            }
        }
        return $results;
    }
}

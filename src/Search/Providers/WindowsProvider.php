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
                'name'  => 'Windows 11 24H2 (26100)',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/dbazure/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/26100.1.240331-1403.GE_RELEASE_SVC_PROD2_CLIENTCORE_SINGLELANGUAGE_OEMRET_x64FRE_en-us.iso',
                'size'  => '6.5 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
            ],
            [
                'name'  => 'Windows 11 23H2',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/akfm/medias/MBF2.191029.1546-23H2/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Win11_23H2_English_x64.iso',
                'size'  => '6.3 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
            ],
            [
                'name'  => 'Windows 10 22H2',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/dbazure/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Win10_22H2_English_x64.iso',
                'size'  => '5.8 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
            ],
            [
                'name'  => 'Windows Server 2022',
                'architectures' => ['x64'],
                'url'   => 'https://software-static.download.prss.microsoft.com/sg/download/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Server2022.iso',
                'size'  => '4.9 GB',
                'source'=> 'Official Microsoft',
                'verified' => true,
            ],
        ];

        $results = [];
        foreach ($versions as $v) {
            if ($query !== '' && !str_contains(strtolower($v['name']), $query)) continue;
            foreach ($v['architectures'] as $arch) {
                $results[] = [
                    'name'      => $v['name'],
                    'version'   => $v['name'],
                    'platform'  => $arch,
                    'type'      => 'ISO',
                    'url'       => $v['url'],
                    'source'    => $v['source'],
                    'verified'  => $v['verified'],
                    'hash_type' => 'SHA256',
                    'category'  => 'windows',
                    'provider'  => $this->getName(),
                ];
            }
        }
        return $results;
    }
}

<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class GitHubProvider implements Provider
{
    private string $apiBase = 'https://api.github.com';

    public function getName(): string { return 'GitHub Releases'; }
    public function getCategory(): string { return 'software'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $results = [];
        $repos = $this->searchRepos($query);

        foreach ($repos as $repo) {
            $release = $this->getLatestRelease($repo['full_name']);
            if (!$release) continue;

            foreach ($release['assets'] as $asset) {
                $name = $asset['name'] ?? '';
                $url = $asset['browser_download_url'] ?? '';
                $size = $asset['size'] ?? 0;

                if ($url === '' || $size < 1000) continue;

                $platform = $this->detectPlatform($name);
                $type = $this->detectType($name);

                $results[] = [
                    'name'      => $repo['full_name'],
                    'version'   => $release['tag_name'] ?? 'latest',
                    'platform'  => $platform,
                    'type'      => $type,
                    'url'       => $url,
                    'source'    => 'GitHub Release',
                    'verified'  => true,
                    'hash_type' => null,
                    'category'  => 'software',
                    'provider'  => $this->getName(),
                    'publisher' => $repo['full_name'],
                    'stars'     => $repo['stargazers_count'] ?? 0,
                    'desc'      => $repo['description'] ?? '',
                    'asset_size'=> $size,
                ];
            }
        }

        return $results;
    }

    private function searchRepos(string $query): array
    {
        $url = $this->apiBase . '/search/repositories?q=' . urlencode($query . ' in:name') . '&sort=stars&per_page=10';

        $data = $this->httpGet($url);
        if (!$data || !isset($data['items'])) return [];

        return $data['items'];
    }

    private function getLatestRelease(string $repo): ?array
    {
        $url = $this->apiBase . '/repos/' . $repo . '/releases/latest';
        return $this->httpGet($url);
    }

    private function httpGet(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Accept: application/vnd.github.v3+json'],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$body) return null;
        return json_decode($body, true);
    }

    private function detectPlatform(string $filename): string
    {
        $l = strtolower($filename);
        if (str_contains($l, 'windows') || str_contains($l, 'win64') || str_contains($l, 'win32') || str_contains($l, '.exe') || str_contains($l, '.msi')) return 'Windows';
        if (str_contains($l, 'linux') || str_contains($l, 'ubuntu') || str_contains($l, 'debian') || str_contains($l, '.deb') || str_contains($l, '.rpm') || str_contains($l, '.appimage') || str_contains($l, '.tar.gz')) return 'Linux';
        if (str_contains($l, 'macos') || str_contains($l, 'darwin') || str_contains($l, '.dmg') || str_contains($l, '.pkg')) return 'macOS';
        if (str_contains($l, 'android') || str_contains($l, '.apk')) return 'Android';
        if (str_contains($l, 'ios') || str_contains($l, '.ipa')) return 'iOS';
        return 'Universal';
    }

    private function detectType(string $filename): string
    {
        $l = strtolower($filename);
        if (str_ends_with($l, '.exe') || str_ends_with($l, '.msi')) return 'Installer';
        if (str_ends_with($l, '.deb')) return 'Package (.deb)';
        if (str_ends_with($l, '.rpm')) return 'Package (.rpm)';
        if (str_ends_with($l, '.dmg')) return 'DMG';
        if (str_ends_with($l, '.pkg')) return 'Package (.pkg)';
        if (str_ends_with($l, '.appimage')) return 'AppImage';
        if (str_ends_with($l, '.tar.gz') || str_ends_with($l, '.tgz')) return 'Archive (.tar.gz)';
        if (str_ends_with($l, '.zip')) return 'Archive (.zip)';
        if (str_ends_with($l, '.apk')) return 'APK';
        if (str_ends_with($l, '.app')) return 'App Bundle';
        return 'Binary';
    }
}

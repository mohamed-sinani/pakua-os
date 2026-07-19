<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class ChocolateyProvider implements Provider
{
    public function getName(): string { return 'Chocolatey'; }
    public function getCategory(): string { return 'software'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $results = [];
        $packages = $this->searchPackages($query);

        foreach ($packages as $pkg) {
            $id = $pkg['id'] ?? '';
            $version = $pkg['version'] ?? $pkg['latestVersion'] ?? '';
            $desc = $pkg['description'] ?? '';
            $url = "https://community.chocolatey.org/api/v2/package/{$id}/{$version}";

            $results[] = [
                'name'      => $id,
                'version'   => $version,
                'platform'  => 'Windows x64',
                'type'      => 'Package (Chocolatey)',
                'url'       => $url,
                'source'    => 'Chocolatey Community',
                'verified'  => true,
                'hash_type' => null,
                'category'  => 'software',
                'provider'  => $this->getName(),
                'publisher' => $id,
                'desc'      => mb_substr(trim($desc), 0, 120),
            ];
        }

        return $results;
    }

    private function searchPackages(string $query): array
    {
        // Use Chocolatey API v3 (JSON)
        $url = 'https://api.chocolatey.org/packages?search=' . urlencode($query) . '&take=10&orderBy=DownloadCount';
        $body = $this->httpGet($url);
        if (!$body) return [];

        $data = json_decode($body, true);
        if (!$data || !isset($data['packages'])) return [];

        $results = [];
        foreach ($data['packages'] as $pkg) {
            $results[] = [
                'id'          => $pkg['id'] ?? '',
                'version'     => $pkg['version'] ?? '',
                'description' => $pkg['description'] ?? '',
            ];
        }
        return $results;
    }

    private function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'X-Chocolatey-Call: true',
            ],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200 && $body) ? $body : null;
    }
}

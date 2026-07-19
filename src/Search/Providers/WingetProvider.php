<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class WingetProvider implements Provider
{
    private string $apiBase = 'https://cdn.winget.microsoft.com/cache';

    public function getName(): string { return 'Winget'; }
    public function getCategory(): string { return 'software'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $results = [];
        $packages = $this->searchPackages($query);

        foreach ($packages as $pkg) {
            $results[] = [
                'name'      => $pkg['name'] ?? '',
                'version'   => $pkg['version'] ?? '',
                'platform'  => 'Windows x64',
                'type'      => 'Package (Winget)',
                'url'       => $pkg['installerUrl'] ?? '',
                'source'    => 'Microsoft Winget',
                'verified'  => true,
                'hash_type' => 'SHA256',
                'category'  => 'software',
                'provider'  => $this->getName(),
                'publisher' => $pkg['publisher'] ?? '',
                'desc'      => $pkg['description'] ?? '',
            ];
        }

        return $results;
    }

    private function searchPackages(string $query): array
    {
        $url = 'https://api.winget.dev/v1.0/manifests/search?query=' . urlencode($query) . '&limit=10';
        $body = $this->httpGet($url);
        if (!$body) return [];
        $data = json_decode($body, true);
        return $data['data'] ?? [];
    }

    private function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200 && $body) ? $body : null;
    }
}

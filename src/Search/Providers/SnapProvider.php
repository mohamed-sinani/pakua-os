<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class SnapProvider implements Provider
{
    public function getName(): string { return 'Snap Store'; }
    public function getCategory(): string { return 'software'; }
    public function isAvailable(): bool { return true; }

    public function search(string $query): array
    {
        $results = [];
        $packages = $this->searchPackages($query);

        foreach ($packages as $pkg) {
            $name = $pkg['name'] ?? '';
            $title = $pkg['title'] ?? $name;
            $publisher = $pkg['developer_name'] ?? '';
            $summary = $pkg['summary'] ?? '';
            $version = $pkg['version'] ?? '';

            $results[] = [
                'name'      => $title,
                'version'   => $version,
                'platform'  => 'Linux (Snap)',
                'type'      => 'Package (Snap)',
                'url'       => "https://snapcraft.io/{$name}",
                'source'    => 'Snap Store',
                'verified'  => (bool)($pkg['publisher_validated'] ?? false),
                'hash_type' => null,
                'category'  => 'software',
                'provider'  => $this->getName(),
                'publisher' => $publisher,
                'desc'      => $summary,
            ];
        }

        return $results;
    }

    private function searchPackages(string $query): array
    {
        $url = 'https://api.snapcraft.io/v2/find?q=' . urlencode($query) . '&size=10';
        $body = $this->httpGet($url);
        if (!$body) return [];

        $data = json_decode($body, true);
        return $data['results'] ?? [];
    }

    private function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/hal+json'],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200 && $body) ? $body : null;
    }
}

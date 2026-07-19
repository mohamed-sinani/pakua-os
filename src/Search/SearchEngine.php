<?php

declare(strict_types=1);

namespace PakuaOS\Search;

use PakuaOS\Search\Providers\Provider;
use PakuaOS\Search\Providers\LinuxProvider;
use PakuaOS\Search\Providers\WindowsProvider;
use PakuaOS\Search\Providers\MacOSProvider;
use PakuaOS\Search\Providers\SoftwareProvider;
use PakuaOS\Search\Providers\GitHubProvider;
use PakuaOS\Search\Providers\ChocolateyProvider;
use PakuaOS\Search\Providers\WingetProvider;
use PakuaOS\Search\Providers\SnapProvider;
use PakuaOS\Search\Providers\WebSearchProvider;

final class SearchEngine
{
    /** @var Provider[] */
    private array $providers = [];

    public function __construct()
    {
        // Offline / curated
        $this->providers[] = new LinuxProvider();
        $this->providers[] = new WindowsProvider();
        $this->providers[] = new MacOSProvider();
        $this->providers[] = new SoftwareProvider();

        // Live API providers
        $this->providers[] = new GitHubProvider();
        $this->providers[] = new ChocolateyProvider();
        $this->providers[] = new SnapProvider();
        $this->providers[] = new WebSearchProvider();
    }

    public function search(string $query): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            $results = $provider->search($query);
            $all = array_merge($all, $results);
        }

        // Deduplicate by name+version+platform
        return $this->deduplicate($all);
    }

    public function searchCategory(string $category, string $query = ''): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            if ($provider->getCategory() !== $category) continue;
            $results = $provider->search($query);
            $all = array_merge($all, $results);
        }
        return $this->deduplicate($all);
    }

    public function searchSoftware(string $query): array
    {
        // Search curated + live API providers
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            if ($provider->getCategory() !== 'software') continue;
            $results = $provider->search($query);
            $all = array_merge($all, $results);
        }
        return $this->deduplicate($all);
    }

    public function searchOnline(string $query): array
    {
        // Only live API providers (GitHub, Chocolatey, Snap)
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            // Skip offline/curated providers
            $class = (new \ReflectionClass($provider))->getShortName();
            if (in_array($class, ['LinuxProvider', 'WindowsProvider', 'MacOSProvider', 'SoftwareProvider'])) continue;
            $results = $provider->search($query);
            $all = array_merge($all, $results);
        }
        return $this->deduplicate($all);
    }

    private function deduplicate(array $results): array
    {
        $seen = [];
        $unique = [];
        foreach ($results as $r) {
            $key = md5(($r['name'] ?? '') . '|' . ($r['version'] ?? '') . '|' . ($r['platform'] ?? '') . '|' . ($r['url'] ?? ''));
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $unique[] = $r;
        }
        return $unique;
    }

    public function getProviders(): array { return $this->providers; }
}

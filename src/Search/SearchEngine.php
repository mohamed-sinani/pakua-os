<?php

declare(strict_types=1);

namespace PakuaOS\Search;

use PakuaOS\Search\Providers\Provider;
use PakuaOS\Search\Providers\LinuxProvider;
use PakuaOS\Search\Providers\WindowsProvider;
use PakuaOS\Search\Providers\MacOSProvider;

final class SearchEngine
{
    /** @var Provider[] */
    private array $providers = [];

    public function __construct()
    {
        $this->providers[] = new LinuxProvider();
        $this->providers[] = new WindowsProvider();
        $this->providers[] = new MacOSProvider();
    }

    public function search(string $query, ?callable $onProgress = null): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            if ($onProgress) {
                $onProgress($provider->getName());
            }
            $results = $provider->search($query);
            $all = array_merge($all, $results);
        }

        return $this->deduplicate($all);
    }

    public function searchCategory(string $category, string $query = '', ?callable $onProgress = null): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            if ($provider->getCategory() !== $category) continue;
            if ($onProgress) {
                $onProgress($provider->getName());
            }
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

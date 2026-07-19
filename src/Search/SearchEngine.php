<?php

declare(strict_types=1);

namespace PakuaOS\Search;

use PakuaOS\Search\Providers\Provider;
use PakuaOS\Search\Providers\LinuxProvider;
use PakuaOS\Search\Providers\WindowsProvider;
use PakuaOS\Search\Providers\MacOSProvider;
use PakuaOS\Search\Providers\SoftwareProvider;

final class SearchEngine
{
    /** @var Provider[] */
    private array $providers = [];

    public function __construct()
    {
        $this->providers[] = new LinuxProvider();
        $this->providers[] = new WindowsProvider();
        $this->providers[] = new MacOSProvider();
        $this->providers[] = new SoftwareProvider();
    }

    public function search(string $query): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            $all = array_merge($all, $provider->search($query));
        }
        return $all;
    }

    public function searchCategory(string $category, string $query = ''): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isAvailable()) continue;
            if ($provider->getCategory() !== $category) continue;
            $all = array_merge($all, $provider->search($query));
        }
        return $all;
    }

    public function getProviders(): array { return $this->providers; }
}

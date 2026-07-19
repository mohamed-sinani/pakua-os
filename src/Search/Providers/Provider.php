<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

interface Provider
{
    public function getName(): string;

    public function getCategory(): string;

    public function search(string $query): array;

    public function isAvailable(): bool;
}

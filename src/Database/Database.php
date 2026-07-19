<?php

declare(strict_types=1);

namespace PakuaOS\Database;

final class Database
{
    private static ?Database $instance = null;
    private string $downloadsFile;
    private string $settingsFile;
    private array $downloads = [];
    private array $settings = [];
    private int $nextId = 1;

    private function __construct()
    {
        $dir = $this->getStorageDir();
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $this->downloadsFile = $dir . '/downloads.json';
        $this->settingsFile = $dir . '/settings.json';

        $this->load();
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function load(): void
    {
        if (file_exists($this->downloadsFile)) {
            $data = json_decode(file_get_contents($this->downloadsFile), true) ?? [];
            $this->downloads = $data;
            $this->nextId = 1;
            foreach ($data as $d) {
                if (isset($d['id']) && $d['id'] >= $this->nextId) {
                    $this->nextId = $d['id'] + 1;
                }
            }
        }

        if (file_exists($this->settingsFile)) {
            $this->settings = json_decode(file_get_contents($this->settingsFile), true) ?? [];
        }
    }

    private function saveDownloads(): void
    {
        file_put_contents($this->downloadsFile, json_encode($this->downloads, JSON_PRETTY_PRINT));
    }

    private function saveSettings(): void
    {
        file_put_contents($this->settingsFile, json_encode($this->settings, JSON_PRETTY_PRINT));
    }

    private function getStorageDir(): string
    {
        $home = $_SERVER['HOME'] ?? (function_exists('posix_getuid') ? getenv('HOME') : sys_get_temp_dir());
        return $home . '/.pakuaos';
    }

    public function getStoragePath(): string { return $this->getStorageDir(); }

    public function addDownload(array $data): int
    {
        $data['id'] = $this->nextId++;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->downloads[] = $data;
        $this->saveDownloads();
        return $data['id'];
    }

    public function updateDownload(int $id, array $fields): void
    {
        foreach ($this->downloads as &$d) {
            if ($d['id'] === $id) {
                $d = array_merge($d, $fields, ['updated_at' => date('Y-m-d H:i:s')]);
                break;
            }
        }
        unset($d);
        $this->saveDownloads();
    }

    public function getDownload(int $id): ?array
    {
        foreach ($this->downloads as $d) {
            if ($d['id'] === $id) return $d;
        }
        return null;
    }

    public function getAllDownloads(): array
    {
        return $this->downloads;
    }

    public function getDownloadsByStatus(string $status): array
    {
        return array_values(array_filter($this->downloads, fn($d) => ($d['status'] ?? '') === $status));
    }

    public function deleteDownload(int $id): void
    {
        $this->downloads = array_values(array_filter($this->downloads, fn($d) => $d['id'] !== $id));
        $this->saveDownloads();
    }

    public function setting(string $key, ?string $default = null): ?string
    {
        return $this->settings[$key] ?? $default;
    }

    public function setSetting(string $key, string $value): void
    {
        $this->settings[$key] = $value;
        $this->saveSettings();
    }
}

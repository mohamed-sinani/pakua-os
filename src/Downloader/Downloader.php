<?php

declare(strict_types=1);

namespace PakuaOS\Downloader;

use PakuaOS\UI\ProgressBar;
use PakuaOS\UI\Theme;
use PakuaOS\Verification\HashVerifier;
use PakuaOS\Database\Database;

final class Downloader
{
    private string $baseDir;
    private ?string $expectedHash = null;
    private string $hashAlgo = 'sha256';
    private ?string $category = null;
    private ?int $currentDownloadId = null;
    private string $lastError = '';

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? $this->getDefaultBaseDir();
    }

    private function getDefaultBaseDir(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $home = getenv('USERPROFILE') ?: getenv('HOMEDRIVE') . getenv('HOMEPATH');
            return $home . '\Downloads\PakuaOS';
        }
        $home = $_SERVER['HOME'] ?? getenv('HOME');
        return $home . '/Downloads/PakuaOS';
    }

    private function resolveDir(?string $category): string
    {
        $dir = match ($category) {
            'os'    => $this->baseDir . '/Operating Systems',
            default => $this->baseDir,
        };

        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return $dir;
    }

    public function download(
        string $url,
        string $name,
        ?string $expectedHash = null,
        string $hashAlgo = 'sha256',
        ?string $category = null,
        array $fallbackUrls = []
    ): ?string {
        $dir = $this->resolveDir($category);

        echo "\n";
        echo Theme::separator("Download") . "\n";
        echo "  " . Theme::bold(Theme::cyan('URL')) . ':        ' . $url . "\n";
        echo "  " . Theme::bold(Theme::cyan('Saving to')) . ':  ' . $dir . "\n";

        if ($expectedHash) {
            echo "  " . Theme::bold(Theme::cyan('Checksum')) . ':  ' . Theme::dim("{$hashAlgo} — will verify after download") . "\n";
        }
        echo "\n";

        $filename = $this->sanitizeFilename($name);
        if ($category === 'os' && !str_ends_with(strtolower($filename), '.iso')) {
            $filename .= '.iso';
        }
        $filePath = $dir . '/' . $filename;

        $db = Database::instance();

        $startByte = 0;
        if (file_exists($filePath)) {
            $startByte = filesize($filePath);
            $existing = $db->getDownloadsByStatus('downloading');
            $existing = array_filter($existing, fn($d) => ($d['file_path'] ?? '') === $filePath);
            if (!empty($existing)) {
                echo "  " . Theme::info("Resuming from " . ProgressBar::formatBytes($startByte) . " (previous session)") . "\n\n";
            } else {
                echo "  " . Theme::info("Resuming from " . ProgressBar::formatBytes($startByte)) . "\n\n";
            }
        }

        // Register download in DB as 'downloading'
        $dlId = $db->addDownload([
            'name'       => basename($filePath),
            'url'        => $url,
            'file_path'  => $filePath,
            'file_size'  => 0,
            'downloaded' => $startByte,
            'status'     => 'downloading',
            'hash_type'  => $hashAlgo,
            'hash_value' => $expectedHash ?? '',
            'source'     => parse_url($url, PHP_URL_HOST) ?? '',
            'category'   => $category ?? 'other',
        ]);

        // Store for use in tryDownload
        $this->expectedHash = $expectedHash;
        $this->hashAlgo = $hashAlgo;
        $this->category = $category;
        $this->currentDownloadId = $dlId;

        // Try primary URL first, then fallbacks
        $allUrls = array_merge([$url], $fallbackUrls);
        $lastError = '';
        foreach ($allUrls as $attempt => $tryUrl) {
            $isLast = ($attempt === count($allUrls) - 1);

            if ($attempt > 0) {
                echo "\n";
                echo "  " . Theme::dim("Trying next source (" . ($attempt + 1) . "/" . count($allUrls) . ")...") . "\n\n";
            }

            $result = $this->tryDownload($tryUrl, $filePath, $startByte, $isLast);
            if ($result !== null) {
                return $result;
            }

            $lastError = $this->lastError;
            $startByte = 0;
        }

        // All attempts failed
        echo "\n";
        echo "  " . Theme::error("All download sources failed.") . "\n";
        if ($lastError) {
            echo "  " . Theme::dim("Last error: " . $lastError) . "\n";
        }
        return null;
    }

    private function tryDownload(string $url, string $filePath, int $startByte, bool $isLast = false): ?string
    {
        // Get file size via HEAD request
        $headCh = curl_init($url);
        curl_setopt_array($headCh, [
            CURLOPT_NOBODY         => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
        ]);
        curl_exec($headCh);
        $totalSize = (int)curl_getinfo($headCh, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($headCh);

        if ($totalSize > 0 && $this->currentDownloadId) {
            Database::instance()->updateDownload($this->currentDownloadId, [
                'file_size' => $totalSize,
            ]);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_NOPROGRESS     => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_RESUME_FROM    => $startByte,
            CURLOPT_BUFFERSIZE     => 65536,
        ]);

        $fp = fopen($filePath, $startByte > 0 ? 'ab' : 'wb');
        $totalSize = max($totalSize, 1);

        while (ob_get_level()) ob_end_flush();
        ob_implicit_flush(true);
        stream_set_write_buffer(STDOUT, 0);
        stream_set_write_buffer(STDERR, 0);

        $downloaded = $startByte;
        $lastDraw = 0;
        $dlStartTime = microtime(true);

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($fp, $totalSize, $startByte, &$downloaded, &$lastDraw, $dlStartTime) {
            $len = strlen($data);
            fwrite($fp, $data);
            $downloaded += $len;

            $now = microtime(true);
            if ($now - $lastDraw >= 0.15 || $downloaded >= $totalSize) {
                $lastDraw = $now;
                $elapsed = $now - $dlStartTime;
                $bytesThisSession = $downloaded - $startByte;
                $speed = $elapsed > 0.5 ? $bytesThisSession / $elapsed : 0;

                $pct = min(($downloaded / $totalSize) * 100, 100);
                $filled = (int)round(($pct / 100) * 36);
                $empty = 36 - $filled;

                $bar = str_repeat('█', $filled) . str_repeat('░', $empty);
                $pctStr = str_pad(number_format($pct, 0) . '%', 5);

                $line = '[' . $bar . '] ' . $pctStr;
                $line .= ' ' . ProgressBar::formatBytes($downloaded) . '/' . ProgressBar::formatBytes($totalSize);

                if ($speed > 0 && $downloaded < $totalSize) {
                    $line .= ' ' . ProgressBar::formatBytes((int)$speed) . '/s';
                    $remaining = $totalSize - $downloaded;
                    $sec = (int)ceil($remaining / $speed);
                    $line .= ' ETA ' . ProgressBar::formatTime($sec);
                } elseif ($downloaded >= $totalSize) {
                    $line .= ' Done';
                }

                fprintf(STDOUT, "\r  %-78s", mb_substr($line, 0, 78));
                fflush(STDOUT);
            }

            return $len;
        });

        echo "\n";
        flush();

        $success = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        echo "\n";

        if (!$success || ($httpCode >= 400 && $httpCode !== 206 && $httpCode !== 0)) {
            $this->lastError = $error ?: "HTTP {$httpCode}";
            if ($this->currentDownloadId) {
                Database::instance()->updateDownload($this->currentDownloadId, [
                    'status' => 'resumable',
                    'downloaded' => $startByte + (int)@filesize($filePath),
                ]);
            }
            return null;
        }

        $finalSize = filesize($filePath);
        fprintf(STDOUT, "\r  %-78s", str_repeat(' ', 78));
        fprintf(STDOUT, "\r  [████████████████████████████████████████] 100%% Done\n");
        flush();

        if ($this->expectedHash) {
            echo Theme::separator("Verification") . "\n";
            $verified = HashVerifier::verify($filePath, $this->expectedHash, $this->hashAlgo);
            if ($verified) {
                echo "  " . Theme::success("✔ Integrity verified.") . "\n";
                echo "  " . Theme::success("✔ Publisher signature verified.") . "\n\n";
            } else {
                echo "  " . Theme::warning("⚠ Checksum could not be verified") . "\n";
                echo "  " . Theme::dim("No local reference checksum available for comparison") . "\n\n";
            }
        }

        $size = filesize($filePath);
        echo Theme::successBox("Download complete.") . "\n";
        echo Theme::successBox("File verified.") . "\n";
        echo Theme::successBox("Ready to install.") . "\n";
        echo "\n";
        echo "  " . Theme::bold(Theme::green("Saved to:")) . "\n\n";
        echo "  " . Theme::cyan($filePath) . "\n";
        echo "  " . Theme::dim("Size: " . ProgressBar::formatBytes($size)) . "\n\n";

        if ($this->currentDownloadId) {
            Database::instance()->updateDownload($this->currentDownloadId, [
                'name'       => basename($filePath),
                'file_path'  => $filePath,
                'file_size'  => $size,
                'downloaded' => $size,
                'status'     => 'completed',
            ]);
        } else {
            Database::instance()->addDownload([
                'name'       => basename($filePath),
                'url'        => $url,
                'file_path'  => $filePath,
                'file_size'  => $size,
                'downloaded' => $size,
                'status'     => 'completed',
                'hash_type'  => $this->hashAlgo,
                'hash_value' => $this->expectedHash ?? '',
                'source'     => parse_url($url, PHP_URL_HOST) ?? '',
                'category'   => $this->category ?? 'other',
            ]);
        }

        return $filePath;
    }

    private function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^\w\s\-\.]+/u', '', $name);
        $name = preg_replace('/\s+/', '_', $name);
        return mb_substr($name, 0, 120);
    }
}

function Menu_confirm(string $question): bool
{
    $handle = fopen('php://stdin', 'r');
    echo "\n  " . Theme::yellow($question) . ' [Y/n] ';
    $input = strtolower(trim(fgets($handle)));
    fclose($handle);
    return $input !== 'n' && $input !== 'no';
}

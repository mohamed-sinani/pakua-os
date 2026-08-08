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
    private string $currentUrl = '';

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? $this->getDefaultBaseDir();
    }

    private function getHomeDir(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return getenv('USERPROFILE') ?: getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return $_SERVER['HOME'] ?? getenv('HOME') ?? sys_get_temp_dir();
    }

    private function getDefaultBaseDir(): string
    {
        $setting = Database::instance()->setting('download_dir');
        if (!empty($setting)) {
            return rtrim(str_replace('~/', $this->getHomeDir() . '/', $setting), '/\\');
        }
        return $this->getHomeDir() . '/Downloads/PakuaOS';
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
        $partPath = $filePath . '.part';

        $db = Database::instance();

        // Already fully downloaded and recorded → skip.
        foreach ($db->getAllDownloads() as $existing) {
            if (($existing['file_path'] ?? '') === $filePath
                && ($existing['status'] ?? '') === 'completed'
                && file_exists($filePath)) {
                echo "  " . Theme::info("File already downloaded:") . "\n";
                echo "  " . Theme::cyan($filePath) . "\n\n";
                return $filePath;
            }
        }

        $startByte = 0;
        if (file_exists($partPath)) {
            $startByte = filesize($partPath);
        } elseif (file_exists($filePath)) {
            $startByte = filesize($filePath);
        }
        if ($startByte > 0) {
            echo "  " . Theme::info("Resuming from " . ProgressBar::formatBytes($startByte)) . "\n\n";
        }

        // Register download in DB as 'downloading'
        $this->currentUrl = $url;
        $dlId = $db->addDownload([
            'name'          => basename($filePath),
            'url'           => $url,
            'file_path'     => $filePath,
            'file_size'     => 0,
            'downloaded'    => $startByte,
            'status'        => 'downloading',
            'hash_type'     => $hashAlgo,
            'hash_value'    => $expectedHash ?? '',
            'source'        => parse_url($url, PHP_URL_HOST) ?? '',
            'category'      => $category ?? 'other',
            'fallback_urls' => $fallbackUrls,
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

            $result = $this->tryDownload($tryUrl, $partPath, $filePath, $startByte, $isLast);
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

    private function tryDownload(string $url, string $writePath, string $finalPath, int $startByte, bool $isLast = false): ?string
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

        // Nothing left to fetch — the on-disk file already matches the source.
        if ($totalSize > 0 && $startByte >= $totalSize && file_exists($writePath)) {
            return $this->finalizeDownload($writePath, $finalPath);
        }

        $resume = ($startByte > 0 && ($totalSize <= 0 || $startByte < $totalSize));
        $resumeFrom = $resume ? $startByte : 0;

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
            CURLOPT_RESUME_FROM    => $resumeFrom,
            CURLOPT_BUFFERSIZE     => 65536,
        ]);

        $fp = fopen($writePath, $resume ? 'ab' : 'wb');
        $unknownSize = ($totalSize <= 0);

        while (ob_get_level()) ob_end_flush();
        ob_implicit_flush(true);
        stream_set_write_buffer(STDOUT, 0);
        stream_set_write_buffer(STDERR, 0);

        $downloaded = $startByte;
        $lastDraw = 0;
        $dlStartTime = microtime(true);
        $startOffset = $startByte;

        $writeFn = function ($ch, $data) use (&$fp, $totalSize, $unknownSize, &$startOffset, &$downloaded, &$lastDraw, &$dlStartTime) {
            $len = strlen($data);
            fwrite($fp, $data);
            $downloaded += $len;

            $now = microtime(true);
            if ($now - $lastDraw >= 0.15 || (!$unknownSize && $downloaded >= $totalSize)) {
                $lastDraw = $now;
                $elapsed = $now - $dlStartTime;
                $bytesThisSession = $downloaded - $startOffset;
                $speed = $elapsed > 0.5 ? $bytesThisSession / $elapsed : 0;

                if ($unknownSize) {
                    $line = ProgressBar::formatBytes($downloaded) . ' downloaded';
                } else {
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
                }

                fprintf(STDOUT, "\r  %-78s", mb_substr($line, 0, 78));
                fflush(STDOUT);
            }

            return $len;
        };

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $writeFn);

        echo "\n";
        flush();

        $success = false;
        $error = '';
        $httpCode = 0;
        for ($attemptNo = 0; $attemptNo < 2; $attemptNo++) {
            $success = curl_exec($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($resume && $errno === CURLE_HTTP_RANGE_ERROR) {
                // Server ignored the Range header — restart from scratch.
                fclose($fp);
                $fp = fopen($writePath, 'wb');
                $resume = false;
                $startOffset = 0;
                $downloaded = 0;
                $lastDraw = 0;
                $dlStartTime = microtime(true);
                curl_setopt($ch, CURLOPT_RESUME_FROM, 0);
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, $writeFn);
                continue;
            }
            break;
        }
        curl_close($ch);
        fclose($fp);

        echo "\n";

        if (!$success || ($httpCode >= 400 && $httpCode !== 206 && $httpCode !== 0)) {
            $this->lastError = $error ?: "HTTP {$httpCode}";
            if ($this->currentDownloadId) {
                Database::instance()->updateDownload($this->currentDownloadId, [
                    'status'     => 'resumable',
                    'downloaded' => (int)@filesize($writePath),
                ]);
            }
            return null;
        }

        return $this->finalizeDownload($writePath, $finalPath);
    }

    private function finalizeDownload(string $writePath, string $finalPath): string
    {
        if ($writePath !== $finalPath && file_exists($writePath)) {
            if (file_exists($finalPath)) @unlink($finalPath);
            @rename($writePath, $finalPath);
        }
        $savedPath = file_exists($finalPath) ? $finalPath : $writePath;

        fprintf(STDOUT, "\r  %-78s", str_repeat(' ', 78));
        fprintf(STDOUT, "\r  [████████████████████████████████████████] 100%% Done\n");
        flush();

        $verified = false;
        if ($this->expectedHash) {
            echo Theme::separator("Verification") . "\n";
            $verified = HashVerifier::verify($savedPath, $this->expectedHash, $this->hashAlgo);
            if ($verified) {
                echo "  " . Theme::success("✔ Integrity verified.") . "\n";
                echo "  " . Theme::success("✔ Checksum matches expected value.") . "\n\n";
            } else {
                echo "  " . Theme::warning("⚠ Checksum mismatch!") . "\n";
                echo "  " . Theme::dim("Expected {$this->hashAlgo}: {$this->expectedHash}") . "\n\n";
            }
        }

        $size = filesize($savedPath);
        echo Theme::successBox("Download complete.") . "\n";
        if (!empty($this->expectedHash)) {
            if ($verified) {
                echo Theme::successBox("File verified.") . "\n";
            } else {
                echo Theme::warningBox("File NOT verified — checksum mismatch.") . "\n";
            }
        }
        echo Theme::successBox("Ready to install.") . "\n";
        echo "\n";
        echo "  " . Theme::bold(Theme::green("Saved to:")) . "\n\n";
        echo "  " . Theme::cyan($savedPath) . "\n";
        echo "  " . Theme::dim("Size: " . ProgressBar::formatBytes($size)) . "\n\n";

        if ($this->currentDownloadId) {
            Database::instance()->updateDownload($this->currentDownloadId, [
                'name'       => basename($savedPath),
                'file_path'  => $savedPath,
                'file_size'  => $size,
                'downloaded' => $size,
                'status'     => 'completed',
            ]);
        } else {
            Database::instance()->addDownload([
                'name'          => basename($savedPath),
                'url'           => $this->currentUrl,
                'file_path'     => $savedPath,
                'file_size'     => $size,
                'downloaded'    => $size,
                'status'        => 'completed',
                'hash_type'     => $this->hashAlgo,
                'hash_value'    => $this->expectedHash ?? '',
                'source'        => parse_url($this->currentUrl, PHP_URL_HOST) ?? '',
                'category'      => $this->category ?? 'other',
                'fallback_urls' => [],
            ]);
        }

        return $savedPath;
    }

    private function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^\w\s\-\.]+/u', '', $name);
        $name = preg_replace('/\s+/', '_', $name);
        return mb_substr($name, 0, 120);
    }
}

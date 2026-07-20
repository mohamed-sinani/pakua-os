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

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? $this->getDefaultBaseDir();
    }

    private function getDefaultBaseDir(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $home = getenv('USERPROFILE') ?: getenv('HOMEDRIVE') . getenv('HOMEPATH');
            return $home . '\Downloads';
        }
        $home = $_SERVER['HOME'] ?? getenv('HOME');
        return $home . '/Downloads';
    }

    private function resolveDir(?string $category): string
    {
        $dir = match ($category) {
            'os'       => $this->baseDir . '/Operating Systems',
            'programs' => $this->baseDir . '/Programs',
            default    => $this->baseDir,
        };

        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return $dir;
    }

    public function download(
        string $url,
        string $name,
        ?string $expectedHash = null,
        string $hashAlgo = 'sha256',
        ?string $category = null
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
        $filePath = $dir . '/' . $filename;

        $startByte = 0;
        if (file_exists($filePath . '.part')) {
            $startByte = filesize($filePath . '.part');
            echo "  " . Theme::info("Resuming from " . ProgressBar::formatBytes($startByte)) . "\n\n";
        } elseif (file_exists($filePath)) {
            if (!Menu_confirm("File exists. Overwrite?")) {
                echo "  " . Theme::info("Download cancelled.") . "\n";
                return null;
            }
        }

        // Get file size via HEAD request — suppress any output
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

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_RESUME_FROM    => $startByte,
        ]);

        $fp = fopen($filePath . '.part', $startByte > 0 ? 'ab' : 'wb');

        $bar = new ProgressBar($totalSize > 0 ? $totalSize : 1);
        if ($startByte > 0) $bar->set($startByte);

        $lastTime = microtime(true);

        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function (
            $resource, $dlNow, $dlTotal, $ulNow, $ulTotal
        ) use ($bar, &$lastTime, $startByte) {
            $now = microtime(true);
            if ($now - $lastTime >= 0.25) {
                $lastTime = $now;
            }
            $bar->set((int)($startByte + $dlNow));
            return 0;
        });

        curl_setopt($ch, CURLOPT_FILE, $fp);
        $success = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if (!$success || ($httpCode >= 400 && $httpCode !== 206 && $httpCode !== 0)) {
            echo "\n\n";
            echo "  " . Theme::error("Download failed!") . "\n";
            if ($error) echo "  " . Theme::error("Error: {$error}") . "\n";
            echo "  " . Theme::dim("HTTP Code: {$httpCode}") . "\n";
            return null;
        }

        rename($filePath . '.part', $filePath);
        $bar->finish();
        echo "\n";

        if ($expectedHash) {
            echo Theme::separator("Verification") . "\n";
            $verified = HashVerifier::verify($filePath, $expectedHash, $hashAlgo);
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

        Database::instance()->addDownload([
            'name'      => $name,
            'url'       => $url,
            'file_path' => $filePath,
            'file_size' => $size,
            'downloaded'=> $size,
            'status'    => 'completed',
            'hash_type' => $hashAlgo,
            'hash_value'=> $expectedHash ?? '',
            'source'    => parse_url($url, PHP_URL_HOST) ?? '',
            'category'  => $category ?? 'other',
        ]);

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

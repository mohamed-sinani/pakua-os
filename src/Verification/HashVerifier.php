<?php

declare(strict_types=1);

namespace PakuaOS\Verification;

final class HashVerifier
{
    public static function verify(string $filePath, string $expectedHash, string $algo = 'sha256'): bool
    {
        if (!file_exists($filePath)) return false;
        $actual = self::computeHash($filePath, $algo);
        return hash_equals(strtolower($expectedHash), strtolower($actual));
    }

    public static function computeHash(string $filePath, string $algo = 'sha256'): string
    {
        return hash_file($algo, $filePath) ?: '';
    }

    public static function displayVerification(string $filePath, ?string $expectedHash, string $algo = 'sha256'): string
    {
        if (!file_exists($filePath)) {
            return "\033[31m  ✗ File not found\033[0m";
        }

        $actual = self::computeHash($filePath, $algo);
        $output = "  \033[36m{$algo}:\033[0m {$actual}\n";

        if ($expectedHash) {
            if (hash_equals(strtolower($expectedHash), strtolower($actual))) {
                $output .= "  \033[32m✓ Checksum verified\033[0m";
            } else {
                $output .= "  \033[31m✗ Checksum mismatch!\033[0m";
                $output .= "\n  \033[33mExpected:\033[0m {$expectedHash}";
            }
        } else {
            $output .= "  \033[33m⚠ No reference checksum provided\033[0m";
        }

        return $output;
    }
}

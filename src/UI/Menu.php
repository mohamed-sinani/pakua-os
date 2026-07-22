<?php

declare(strict_types=1);

namespace PakuaOS\UI;

final class Menu
{
    private static function readLine(): ?string
    {
        $handle = fopen('php://stdin', 'r');
        if ($handle === false) return null;
        $line = fgets($handle);
        fclose($handle);
        return $line === false ? null : trim($line);
    }

    public static function select(string $title, array $options, bool $allowBack = true): ?int
    {
        echo "\n";
        echo Theme::separator($title) . "\n";
        echo "\n";

        foreach ($options as $i => $option) {
            $num = Theme::cyan(str_pad((string)($i + 1), 2));
            if (is_array($option)) {
                $label = $option['label'] ?? '';
                $desc = $option['desc'] ?? '';
                echo "  {$num}. " . Theme::bold($label);
                if ($desc) echo Theme::dim(" — {$desc}");
                echo "\n";
            } else {
                echo "  {$num}. " . Theme::bold($option) . "\n";
            }
        }

        if ($allowBack) {
            echo "\n  " . Theme::dim('0. Back') . "\n";
        }

        echo "\n";

        while (true) {
            echo '  ' . Theme::cyan('>') . ' ';
            $input = self::readLine();
            if ($input === null) return $allowBack ? null : null;
            if ($input === '' && $allowBack) return null;
            if (is_numeric($input)) {
                $choice = (int)$input;
                if ($allowBack && $choice === 0) return null;
                if ($choice >= 1 && $choice <= count($options)) {
                    return $choice - 1;
                }
            }
            echo '  ' . Theme::error("Invalid choice. Enter 1-" . count($options)) . "\n";
        }
    }

    public static function confirm(string $question): bool
    {
        echo "\n  " . Theme::yellow($question) . ' [Y/n] ';
        $input = self::readLine();
        if ($input === null) return false;
        $input = strtolower($input);
        return $input !== 'n' && $input !== 'no';
    }

    public static function prompt(string $label, string $default = ''): string
    {
        $suffix = $default ? Theme::dim(" ({$default})") : '';
        echo "\n  " . Theme::cyan('>') . ' ' . Theme::bold($label) . $suffix . ': ';
        $input = self::readLine();
        if ($input === null || $input === '') return $default;
        return $input;
    }
}

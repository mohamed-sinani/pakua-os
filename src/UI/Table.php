<?php

declare(strict_types=1);

namespace PakuaOS\UI;

final class Table
{
    public static function render(array $headers, array $rows, array $widths = []): void
    {
        if (empty($rows)) {
            echo Theme::dim("  No results found.") . "\n";
            return;
        }

        $colCount = count($headers);
        if (empty($widths)) {
            $widths = array_fill(0, $colCount, 20);
            foreach ($rows as $row) {
                foreach ($row as $i => $cell) {
                    $visible = preg_replace('/\x1b\[[0-9;]*m/', '', (string)$cell);
                    $widths[$i] = max($widths[$i], mb_strlen($visible) + 2);
                }
            }
            foreach ($headers as $i => $h) {
                $visible = preg_replace('/\x1b\[[0-9;]*m/', '', $h);
                $widths[$i] = max($widths[$i], mb_strlen($visible) + 2);
            }
        }

        $top    = '  ┌' . implode('┬', array_map(fn($w) => str_repeat('─', $w), $widths)) . '┐';
        $bottom = '  └' . implode('┴', array_map(fn($w) => str_repeat('─', $w), $widths)) . '┘';

        echo $top . "\n";

        $headerLine = '  │';
        foreach ($headers as $i => $h) {
            $headerLine .= ' ' . Theme::bold(self::pad($h, $widths[$i])) . '│';
        }
        echo $headerLine . "\n";
        echo $bottom . "\n";

        foreach ($rows as $row) {
            $line = '  │';
            foreach ($row as $i => $cell) {
                $line .= ' ' . self::pad((string)$cell, $widths[$i]) . '│';
            }
            echo $line . "\n";
        }

        echo $bottom . "\n";
    }

    private static function pad(string $text, int $width): string
    {
        $visible = preg_replace('/\x1b\[[0-9;]*m/', '', $text);
        $len = mb_strlen($visible);
        $pad = max(0, $width - $len);
        return $text . str_repeat(' ', $pad);
    }
}

<?php

declare(strict_types=1);

namespace PakuaOS\UI;

final class ProgressBar
{
    private int $total;
    private int $current = 0;
    private int $width;
    private float $startTime;
    private string $label;

    public function __construct(int $total, int $width = 30, string $label = '')
    {
        $this->total = max($total, 1);
        $this->width = $width;
        $this->startTime = microtime(true);
        $this->label = $label;
    }

    public function advance(int $bytes): void
    {
        $this->current = min($this->current + $bytes, $this->total);
        $this->draw();
    }

    public function set(int $current): void
    {
        $this->current = min(max($current, 0), $this->total);
        $this->draw();
    }

    public function finish(): void
    {
        $this->current = $this->total;
        $this->draw();
        echo "\n";
    }

    public function draw(): void
    {
        $pct = $this->total > 0 ? ($this->current / $this->total) * 100 : 0;
        $filled = (int)round(($this->current / $this->total) * $this->width);
        $empty = $this->width - $filled;

        $bar = Theme::cyan(str_repeat('█', $filled)) . Theme::dim(str_repeat('░', $empty));

        $elapsed = microtime(true) - $this->startTime;
        $speed = $elapsed > 0 ? $this->current / $elapsed : 0;
        $speedStr = self::formatBytes((int)$speed) . '/s';

        $eta = '∞';
        if ($speed > 0 && $this->current < $this->total) {
            $remaining = $this->total - $this->current;
            $sec = (int)ceil($remaining / $speed);
            $eta = self::formatTime($sec);
        }

        $currentStr = self::formatBytes($this->current);
        $totalStr = self::formatBytes($this->total);
        $pctStr = number_format($pct, 0) . '%';

        $output = "\r\033[K";
        if ($this->label) {
            $output .= '  ' . Theme::bold($this->label) . "\n";
        }
        $output .= '  ';
        $output .= '[' . $bar . '] ';
        $output .= ($pct >= 100 ? Theme::bold(Theme::green($pctStr)) : Theme::cyan($pctStr)) . ' ';
        $output .= Theme::dim($currentStr . ' / ' . $totalStr) . ' ';
        $output .= Theme::dim($speedStr) . ' ';
        $output .= Theme::dim('ETA: ' . $eta);

        if ($pct >= 100) {
            $output .= '  ' . Theme::green('✔');
        }

        echo $output;
        flush();
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int)floor((strlen((string)$bytes) - 1) / 3);
        $i = min($i, count($units) - 1);
        return sprintf('%.1f %s', $bytes / pow(1024, $i), $units[$i]);
    }

    public static function formatTime(int $seconds): string
    {
        if ($seconds < 60) return $seconds . 's';
        $h = (int)floor($seconds / 3600);
        $m = (int)floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m {$s}s";
    }
}

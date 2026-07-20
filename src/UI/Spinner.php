<?php

declare(strict_types=1);

namespace PakuaOS\UI;

final class Spinner
{
    private const FRAMES = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
    private string $message;
    private int $current = 0;
    private bool $running = false;
    private ?float $startTime = null;

    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    public function start(): void
    {
        $this->running = true;
        $this->startTime = microtime(true);
        $this->tick();
    }

    public function tick(): void
    {
        if (!$this->running) return;
        $frame = self::FRAMES[$this->current % count(self::FRAMES)];
        $this->current++;
        $elapsed = $this->startTime !== null ? round(microtime(true) - $this->startTime, 1) : 0;
        echo "\r\033[K  " . Theme::cyan($frame) . ' ' . Theme::dim($this->message) . Theme::gray(" ({$elapsed}s)");
    }

    public function stop(?string $result = null): void
    {
        $this->running = false;
        $elapsed = $this->startTime !== null ? round(microtime(true) - $this->startTime, 1) : 0;
        if ($result !== null) {
            echo "\r\033[K  " . Theme::green('✔') . ' ' . $result . Theme::gray(" ({$elapsed}s)") . "\n";
        } else {
            echo "\r\033[K";
        }
    }

    public function updateMessage(string $message): void
    {
        $this->message = $message;
    }
}

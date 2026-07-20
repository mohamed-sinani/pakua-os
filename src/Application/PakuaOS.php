<?php

declare(strict_types=1);

namespace PakuaOS\Application;

use Symfony\Component\Console\Application;
use PakuaOS\Commands\SearchCommand;
use PakuaOS\Commands\DownloadCommand;
use PakuaOS\Commands\HistoryCommand;
use PakuaOS\Commands\MenuCommand;

final class PakuaOS extends Application
{
    public function __construct()
    {
        parent::__construct('PakuaOS', '1.0');
    }

    public function boot(): void
    {
        $this->add(new SearchCommand());
        $this->add(new DownloadCommand());
        $this->add(new HistoryCommand());
        $this->add(new MenuCommand());
        $this->setDefaultCommand('menu', false);
    }
}

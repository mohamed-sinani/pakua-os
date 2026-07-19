<?php

declare(strict_types=1);

namespace PakuaOS\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PakuaOS\UI\Theme;
use PakuaOS\UI\Table;
use PakuaOS\UI\Menu;
use PakuaOS\Search\SearchEngine;

final class SearchCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('search')
            ->setDescription('Search for operating systems or software')
            ->setAliases(['find', 's'])
            ->addArgument('query', InputArgument::OPTIONAL, 'Search query');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $input->getArgument('query') ?? '';

        $engine = new SearchEngine();
        $results = $engine->search($query);

        if (empty($results)) {
            $output->writeln(Theme::error("\n  No results found for: {$query}"));
            return Command::SUCCESS;
        }

        $output->writeln('');
        $output->writeln(Theme::bold("  Found " . count($results) . " results for: " . Theme::cyan($query ?: '(all)')));
        $output->writeln('');

        $rows = [];
        foreach ($results as $i => $r) {
            $rows[] = [
                Theme::cyan((string)($i + 1)),
                Theme::bold($r['name']),
                $r['platform'],
                $r['type'],
                $r['verified'] ? Theme::success('✓ Verified') : Theme::dim('—'),
            ];
        }

        Table::render(
            ['#', 'Name', 'Platform', 'Type', 'Security'],
            $rows,
            [4, 30, 18, 14, 14]
        );

        $choice = Menu::prompt("\n  Enter number to view details (0 to go back)");
        $idx = (int)$choice - 1;

        if ($idx >= 0 && $idx < count($results)) {
            $r = $results[$idx];
            echo "\n";
            echo Theme::separator("{$r['name']} Details") . "\n";
            echo "  \033[36mName:\033[0m       {$r['name']}\n";
            echo "  \033[36mPlatform:\033[0m   {$r['platform']}\n";
            echo "  \033[36mType:\033[0m       {$r['type']}\n";
            echo "  \033[36mSource:\033[0m     {$r['source']}\n";
            echo "  \033[36mURL:\033[0m        {$r['url']}\n";
            if (isset($r['publisher'])) {
                echo "  \033[36mPublisher:\033[0m  {$r['publisher']}\n";
            }
            echo "  \033[36mVerified:\033[0m  " . ($r['verified'] ? Theme::success('✓ Yes') : Theme::warning('No')) . "\n";
            echo "\n";

            if (Menu::confirm("  Start download?")) {
                $cat = $r['category'] ?? null;
                $dlCategory = in_array($cat, ['linux', 'windows', 'macos']) ? 'os' : 'programs';
                $dl = new \PakuaOS\Downloader\Downloader();
                $dl->download($r['url'], $r['name'] . ' ' . $r['platform'], null, 'sha256', $dlCategory);
            }
        }

        return Command::SUCCESS;
    }
}

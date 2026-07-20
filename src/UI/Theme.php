<?php

declare(strict_types=1);

namespace PakuaOS\UI;

final class Theme
{
    private static bool $colors = true;

    private const FG = [
        'black' => '30', 'red' => '31', 'green' => '32', 'yellow' => '33',
        'blue' => '34', 'magenta' => '35', 'cyan' => '36', 'white' => '37',
        'gray' => '90', 'brred' => '91', 'brgreen' => '92', 'bryellow' => '93',
        'brblue' => '94', 'brmagenta' => '95', 'brcyan' => '96', 'brwhite' => '97',
    ];

    public static function enableColors(bool $on): void { self::$colors = $on; }

    public static function c(string $text, string $color): string
    {
        if (!self::$colors) return $text;
        $code = self::FG[$color] ?? '37';
        return "\033[{$code}m{$text}\033[0m";
    }

    public static function bold(string $t): string   { return self::$colors ? "\033[1m{$t}\033[0m" : $t; }
    public static function dim(string $t): string    { return self::$colors ? "\033[2m{$t}\033[0m" : $t; }
    public static function italic(string $t): string { return self::$colors ? "\033[3m{$t}\033[0m" : $t; }
    public static function ul(string $t): string     { return self::$colors ? "\033[4m{$t}\033[0m" : $t; }

    public static function red(string $t): string    { return self::c($t, 'red'); }
    public static function green(string $t): string  { return self::c($t, 'green'); }
    public static function yellow(string $t): string { return self::c($t, 'yellow'); }
    public static function blue(string $t): string   { return self::c($t, 'blue'); }
    public static function cyan(string $t): string   { return self::c($t, 'cyan'); }
    public static function magenta(string $t): string { return self::c($t, 'magenta'); }
    public static function white(string $t): string  { return self::c($t, 'white'); }
    public static function gray(string $t): string   { return self::c($t, 'gray'); }

    public static function success(string $t): string { return self::green($t); }
    public static function error(string $t): string   { return self::red($t); }
    public static function warning(string $t): string { return self::yellow($t); }
    public static function info(string $t): string    { return self::cyan($t); }

    public static function banner(): void
    {
        $cyan = fn(string $t) => self::c($t, 'cyan');
        $white = fn(string $t) => self::c($t, 'white');
        $dim = fn(string $t) => self::c($t, 'gray');
        $green = fn(string $t) => self::c($t, 'green');
        $blue = fn(string $t) => self::c($t, 'blue');
        $bold = fn(string $t) => self::bold($t);

        $w = 78;
        $line = fn() => $cyan(str_repeat('═', $w));

        echo "\n";
        echo "  " . $cyan(str_repeat('═', $w)) . "\n";
        echo "  " . $cyan('═') . str_repeat(' ', $w - 2) . $cyan('═') . "\n";
        echo "  " . $cyan('═') . str_repeat(' ', 26) . $bold($white('PakuaOS v1.0')) . str_repeat(' ', 26) . $cyan('═') . "\n";
        echo "  " . $cyan('═') . str_repeat(' ', 16) . $dim('Software & Operating System Hub') . str_repeat(' ', 16) . $cyan('═') . "\n";
        echo "  " . $cyan('═') . str_repeat(' ', 20) . $green('Find') . ' ' . $dim('•') . ' ' . $green('Verify') . ' ' . $dim('•') . ' ' . $green('Download') . ' ' . $dim('Safely') . str_repeat(' ', 18) . $cyan('═') . "\n";
        echo "  " . $cyan('═') . str_repeat(' ', $w - 2) . $cyan('═') . "\n";
        echo "  " . $cyan(str_repeat('═', $w)) . "\n";
        echo "\n";

        // PakuaOS ASCII art
        $art = [
            'cyan' => [
                '██████╗  █████╗ ██╗  ██╗██╗   ██╗ █████╗  ██████╗ ███████╗',
                '██╔══██╗██╔══██╗██║ ██╔╝██║   ██║██╔══██╗██╔═══██╗██╔════╝',
                '██████╔╝███████║█████╔╝ ██║   ██║███████║██║   ██║███████╗',
                '██╔═══╝ ██╔══██║██╔═██╗ ██║   ██║██╔══██║██║   ██║╚════██║',
                '██║     ██║  ██║██║  ██╗╚██████╔╝██║  ██║╚██████╔╝███████║',
                '╚═╝     ╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═╝ ╚═════╝ ╚══════╝',
            ],
        ];

        foreach ($art['cyan'] as $line) {
            echo '  ' . $cyan($line) . "\n";
        }
        echo "\n";
        echo '  ' . str_repeat(' ', 20) . $dim('Software & Operating System Hub') . "\n";
        echo '  ' . str_repeat(' ', 26) . $green('Find') . ' ' . $dim('•') . ' ' . $green('Verify') . ' ' . $dim('•') . ' ' . $green('Download') . "\n";
        echo "\n";

        // Developer info box
        echo "  " . $cyan(str_repeat('─', $w)) . "\n";
        echo "\n";
        echo '  ' . $bold($white('Developer')) . ' : ' . $cyan('Mohamed Sinani') . $dim(' (Dev_Meddy)') . "\n";
        echo '  ' . $bold($white('Website')) . '   : ' . $blue('https://dev.mohamedsinani.com') . "\n";
        echo '  ' . $bold($white('GitHub')) . '    : ' . $blue('https://github.com/mohamed-sinani') . "\n";
        echo '  ' . $bold($white('Instagram')) . ' : ' . $blue('https://instagram.com/dev_meddy') . "\n";
        echo '  ' . $bold($white('Email')) . '     : ' . $blue('dev@mohamedsinani.com') . "\n";
        echo "\n";
        echo "  " . $cyan(str_repeat('─', $w)) . "\n";
        echo "\n";

        // Welcome box
        echo "  " . $cyan('╭' . str_repeat('─', $w - 2) . '╮') . "\n";
        echo "  " . $cyan('│') . str_repeat(' ', $w - 2) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . str_repeat(' ', 28) . $bold($white('PakuaOS v1.0')) . str_repeat(' ', 28) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . str_repeat(' ', 16) . $dim('Software & Operating System Hub') . str_repeat(' ', 16) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . str_repeat(' ', $w - 2) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . str_repeat(' ', 18) . $green('Find') . ' ' . $dim('•') . ' ' . $green('Verify') . ' ' . $dim('•') . ' ' . $green('Download') . ' ' . $dim('Safely') . str_repeat(' ', 18) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . str_repeat(' ', $w - 2) . $cyan('│') . "\n";
        echo "  " . $cyan('├' . str_repeat('─', $w - 2) . '┤') . "\n";
        echo "  " . $cyan('│') . ' ' . $bold($white('Developer')) . ' : ' . $cyan('Mohamed Sinani') . $dim(' (Dev_Meddy)') . str_repeat(' ', $w - 49 - 4) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . ' ' . $bold($white('GitHub')) . '    : ' . $blue('github.com/mohamed-sinani') . str_repeat(' ', $w - 38 - 4) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . ' ' . $bold($white('Website')) . '   : ' . $blue('dev.mohamedsinani.com') . str_repeat(' ', $w - 36 - 4) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . ' ' . $bold($white('Instagram')) . ' : ' . $blue('instagram.com/dev_meddy') . str_repeat(' ', $w - 34 - 4) . $cyan('│') . "\n";
        echo "  " . $cyan('│') . ' ' . $bold($white('Email')) . '     : ' . $blue('dev@mohamedsinani.com') . str_repeat(' ', $w - 33 - 4) . $cyan('│') . "\n";
        echo "  " . $cyan('╰' . str_repeat('─', $w - 2) . '╯') . "\n";
        echo "\n";
    }

    public static function separator(string $title = ''): string
    {
        if ($title !== '') {
            return self::dim("  ── {$title} " . str_repeat('─', max(0, 60 - mb_strlen($title))));
        }
        return self::dim('  ' . str_repeat('─', 78));
    }

    public static function header(string $title): string
    {
        $cyan = fn(string $t) => self::c($t, 'cyan');
        $w = 78;
        return "  " . $cyan(str_repeat('=', $w)) . "\n  " . self::bold("  {$title}") . "\n  " . $cyan(str_repeat('=', $w));
    }

    public static function successBox(string $msg): string
    {
        return "\n  " . self::green('✔') . ' ' . $msg;
    }

    public static function errorBox(string $msg): string
    {
        return "\n  " . self::red('✖') . ' ' . $msg;
    }

    public static function warningBox(string $msg): string
    {
        return "\n  " . self::yellow('⚠') . ' ' . $msg;
    }

    public static function infoBox(string $msg): string
    {
        return "\n  " . self::cyan('ℹ') . ' ' . $msg;
    }

    public static function savedPath(string $path): string
    {
        return "\n  " . self::bold(self::green('Saved to:')) . "\n\n  " . self::cyan($path) . "\n";
    }
}

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
        $lines = [
            '',
            '  ' . self::cyan('╔══════════════════════════════════════════╗'),
            '  ' . self::cyan('║') . '                                          ' . self::cyan('║'),
            '  ' . self::cyan('║') . '   ' . self::bold(self::white('PakuaOS')) . '                                  ' . self::cyan('║'),
            '  ' . self::cyan('║') . '   ' . self::dim('Software & Operating System Hub') . '      ' . self::cyan('║'),
            '  ' . self::cyan('║') . '   ' . self::green('Find. Verify. Download.') . '               ' . self::cyan('║'),
            '  ' . self::cyan('║') . '                                          ' . self::cyan('║'),
            '  ' . self::cyan('╚══════════════════════════════════════════╝'),
            '',
        ];
        foreach ($lines as $line) echo $line . "\n";
    }

    public static function separator(string $title = ''): string
    {
        if ($title !== '') {
            return self::dim("  ── {$title} " . str_repeat('─', max(0, 38 - mb_strlen($title))));
        }
        return self::dim('  ' . str_repeat('─', 42));
    }
}

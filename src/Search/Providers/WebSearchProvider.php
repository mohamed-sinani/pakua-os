<?php

declare(strict_types=1);

namespace PakuaOS\Search\Providers;

final class WebSearchProvider implements Provider
{
    public function getName(): string { return 'Web Search'; }
    public function getCategory(): string { return 'software'; }
    public function isAvailable(): bool { return true; }

    private array $knownSites = [
        'github.com'          => 'GitHub',
        'gitlab.com'          => 'GitLab',
        'sourceforge.net'     => 'SourceForge',
        'fossHub.com'         => 'FossHub',
        'download.cnet.com'   => 'CNET Download',
        'majorgeeks.com'      => 'MajorGeeks',
        'filehippo.com'       => 'FileHippo',
        'softpedia.com'       => 'Softpedia',
        'chrome.google.com'   => 'Chrome Web Store',
        'microsoft.com'       => 'Microsoft',
        'apple.com'           => 'Apple',
        'google.com'          => 'Google',
        'mozilla.org'         => 'Mozilla',
        'docker.com'          => 'Docker',
        'nodejs.org'          => 'Node.js',
        'python.org'          => 'Python',
        'jetbrains.com'       => 'JetBrains',
        'android.com'         => 'Android',
        'ubuntu.com'          => 'Ubuntu',
        'debian.org'          => 'Debian',
        'fedoraproject.org'   => 'Fedora',
        'archlinux.org'       => 'Arch Linux',
        'kali.org'            => 'Kali Linux',
        'linuxmint.com'       => 'Linux Mint',
        'visualstudio.microsoft.com' => 'Visual Studio',
        'code.visualstudio.com' => 'VS Code',
        'sublimetext.com'     => 'Sublime Text',
        'notepad-plus-plus.org' => 'Notepad++',
        'gimp.org'            => 'GIMP',
        'inkscape.org'        => 'Inkscape',
        'blender.org'         => 'Blender',
        'obsproject.com'      => 'OBS Studio',
        'signal.org'          => 'Signal',
        'telegram.org'        => 'Telegram',
        'discord.com'         => 'Discord',
        'zoom.us'             => 'Zoom',
        'spotify.com'         => 'Spotify',
        '1password.com'       => '1Password',
        'bitwarden.com'       => 'Bitwarden',
        'veracrypt.fr'        => 'VeraCrypt',
        'qbittorrent.org'     => 'qBittorrent',
        'transmissionbt.com'  => 'Transmission',
        'handbrake.fr'        => 'HandBrake',
        'mpv.io'              => 'mpv',
        'ffmpeg.org'          => 'FFmpeg',
        'curl.se'             => 'cURL',
        '7-zip.org'           => '7-Zip',
        'notepadplusplus.org' => 'Notepad++',
        'brave.com'           => 'Brave',
        'opera.com'           => 'Opera',
        'vivaldi.com'         => 'Vivaldi',
        'torproject.org'      => 'Tor Browser',
        'proton.me'           => 'Proton',
        'mega.nz'             => 'MEGA',
    ];

    public function search(string $query): array
    {
        $results = [];

        // Strategy 1: Search DuckDuckGo HTML
        $webResults = $this->searchDuckDuckGo($query);
        $results = array_merge($results, $webResults);

        // Strategy 2: Try known sites directly
        $knownResults = $this->searchKnownSites($query);
        $results = array_merge($results, $knownResults);

        return $results;
    }

    private function searchDuckDuckGo(string $query): array
    {
        $results = [];
        $searchQuery = $query . ' download latest';
        $url = 'https://html.duckduckgo.com/html/?q=' . urlencode($searchQuery);

        $body = $this->httpGet($url);
        if (!$body) return [];

        // Extract result links from DuckDuckGo HTML
        // Pattern: <a rel="nofollow" class="result__a" href="...">title</a>
        preg_match_all('/<a[^>]+class="result__a"[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/s', $body, $links);
        preg_match_all('/<a[^>]+class="result__snippet"[^>]*>(.*?)<\/a>/s', $body, $snippets);

        $count = min(count($links[1]), 15);

        for ($i = 0; $i < $count; $i++) {
            $href = $links[1][$i];
            $title = strip_tags($links[2][$i]);
            $snippet = isset($snippets[1][$i]) ? strip_tags($snippets[1][$i]) : '';

            // DuckDuckGo wraps URLs in redirects, extract the actual URL
            if (preg_match('/uddg=([^&]+)/', $href, $m)) {
                $href = urldecode($m[1]);
            }

            if (!filter_var($href, FILTER_VALIDATE_URL)) continue;

            $host = parse_url($href, PHP_URL_HOST) ?? '';
            $path = parse_url($href, PHP_URL_PATH) ?? '';
            $lPath = strtolower($path);

            // Check if it looks like a download page
            $isDownloadPage = (
                str_contains($lPath, 'download') ||
                str_ends_with($lPath, '.exe') ||
                str_ends_with($lPath, '.msi') ||
                str_ends_with($lPath, '.deb') ||
                str_ends_with($lPath, '.rpm') ||
                str_ends_with($lPath, '.dmg') ||
                str_ends_with($lPath, '.pkg') ||
                str_ends_with($lPath, '.appimage') ||
                str_ends_with($lPath, '.tar.gz') ||
                str_ends_with($lPath, '.zip') ||
                str_ends_with($lPath, '.apk') ||
                str_contains($host, 'github.com') ||
                str_contains($host, 'sourceforge.net')
            );

            $source = $this->knownSites[$host] ?? $host;

            $platform = $this->detectPlatformFromUrl($href, $title . ' ' . $snippet);
            $type = $this->detectTypeFromUrl($href, $title);

            $results[] = [
                'name'      => $title,
                'version'   => 'latest',
                'platform'  => $platform,
                'type'      => $type,
                'url'       => $href,
                'source'    => $source,
                'verified'  => $isDownloadPage,
                'hash_type' => null,
                'category'  => 'software',
                'provider'  => 'Web Search',
                'publisher' => $source,
                'desc'      => $snippet,
                'is_download_page' => $isDownloadPage,
            ];
        }

        return $results;
    }

    private function searchKnownSites(string $query): array
    {
        $results = [];
        $lq = strtolower($query);

        // Match against known sites
        foreach ($this->knownSites as $domain => $source) {
            if (!str_contains($domain, $lq) && !$this->fuzzyMatch($lq, $domain)) continue;

            $baseUrl = 'https://' . $domain;
            $downloadUrl = $this->guessDownloadUrl($domain, $query);
            if (!$downloadUrl) continue;

            $platform = $this->detectPlatformFromUrl($downloadUrl, $query);

            $results[] = [
                'name'      => $source . ' — ' . ucfirst($query),
                'version'   => 'latest',
                'platform'  => $platform,
                'type'      => $this->detectTypeFromUrl($downloadUrl, $query),
                'url'       => $downloadUrl,
                'source'    => $source,
                'verified'  => true,
                'hash_type' => null,
                'category'  => 'software',
                'provider'  => 'Web Search',
                'publisher' => $source,
                'desc'      => "Official download from {$source}",
                'is_download_page' => true,
            ];
        }

        return $results;
    }

    private function guessDownloadUrl(string $domain, string $query): ?string
    {
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($query));
        $slug = trim($slug, '-');

        $candidates = [
            "https://{$domain}/download",
            "https://www.{$domain}/download",
            "https://{$domain}/downloads",
            "https://www.{$domain}/downloads",
            "https://{$domain}/{$slug}/download",
            "https://{$domain}/{$slug}",
            "https://www.{$domain}/{$slug}/download",
        ];

        foreach ($candidates as $url) {
            $head = $this->httpHead($url);
            if ($head !== null && $head >= 200 && $head < 400) {
                return $url;
            }
        }

        return null;
    }

    private function fuzzyMatch(string $query, string $domain): bool
    {
        $clean = preg_replace('/[^a-z0-9]/', '', $domain);
        $qClean = preg_replace('/[^a-z0-9]/', '', $query);
        return str_contains($clean, $qClean) || str_contains($qClean, $clean);
    }

    private function detectPlatformFromUrl(string $url, string $context): string
    {
        $combined = strtolower($url . ' ' . $context);
        if (str_contains($combined, 'windows') || str_contains($combined, 'win64') || str_contains($combined, 'win32') || str_contains($combined, '.exe') || str_contains($combined, '.msi')) return 'Windows';
        if (str_contains($combined, 'linux') || str_contains($combined, 'ubuntu') || str_contains($combined, 'debian') || str_contains($combined, '.deb') || str_contains($combined, '.rpm') || str_contains($combined, '.appimage') || str_contains($combined, '.flatpak')) return 'Linux';
        if (str_contains($combined, 'macos') || str_contains($combined, 'darwin') || str_contains($combined, '.dmg') || str_contains($combined, '.pkg') || str_contains($combined, 'osx')) return 'macOS';
        if (str_contains($combined, 'android') || str_contains($combined, '.apk')) return 'Android';
        return 'Universal';
    }

    private function detectTypeFromUrl(string $url, string $context): string
    {
        $combined = strtolower($url . ' ' . $context);
        if (str_ends_with($combined, '.exe') || str_ends_with($combined, '.msi')) return 'Installer';
        if (str_ends_with($combined, '.deb')) return 'Package (.deb)';
        if (str_ends_with($combined, '.rpm')) return 'Package (.rpm)';
        if (str_ends_with($combined, '.dmg')) return 'DMG';
        if (str_ends_with($combined, '.pkg')) return 'Package (.pkg)';
        if (str_ends_with($combined, '.appimage')) return 'AppImage';
        if (str_ends_with($combined, '.tar.gz') || str_ends_with($combined, '.tgz')) return 'Archive';
        if (str_ends_with($combined, '.zip')) return 'Archive (.zip)';
        if (str_ends_with($combined, '.apk')) return 'APK';
        if (str_contains($combined, 'download')) return 'Download Page';
        return 'Link';
    }

    private function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_ENCODING       => '',
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200 && $body) ? $body : null;
    }

    private function httpHead(string $url): ?int
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PakuaOS/1.0',
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code > 0 ? $code : null;
    }
}

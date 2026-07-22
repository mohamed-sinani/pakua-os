# AGENTS.md — PakuaOS Development Guide

> **Purpose:** Quick reference for AI agents and developers working on this codebase.

---

## Project Overview

PakuaOS is a PHP CLI tool for finding and downloading operating systems. It uses Symfony Console for the CLI framework and cURL for downloads.

- **Language:** PHP 8.2+
- **Framework:** Symfony Console 7.x
- **HTTP:** cURL (raw PHP functions)
- **Storage:** JSON files at `~/.pakuaos/`
- **Entry point:** `pakuaos`

---

## Directory Structure

```
pakuaos                              # CLI entry point
src/
├── Application/PakuaOS.php          # App boot & command registration
├── Commands/
│   ├── MenuCommand.php              # Default: interactive menu
│   ├── SearchCommand.php            # `pakuaos search <query>`
│   ├── DownloadCommand.php          # `pakuaos download <url>`
│   └── HistoryCommand.php           # `pakuaos history`
├── Database/Database.php            # JSON persistence (singleton)
├── Downloader/Downloader.php        # cURL download engine + fallback
├── Search/
│   ├── SearchEngine.php             # Provider orchestrator
│   └── Providers/
│       ├── Provider.php             # Interface
│       ├── LinuxProvider.php        # Curated: Ubuntu, Debian, Fedora, Arch, Kali, Mint, openSUSE
│       ├── WindowsProvider.php      # Curated: Win11 25H2, Win11 23H2, Win10 22H2, Server 2022
│       └── MacOSProvider.php        # Curated: Sequoia, Sonoma, Ventura
├── UI/
│   ├── Theme.php                    # ANSI colors, banner, separators
│   ├── Table.php                    # Box-drawing table renderer
│   ├── ProgressBar.php              # Download progress with speed/ETA
│   ├── Spinner.php                  # Braille-dot loading animation
│   └── Menu.php                     # Interactive select/confirm/prompt
└── Verification/HashVerifier.php    # SHA256 hash verification
```

---

## Architecture

### Command Flow
```
pakuaos → PakuaOS::boot() → MenuCommand (default)
  ├── Operating Systems → handleOS() → OS providers → distro/version/arch picker → download
  ├── Download by URL   → handleDirectDownload() → Downloader
  ├── My Downloads      → handleHistory() → Database → Table
  └── Settings          → handleSettings() → Database
```

### Provider System
- **Curated providers** (offline): LinuxProvider, WindowsProvider, MacOSProvider
- All implement `Provider` interface: `getName()`, `getCategory()`, `search(query)`, `isAvailable()`
- SearchEngine orchestrates, deduplicates by `md5(name|version|platform|url)`

### Download Flow
```
Downloader::download(url, name, hash, algo, category, fallbackUrls)
  → resolveDir(category)  # 'os' → ~/Downloads/Operating Systems/
  → tryDownload(url)      # HEAD for size → cURL GET → .part file → ProgressBar
  → on failure: try each fallbackUrl
  → rename .part → final file
  → HashVerifier::verify() (if hash provided)
  → Database::addDownload() (persist record)
```

### Database Schema (JSON at ~/.pakuaos/)
**downloads.json:**
```json
{
  "id": 1,
  "name": "filename",
  "url": "https://...",
  "file_path": "/full/path/to/file",
  "file_size": 12345678,
  "downloaded": 12345678,
  "status": "completed|failed|paused|queued",
  "hash_type": "sha256",
  "hash_value": "...",
  "source": "hostname",
  "category": "os|other",
  "created_at": "2026-07-20 12:00:00",
  "updated_at": "2026-07-20 12:05:00"
}
```

---

## Key Patterns to Follow

### Adding a new OS distro
Edit `src/Search/Providers/LinuxProvider.php` or `WindowsProvider.php`. Add entry to `$versions` array with: name, architectures, url, size, source, verified, distro_label, distro_desc, fallback_urls.

### Adding a new search provider
1. Create `src/Search/Providers/MyProvider.php` implementing `Provider`
2. Register in `src/Search/SearchEngine.php` constructor

### Table rendering
```php
Table::render(
    ['Header1', 'Header2'],
    [
        [Theme::cyan('val1'), Theme::bold('val2')],
    ],
    [12, 30]  // column widths
);
```

### Menu selection
```php
$choice = Menu::select('Title', [
    ['label' => 'Option 1', 'desc' => 'Description'],
    ['label' => 'Option 2', 'desc' => 'Description'],
]);
```

### Theme colors
- `Theme::success()` → green, `Theme::error()` → red, `Theme::warning()` → yellow, `Theme::info()` → cyan
- `Theme::bold()`, `Theme::dim()`, `Theme::separator()`, `Theme::successBox()`

---

## Important Notes

1. **JSON database** — Database is plain JSON files at `~/.pakuaos/`
2. **cURL only** — All HTTP uses raw cURL functions
3. **Spinner is static** — Shows one frame, no animation (PHP CLI limitation)
4. **Cross-platform** — Downloader handles both Unix and Windows paths
5. **Fallback URLs** — WindowsProvider has archive.org mirrors for when Microsoft links expire
6. **Resume support** — Downloads use `.part` files with `CURLOPT_RESUME_FROM`

---

## Commands Reference

```bash
# Run interactive menu (default)
./pakuaos

# Search for operating systems
./pakuaos search ubuntu
./pakuaos find windows

# Download by URL
./pakuaos download https://example.com/file.zip
./pakuaos dl https://example.com/file.zip

# View history
./pakuaos history
./pakuaos ls
```

---

## Last Updated

- **Date:** 2026-07-22
- **Version:** 2.0
- **Changes:** Removed all software/app downloading functionality. PakuaOS now focuses exclusively on operating systems. Renamed composer package to `pakuaos/app`.

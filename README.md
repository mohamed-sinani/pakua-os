# PakuaOS

> **Find. Verify. Download Operating Systems.**

PakuaOS is a CLI tool for finding and downloading operating systems. It pulls from official sources — Ubuntu mirrors, Microsoft servers, Kali, Fedora, and more. Includes fallback URLs for reliability.

```
    ███████╗  ██████╗ ███████╗
    ██╔════╝ ██╔═══██╗██╔════╝
    ███████╗ ██║   ██║███████╗
    ╚════██║ ██║   ██║╚════██║
    ███████║ ╚██████╔╝███████║
    ╚══════╝  ╚═════╝ ╚══════╝

       Operating System Downloader
         Find • Verify • Download
```

---

## Install

### Step 1 — Install PHP 8.2+

```bash
# Check version
php -v

# Ubuntu/Debian
sudo apt install php8.2-cli php8.2-curl php8.2-mbstring

# macOS
brew install php
```

### Step 2 — Install PakuaOS

```bash
composer create-project pakuaos/app
```

### Step 3 — Run

```bash
cd app
php pakuaos
```

---

## How to Use

### Browse & Download an OS

```
1. Select "Operating Systems"
2. Pick a family: Linux / Windows / macOS
3. Pick a distro: Ubuntu / Debian / Fedora / Arch / Kali / Mint / openSUSE
4. Pick a version
5. Pick architecture: amd64 / arm64
6. Confirm download
```

Live progress bar shows percentage, speed, and ETA:

```
  [████████████████████░░░░░░░░░░░░░░░░░░░░] 45%  2.1 GB / 4.7 GB  12.3 MB/s  ETA 3m 12s
```

### Download by URL

```
1. Select "Download by URL"
2. Paste any download link
3. Enter a filename
4. Download starts
```

### View History

```
1. Select "My Downloads"
2. See all past downloads with status and size
3. Resume interrupted downloads
```

### Navigate

- Type a number to select
- Type `0` to go back to the previous step

---

## Supported Systems

| Family | Distros |
|--------|---------|
| **Linux** | Ubuntu 24.04 / 22.04, Debian 12, Fedora 42 / 41, Arch Linux, Kali 2025.4, Linux Mint 22.1, openSUSE Leap 15.6 |
| **Windows** | Windows 11 25H2, Windows 11 23H2, Windows 10 22H2, Server 2022 |
| **macOS** | Sequoia 15, Sonoma 14, Ventura 13 |

---

## Features

- **Official sources only** — Downloads from official mirrors and publisher sites
- **Fallback URLs** — Automatically tries backup sources if primary fails
- **Live progress** — Real-time percentage, speed, and ETA
- **Resume support** — Interrupted downloads resume from where they stopped
- **SHA256 verification** — Hash checking for supported downloads
- **Download history** — All downloads tracked locally

---

## CLI Commands

| Command | What it does |
|---------|--------------|
| `php pakuaos` | Launch interactive menu |
| `php pakuaos search ubuntu` | Search for an OS |
| `php pakuaos download <url>` | Download from a URL |
| `php pakuaos history` | View past downloads |

---

## Where Files Are Saved

```
~/Downloads/PakuaOS/
└── Operating Systems/
    ├── ubuntu-24.04.2-desktop-amd64.iso
    ├── kali-linux-2025.4-installer-amd64.iso
    └── ...
```

---

## Requirements

- PHP 8.2+
- Extensions: `curl`, `json`, `mbstring`
- Internet connection

---

## License

MIT

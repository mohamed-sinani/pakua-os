# PakuaOS

> **Find. Verify. Download Operating Systems.**

PakuaOS is a CLI tool for downloading operating systems (Ubuntu, Windows, Kali, Fedora, etc.) from official sources with live progress and resume support.

---

## How to Install 

### Windows

**Step 1** — Open **PowerShell** (right-click Start → Terminal)

**Step 2** — Install PHP

```powershell
winget install PHP.PHP
```

**Step 3** — Install Composer

```powershell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=C:\bin --filename=composer
```

**Step 4** — Install PakuaOS

```powershell
composer require pakuaos/pakua
cd app
```

**Step 5** — Run

```powershell
vendor/bin/pakuaos
```

---

### Linux (Ubuntu/Debian)

**Step 1** — Open **Terminal**

**Step 2** — Install PHP

```bash
sudo apt update
sudo apt install php-cli php-curl php-mbstring
```

**Step 3** — Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

**Step 4** — Install PakuaOS

```bash
composer require pakuaos/pakua
cd app
```

**Step 5** — Run

```bash
vendor/bin/pakuaos
```

---

### macOS

**Step 1** — Open **Terminal** (Spotlight → type "Terminal")

**Step 2** — Install Homebrew (if you don't have it)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

**Step 3** — Install PHP and Composer

```bash
brew install php
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

**Step 4** — Install PakuaOS

```bash
composer require pakuaos/pakua
cd app
```

**Step 5** — Run

```bash
vendor/bin/pakuaos
```

---

## How to Use

### Download an Operating System

```
1. Type 1 → Operating Systems
2. Pick a family: Linux / Windows / macOS
3. Pick a distro: Ubuntu / Debian / Kali / Fedora / Windows 11 ...
4. Pick a version
5. Pick architecture: amd64 / arm64
6. Type y → download starts
```

You'll see a live progress bar:

```
  [████████████████████░░░░░░░░░░░░░░░░░░░░] 45%  2.1 GB / 4.7 GB  12.3 MB/s  ETA 3m 12s
```

### Download by URL

```
1. Type 2 → Download by URL
2. Paste the download link
3. Type a filename
4. Download starts
```

### View History

```
1. Type 3 → My Downloads
2. See all past downloads with status
3. Resume interrupted downloads
```

### Go Back

At any step, type `0` to go back to the previous menu.

---

## Supported Systems

| Linux | Windows | macOS |
|-------|---------|-------|
| Ubuntu 24.04 / 22.04 | Windows 11 25H2 | Sequoia 15 |
| Debian 12 | Windows 11 23H2 | Sonoma 14 |
| Fedora 42 / 41 | Windows 10 22H2 | Ventura 13 |
| Arch Linux | Server 2022 | |
| Kali 2025.4 | | |
| Linux Mint 22.1 | | |
| openSUSE Leap 15.6 | | |

---

## Features

- Official mirrors and sources only
- Fallback URLs if primary source fails
- Live progress with speed and ETA
- Resume interrupted downloads
- SHA256 hash verification
- Download history tracking

---

## License

MIT

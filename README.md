# PakuaOS

**Find. Verify. Install.**

PakuaOS is a universal operating system and software setup finder CLI. It helps you discover and download verified OS ISOs and software installers from official sources.

```
╔══════════════════════════════════════════╗
║              PakuaOS                     ║
║   Software & Operating System Hub        ║
║        Find. Verify. Download.           ║
╚══════════════════════════════════════════╝
```

## Installation

```bash
composer global require pakuaos/pakua
```

## Usage

### Interactive mode

```bash
pakuaos
```

### Search

```bash
pakuaos search vscode
pakuaos find ubuntu
pakuaos s docker
```

### Download

```bash
pakuaos download <url> [filename]
pakuaos dl <url> [filename]
```

### History

```bash
pakuaos history
pakuaos ls
```

## Categories

### Operating Systems

| OS | Versions |
|----|----------|
| **Linux** | Ubuntu, Debian, Fedora, Arch, Kali, Mint, openSUSE |
| **Windows** | Windows 11, 10, Server 2022 |
| **macOS** | Sequoia, Sonoma, Ventura |

### Software

| Category | Apps |
|----------|------|
| **Browsers** | Firefox, Chrome, Edge, Brave |
| **Development** | VS Code, Android Studio, Docker, Git, Node.js |
| **Security** | Wireshark, Nmap |
| **Productivity** | LibreOffice |
| **Utilities** | VLC, 7-Zip |

## Source Providers

All software is sourced from verified, official URLs:

- Official Microsoft downloads
- Official Ubuntu / Fedora / Debian mirrors
- Mozilla / Google / Docker official channels
- GitHub releases

## Verification

Each download shows security information before proceeding:

```
Security Check

Publisher:  Mozilla
Source:     Official
Checksum:   SHA256 provided

Download? [Y/n]
```

## Download History

All downloads are recorded to a local SQLite database at `~/.pakuaos/pakuaos.sqlite`.

```
pakuaos history
```

## Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Download directory | `~/.pakuaos/downloads` | Where files are saved |

## Project Structure

```
bin/pakuaos                          # CLI entry point
src/
├── Application/PakuaOS.php          # Console application
├── Commands/
│   ├── SearchCommand.php            # search / find / s
│   ├── DownloadCommand.php          # download / dl / get
│   ├── HistoryCommand.php           # history / ls / list
│   └── MenuCommand.php             # Interactive main menu
├── UI/
│   ├── Theme.php                    # Colors & styling
│   ├── Table.php                    # Table rendering
│   ├── ProgressBar.php              # Download progress bar
│   └── Menu.php                     # Interactive menus
├── Search/
│   ├── SearchEngine.php             # Search orchestrator
│   └── Providers/
│       ├── Provider.php             # Provider interface
│       ├── LinuxProvider.php        # Linux distros
│       ├── WindowsProvider.php      # Windows ISOs
│       ├── MacOSProvider.php        # macOS versions
│       └── SoftwareProvider.php     # Desktop software
├── Downloader/
│   └── Downloader.php               # HTTP download with resume
├── Verification/
│   └── HashVerifier.php             # SHA256 hash verification
└── Database/
    └── Database.php                 # SQLite storage
```

## Requirements

- PHP 8.2+
- Extensions: `curl`, `json`, `mbstring`, `sqlite3`

## License

MIT

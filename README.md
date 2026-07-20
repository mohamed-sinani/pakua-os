# PakuaOS

> **Find. Verify. Download.**

PakuaOS is a PHP CLI tool for finding and downloading verified operating systems and software. It pulls from official sources only — Ubuntu mirrors, Microsoft servers, Mozilla, Google, GitHub releases, and more.

```
  ══════════════════════════════════════════════════════════════════════════
  ║                            PakuaOS v1.0                               ║
  ║                  Software & Operating System Hub                      ║
  ║                    Find • Verify • Download Safely                    ║
  ══════════════════════════════════════════════════════════════════════════

    ███████╗  ██████╗ ███████╗
    ██╔════╝ ██╔═══██╗██╔════╝
    ███████╗ ██║   ██║███████╗
    ╚════██║ ██║   ██║╚════██║
    ███████║ ╚██████╔╝███████║
    ╚══════╝  ╚═════╝ ╚══════╝

        Software & Operating System Hub
             Find • Verify • Download

    Developer  : Mohamed Sinani (Dev_Meddy)
    Website    : https://dev.mohamedsinani.com
    GitHub     : https://github.com/mohamed-sinani
```

---

## Quick Start

### Step 1 — Install PHP 8.2+

PakuaOS requires PHP 8.2 or higher with these extensions: `curl`, `json`, `mbstring`.

```bash
# Check your PHP version
php -v

# Ubuntu/Debian — install if needed
sudo apt install php8.2-cli php8.2-curl php8.2-mbstring

# macOS — install via Homebrew
brew install php
```

### Step 2 — Install via Composer

```bash
composer global require pakuaos/pakua
```

Make sure Composer's global bin directory is in your `PATH`:

```bash
# Add to ~/.bashrc or ~/.zshrc
export PATH="$HOME/.composer/vendor/bin:$PATH"

# Then reload
source ~/.bashrc
```

### Step 3 — Run It

```bash
pakuaos
```

That's it. You'll see the interactive menu.

---

## How to Use

### Interactive Menu

Just type `pakuaos` to launch the interactive menu:

```
  What do you want?

  > Operating Systems     Find ISOs — pick distro, version, arch
    Software Setup        Search ANY app — unlimited from web
    Search Everything     Search all sources at once
    Download by URL       Direct download from any URL
    My Downloads          View download history
    Settings              Configure PakuaOS
```

Use **↑ ↓** arrow keys to navigate, **Enter** to select.

---

### Browse Operating Systems

Select **Operating Systems** from the menu, then pick a category:

```
  Pick a category?

  > Linux       Ubuntu, Debian, Fedora, Arch, Kali, Mint, openSUSE
    Windows     Windows 11, 10, Server 2022
    macOS       Sequoia, Sonoma, Ventura
```

Then pick a specific version:

```
  Pick a Linux distro?

  > Ubuntu 24.04.2 LTS          Desktop / Server / Minimal   4.7 GB
    Ubuntu 22.04.5 LTS          Desktop / Server              4.1 GB
    Debian 12 Bookworm          Netinst / DVD / Live          3.7 GB
    Fedora 42 Workstation       Workstation / Server / Spin   2.1 GB
    Arch Linux                  Base ISO                      800 MB
    Kali Linux 2025.1           Installer / NetInstaller      4.2 GB
    Linux Mint 22.1 Xia         Cinnamon / MATE / Xfce        2.4 GB
    openSUSE Leap 15.6          DVD / Network                 4.5 GB
```

Pick your architecture and confirm — download starts with a live progress bar:

```
  Download

  URL:        https://releases.ubuntu.com/24.04.2/ubuntu-24.04.2-desktop-amd64.iso
  Saving to:  ~/Downloads/PakuaOS/Operating Systems

  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  45.2%  12.3 MB/s  ETA: 3m 12s
  Downloaded:  2.1 GB  /  4.7 GB

  ✓ Download complete!
  Saved to: ~/Downloads/PakuaOS/Operating Systems/ubuntu-24.04.2-desktop-amd64.iso
```

---

### Search for Software

Select **Software Setup** from the menu, or use the search command:

```
  Search for software?

  > vscode
```

Results from all sources:

```
  Results for "vscode"

  #   Package                    Status      Source
  ─── ────────────────────────── ─────────── ──────────────────
  1   Visual Studio Code         ✓ Ready     Microsoft Corp
  2   VS Code Insiders           ✓ Ready     Microsoft Corp
  3   VSCodium                   ✓ Ready     Community
```

---

### Direct URL Download

Have a direct download link? Use **Download by URL**:

```
  Enter download URL:

  > https://example.com/file.zip

  Download

  URL:        https://example.com/file.zip
  Saving to:  ~/Downloads/PakuaOS

  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  100%  8.7 MB/s  ETA: 0s
  ✓ Download complete!
```

---

### View Download History

Select **My Downloads** from the menu, or run:

```bash
pakuaos history
```

```
  Download History

  #   Package                            Status        Size
  ─── ────────────────────────────────── ──────────── ──────
  1   ubuntu-24.04.2-desktop-amd64.iso   ✓ Ready       4.7 GB
  2   Fedora-Workstation-x86_64-42.iso   ✓ Ready       2.1 GB
  3   firefox-latest.en-US.win64.exe     ↻ Resumable   58 MB
```

---

## CLI Commands

| Command | Description |
|---------|-------------|
| `pakuaos` | Launch interactive menu (default) |
| `pakuaos search <query>` | Search for software |
| `pakuaos find <query>` | Alias for search |
| `pakuaos download <url>` | Download from URL |
| `pakuaos dl <url>` | Alias for download |
| `pakuaos history` | View download history |
| `pakuaos ls` | Alias for history |

---

## Download Structure

All files are saved under `~/Downloads/PakuaOS/`:

```
~/Downloads/PakuaOS/
├── Operating Systems/
│   ├── ubuntu-24.04.2-desktop-amd64.iso
│   ├── Fedora-Workstation-x86_64-42.iso
│   └── ...
└── Programs/
    ├── firefox-latest.en-US.win64.exe
    ├── VSCodeSetup-x64.exe
    └── ...
```

---

## Features

- **Verified Sources Only** — Every download URL points to official mirrors and publisher sites
- **Cross-Session Resume** — Interrupted downloads resume from where they left off
- **Fallback URLs** — Windows ISOs include archive.org mirrors for reliability
- **Live Progress Bar** — Real-time speed, ETA, and percentage during downloads
- **SHA256 Verification** — Hash checking for supported downloads
- **Download History** — All downloads tracked locally in `~/.pakuaos/`
- **Web Interface** — Also available as a web app at `web/`

---

## Supported Software

| Category | Apps |
|----------|------|
| **Browsers** | Firefox, Chrome, Edge, Brave |
| **Development** | VS Code, Android Studio, Docker, Git, Node.js |
| **Security** | Wireshark, Nmap |
| **Productivity** | LibreOffice |
| **Utilities** | VLC, 7-Zip |

---

## Web Interface

PakuaOS also includes a web frontend. Point your local server to the `web/` directory:

```bash
# Using PHP built-in server
cd web/
php -S localhost:8000

# Or use XAMPP/WAMP — copy web/ to your htdocs
```

Then open `http://localhost:8000` in your browser.

---

## Requirements

- PHP 8.2+
- Extensions: `curl`, `json`, `mbstring`
- Composer (for installation)
- Internet connection

---

## Project Structure

```
bin/pakuaos                          # CLI entry point
src/
├── Application/PakuaOS.php          # App boot & command registration
├── Commands/
│   ├── MenuCommand.php              # Interactive main menu
│   ├── SearchCommand.php            # search / find
│   ├── DownloadCommand.php          # download / dl
│   └── HistoryCommand.php           # history / ls
├── UI/
│   ├── Theme.php                    # Colors & banner
│   ├── Table.php                    # Table renderer
│   ├── ProgressBar.php              # Download progress
│   ├── Spinner.php                  # Loading animation
│   └── Menu.php                     # Interactive menus
├── Search/
│   ├── SearchEngine.php             # Provider orchestrator
│   └── Providers/
│       ├── LinuxProvider.php        # Linux distros
│       ├── WindowsProvider.php      # Windows ISOs
│       ├── MacOSProvider.php        # macOS versions
│       └── SoftwareProvider.php     # Desktop software
├── Downloader/
│   └── Downloader.php               # cURL download engine
├── Verification/
│   └── HashVerifier.php             # SHA256 verification
└── Database/
    └── Database.php                 # JSON persistence

web/                                 # Web frontend
├── index.html                       # Main page
├── style.css                        # Theme CSS
├── app.js                           # SPA logic
└── api/
    ├── download.php                 # SSE download engine
    └── history.php                  # History API
```

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing`)
5. Open a Pull Request

---

## License

MIT License. See [LICENSE](LICENSE) for details.

---

**Powered by [dev_meddy](https://instagram.com/dev_meddy)**

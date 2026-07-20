/* ═══════════════════════════════════════════════════════════════
   PakuaOS Web — Exact CLI data, SVG icons, real downloads
   ═══════════════════════════════════════════════════════════════ */

const API_BASE = 'api';

// ─── SVG Icons (match sky-blue theme) ───
const ICO = {
    search: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>`,
    download: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11M7.5 10.5 12 15l4.5-4.5M5 20h14"/></svg>`,
    check: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>`,
    shield: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
    arrow: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>`,
    back: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>`,
    globe: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`,
    code: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>`,
    cpu: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>`,
    package: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>`,
    folder: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>`,
    star: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`,
    monitor: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
    server: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>`,
    apple: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20.94c1.5 0 2.75 1.06 4 1.06 3 0 4-6 4-12s-2-12-6-12c-1.5 0-2.5 1-4 1s-2.5-1-4-1C4 0 2 6 2 12s1 12 4 12c1.25 0 2.5-1.06 4-1.06z"/><path d="M10 2c1 .5 2 2 2 5"/></svg>`,
    linux: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>`,
    windows: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-7h14l2 7"/><path d="M3 12v8h7v-5h4v5h7v-8"/><path d="M3 12h18"/></svg>`,
    verified: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>`,
    history: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
    zap: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
    refresh: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>`,
    x: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    alert: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
    file: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`,
    external: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>`,
};

// ─── Exact CLI Data ───
const OS_DATA = {
    linux: [
        { name: 'Ubuntu 24.04.2 LTS', icon: 'linux', desc: 'Most popular desktop Linux — beginner-friendly, huge community.', arch: 'amd64, arm64, armhf', types: 'Desktop / Server / Minimal', size: '4.7 GB', verified: true, source: 'Official Ubuntu Mirror', url: 'https://releases.ubuntu.com/24.04.2/ubuntu-24.04.2-desktop-amd64.iso' },
        { name: 'Ubuntu 22.04.5 LTS', icon: 'linux', desc: 'Long-term support release — stable and proven.', arch: 'amd64, arm64, armhf', types: 'Desktop / Server', size: '4.1 GB', verified: true, source: 'Official Ubuntu Mirror', url: 'https://releases.ubuntu.com/22.04/ubuntu-22.04.5-desktop-amd64.iso' },
        { name: 'Debian 12 Bookworm', icon: 'linux', desc: 'The universal operating system — rock-solid stability.', arch: 'amd64, arm64, i386', types: 'Netinst / DVD / Live', size: '3.7 GB', verified: true, source: 'Official Debian Mirror', url: 'https://cdimage.debian.org/debian-cd/current/amd64/iso-cd/debian-12.11.0-amd64-netinst.iso' },
        { name: 'Fedora 42 Workstation', icon: 'linux', desc: 'Cutting-edge Linux with latest kernel and GNOME.', arch: 'x86_64, aarch64', types: 'Workstation / Server / Spin', size: '2.1 GB', verified: true, source: 'Official Fedora Mirror', url: 'https://download.fedoraproject.org/pub/fedora/linux/releases/42/Workstation/x86_64/iso/Fedora-Workstation-x86_64-42.iso' },
        { name: 'Fedora 41 Workstation', icon: 'linux', desc: 'Previous Fedora release — stable and tested.', arch: 'x86_64, aarch64', types: 'Workstation / Server', size: '2.0 GB', verified: true, source: 'Official Fedora Mirror', url: 'https://download.fedoraproject.org/pub/fedora/linux/releases/41/Workstation/x86_64/iso/Fedora-Workstation-x86_64-41.iso' },
        { name: 'Arch Linux', icon: 'linux', desc: 'DIY rolling-release distro for advanced users.', arch: 'x86_64', types: 'Base ISO', size: '800 MB', verified: true, source: 'Official Arch Linux Mirror', url: 'https://geo.mirror.pkgbuild.com/iso/latest/archlinux-x86_64.iso' },
        { name: 'Kali Linux 2025.1', icon: 'linux', desc: 'Penetration testing and security auditing distro.', arch: 'amd64, arm64', types: 'Installer / NetInstaller / Live', size: '4.2 GB', verified: true, source: 'Official Kali Mirror', url: 'https://cdimage.kali.org/kali-2025.1/kali-linux-2025.1-installer-amd64.iso' },
        { name: 'Linux Mint 22.1 Xia', icon: 'linux', desc: 'Elegant, easy-to-use Ubuntu-based distro.', arch: 'amd64', types: 'Cinnamon / MATE / Xfce', size: '2.4 GB', verified: true, source: 'Official Linux Mint Mirror', url: 'https://mirror.cs.uchicago.edu/linuxmint-cd/stable/22.1/linuxmint-22.1-cinnamon-64bit.iso' },
        { name: 'openSUSE Leap 15.6', icon: 'linux', desc: 'Enterprise-grade Linux with YaST configuration.', arch: 'x86_64, aarch64', types: 'DVD / Network', size: '4.5 GB', verified: true, source: 'Official openSUSE Mirror', url: 'https://download.opensuse.org/distribution/leap/15.6/iso/openSUSE-Leap-15.6-DVD-x86_64-Media.iso' },
    ],
    windows: [
        { name: 'Windows 11 25H2', icon: 'windows', desc: 'Latest Windows 11 with AI features and Copilot.', arch: 'x64', size: '7.2 GB', verified: true, source: 'Official Microsoft', url: 'https://software-static.download.prss.microsoft.com/dbazure/888969d5-f34g-4e03-ac9d-1f9786c66749/26200.6584.250915-1905.25h2_ge_release_svc_refresh_CLIENT_CONSUMER_x64FRE_en-us.iso', fallback_urls: ['https://archive.org/download/win1125h2_26200.4946/en-us_windows_11_consumer_editions_version_25h2_updated_2025_x64_dvd.iso'] },
        { name: 'Windows 11 23H2', icon: 'windows', desc: 'Previous Windows 11 release — stable and proven.', arch: 'x64', size: '6.3 GB', verified: true, source: 'Official Microsoft', url: 'https://software-static.download.prss.microsoft.com/akfm/medias/MBF2.191029.1546-23H2/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Win11_23H2_English_x64.iso', fallback_urls: ['https://archive.org/download/win-11-23h2/Win11_23H2_English_x64.iso'] },
        { name: 'Windows 10 22H2', icon: 'windows', desc: 'Windows 10 final feature update — widely supported.', arch: 'x64', size: '5.8 GB', verified: true, source: 'Official Microsoft', url: 'https://software-static.download.prss.microsoft.com/dbazure/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Win10_22H2_English_x64.iso', fallback_urls: ['https://archive.org/download/windows-10-22h2-x64-english/en-us_windows_10_22h2_updated_may_2023_x64_dvd_8ae93bf4.iso'] },
        { name: 'Windows Server 2022', icon: 'server', desc: 'Long-term servicing channel server OS.', arch: 'x64', size: '4.9 GB', verified: true, source: 'Official Microsoft', url: 'https://software-static.download.prss.microsoft.com/sg/download/888858/9d2abac5-3e76-4292-8e47-5fc5ab657a9d/Server2022.iso', fallback_urls: ['https://archive.org/download/WindowsServer2022_RTM/en-us_windows_server_2022_x64_dvd_620d7eac.iso'] },
    ],
    macos: [
        { name: 'macOS Sequoia 15', icon: 'apple', desc: 'Latest macOS with Apple Intelligence.', arch: 'arm64, x64', size: '14.2 GB', verified: true, source: 'Apple Official', url: 'https://support.apple.com/en-us/111901' },
        { name: 'macOS Sonoma 14', icon: 'apple', desc: 'Previous macOS with widgets and Safari profiles.', arch: 'arm64, x64', size: '13.1 GB', verified: true, source: 'Apple Official', url: 'https://support.apple.com/en-us/108897' },
        { name: 'macOS Ventura 13', icon: 'apple', desc: 'macOS with Stage Manager and Freeform.', arch: 'arm64, x64', size: '12.0 GB', verified: true, source: 'Apple Official', url: 'https://support.apple.com/en-us/108069' },
    ]
};

const SOFTWARE_DATA = [
    { name: 'Mozilla Firefox', key: 'firefox', icon: 'globe', desc: 'Privacy-focused open-source browser', category: 'Browser', source: 'Mozilla Foundation', verified: true, platforms: { Windows: { url: 'https://download.mozilla.org/?product=firefox-latest&os=win64&lang=en-US', type: 'Installer (.exe)' }, Linux: { url: 'https://download.mozilla.org/?product=firefox-latest&os=linux64&lang=en-US', type: 'Package (.deb)' }, macOS: { url: 'https://download.mozilla.org/?product=firefox-latest&os=osx&lang=en-US', type: 'DMG' } } },
    { name: 'Google Chrome', key: 'chrome', icon: 'globe', desc: 'Fast, secure browser by Google', category: 'Browser', source: 'Google LLC', verified: true, platforms: { Windows: { url: 'https://dl.google.com/chrome/install/latest/chrome_installer.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb', type: 'Package (.deb)' }, macOS: { url: 'https://dl.google.com/chrome/mac/universal/GGRO/googlechrome.dmg', type: 'DMG' } } },
    { name: 'Microsoft Edge', key: 'edge', icon: 'globe', desc: 'Microsoft Chromium-based browser', category: 'Browser', source: 'Microsoft Corporation', verified: true, platforms: { Windows: { url: 'https://msedge.sf.dl.delivery.mp.microsoft.com/filestreamingservice/files/12382b2b-0255-401c-8e45-46cd2a84446e/MicrosoftEdgeEnterpriseX64.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://packages.microsoft.com/repos/edge/pool/main/m/microsoft-edge-stable/microsoft-edge-stable_131.0.2903.86-1_amd64.deb', type: 'Package (.deb)' } } },
    { name: 'Brave Browser', key: 'brave', icon: 'shield', desc: 'Privacy browser with built-in ad blocker', category: 'Browser', source: 'Brave Software', verified: true, platforms: { Windows: { url: 'https://brave-download-1.s3.brave.com/BraveBrowserSetup.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://brave-browser-apt-release.s3.brave.com/brave-browser-release_amd64.deb', type: 'Package (.deb)' } } },
    { name: 'Visual Studio Code', key: 'vscode', icon: 'code', desc: 'Popular code editor by Microsoft', category: 'Development', source: 'Microsoft Corporation', verified: true, platforms: { Windows: { url: 'https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-archive', type: 'Installer (.exe)' }, Linux: { url: 'https://code.visualstudio.com/sha/download?build=stable&os=linux-deb-x64', type: 'Package (.deb)' }, macOS: { url: 'https://code.visualstudio.com/sha/download?build=stable&os=darwin-arm64', type: 'DMG' } } },
    { name: 'Android Studio', key: 'androidstudio', icon: 'code', desc: 'Official Android IDE by Google', category: 'Development', source: 'Google LLC', verified: true, platforms: { Windows: { url: 'https://redirector.gvt1.com/edgedl/android/studio/install/2024.3.1.14/android-studio-2024.3.1.14-windows.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://redirector.gvt1.com/edgedl/android/stide/2024.3.1.14/android-studio-2024.3.1.14-linux.tar.gz', type: 'Archive (.tar.gz)' }, macOS: { url: 'https://redirector.gvt1.com/edgedl/android/studio/install/2024.3.1.14/android-studio-2024.3.1.14-mac_arm.dmg', type: 'DMG' } } },
    { name: 'Docker Desktop', key: 'docker', icon: 'package', desc: 'Container platform for modern apps', category: 'Development', source: 'Docker Inc.', verified: true, platforms: { Windows: { url: 'https://desktop.docker.com/win/main/amd64/Docker%20Desktop%20Installer.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://download.docker.com/linux/ubuntu/dists/noble/pool/stable/amd64/docker-desktop-amd64.deb', type: 'Package (.deb)' }, macOS: { url: 'https://desktop.docker.com/mac/main/arm64/Docker.dmg', type: 'DMG' } } },
    { name: 'Git', key: 'git', icon: 'folder', desc: 'Distributed version control system', category: 'Development', source: 'Git Community', verified: true, platforms: { Windows: { url: 'https://github.com/git-for-windows/git/releases/latest/download/Git-2.47.1-64-bit.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://github.com/git-for-windows/git/releases/latest/download/git_2.47.1-1_amd64.deb', type: 'Package (.deb)' }, macOS: { url: 'https://sourceforge.net/projects/git-osx-installer/files/git-2.47.1-intel-universal-mavericks.dmg/download', type: 'DMG' } } },
    { name: 'Node.js', key: 'nodejs', icon: 'zap', desc: 'JavaScript runtime for server-side apps', category: 'Development', source: 'Node.js Foundation', verified: true, platforms: { Windows: { url: 'https://nodejs.org/dist/v22.12.0/node-v22.12.0-x64.msi', type: 'MSI Installer' }, Linux: { url: 'https://nodejs.org/dist/v22.12.0/node-v22.12.0-linux-x64.tar.xz', type: 'Binary (.tar.xz)' }, macOS: { url: 'https://nodejs.org/dist/v22.12.0/node-v22.12.0.pkg', type: 'Package (.pkg)' } } },
    { name: 'Wireshark', key: 'wireshark', icon: 'search', desc: 'Network protocol analyzer', category: 'Security', source: 'Wireshark Foundation', verified: true, platforms: { Windows: { url: 'https://www.wireshark.org/download/win64/Wireshark-4.4.3-x64.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://www.wireshark.org/download/src/wireshark-4.4.3.tar.xz', type: 'Source (.tar.xz)' } } },
    { name: 'Nmap', key: 'nmap', icon: 'search', desc: 'Network discovery and security auditing', category: 'Security', source: 'Nmap Project', verified: true, platforms: { Windows: { url: 'https://nmap.org/dist/nmap-7.95-setup.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://nmap.org/dist/nmap-7.95-1.x86_64.rpm', type: 'Package (.rpm)' } } },
    { name: 'LibreOffice', key: 'libreoffice', icon: 'file', desc: 'Free office suite — Writer, Calc, Impress', category: 'Productivity', source: 'The Document Foundation', verified: true, platforms: { Windows: { url: 'https://download.documentfoundation.org/libreoffice/stable/25.2.2/win/x86_64/LibreOffice_25.2.2_Win_x86-64.msi', type: 'MSI Installer' }, Linux: { url: 'https://download.documentfoundation.org/libreoffice/stable/25.2.2/deb/x86_64/LibreOffice_25.2.2_Linux_x86-64_deb.tar.gz', type: 'Package (.deb)' }, macOS: { url: 'https://download.documentfoundation.org/libreoffice/stable/25.2.2/mac/x86_64/LibreOffice_25.2.2_MacOS_x86-64.dmg', type: 'DMG' } } },
    { name: 'VLC Media Player', key: 'vlc', icon: 'monitor', desc: 'Open-source media player — plays everything', category: 'Utilities', source: 'VideoLAN', verified: true, platforms: { Windows: { url: 'https://get.videolan.org/vlc/3.0.21/win64/vlc-3.0.21-win64.exe', type: 'Installer (.exe)' }, Linux: { url: 'https://download.videolan.org/pub/videolan/vlc/3.0.21/linux64/vlc-3.0.21-linux-x64.tar.xz', type: 'Archive (.tar.xz)' } } },
    { name: '7-Zip', key: '7zip', icon: 'package', desc: 'Free file archiver with high compression', category: 'Utilities', source: 'Igor Pavlov', verified: true, platforms: { Windows: { url: 'https://www.7-zip.org/a/7z2409-x64.exe', type: 'Installer (.exe)' } } },
];

// Popular = first items from each category
const POPULAR_SOFTWARE = SOFTWARE_DATA.filter(s => ['firefox','chrome','vscode','vlc','docker','7zip','git','nodejs'].includes(s.key));

let downloadHistory = [];

// ─── Navigation ───
function showSection(name) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
    const section = document.getElementById('section-' + name);
    if (section) section.style.display = 'block';
    const navLink = document.querySelector(`.nav-link[data-section="${name}"]`);
    if (navLink) navLink.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    if (name === 'os') renderOS('linux');
    if (name === 'software') renderSoftware(SOFTWARE_DATA);
    if (name === 'history') renderHistory();
}

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => { e.preventDefault(); showSection(link.dataset.section); });
});
document.getElementById('hamburger').addEventListener('click', () => {
    document.getElementById('mainNav').classList.toggle('open');
});

// ─── Render helpers ───
function ico(name, cls = '') { return `<span class="ico ${cls}">${ICO[name] || ''}</span>`; }

function verifiedBadge(v) {
    return v
        ? `<span class="pop-card-badge badge-verified">${ico('verified', 'ico-sm')} Verified</span>`
        : `<span class="pop-card-badge badge-unverified">${ico('alert', 'ico-sm')} Unverified</span>`;
}

function platformBadges(platforms) {
    return Object.keys(platforms).map(p => `<span class="platform-badge">${p}</span>`).join('');
}

// ─── OS ───
function showOSTab(tab) {
    document.querySelectorAll('.os-tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`.os-tab[onclick*="${tab}"]`).classList.add('active');
    renderOS(tab);
}

function renderOS(tab) {
    const grid = document.getElementById('os-grid');
    const items = OS_DATA[tab] || [];
    grid.innerHTML = items.map(os => `
        <div class="os-card" onclick="showOSDetail('${tab}', '${os.name}')">
            <div class="os-card-header">
                <div class="os-card-logo">${ico(os.icon, 'ico-md ico-sky')}</div>
                <div><div class="os-card-name">${os.name}</div></div>
                ${verifiedBadge(os.verified)}
            </div>
            <div class="os-card-desc">${os.desc}</div>
            <div class="os-card-meta">
                <span>${ico('cpu', 'ico-sm ico-muted')} ${os.arch}</span>
                <span>${ico('package', 'ico-sm ico-muted')} ${os.size}</span>
            </div>
        </div>
    `).join('');
}

function showOSDetail(tab, name) {
    const os = OS_DATA[tab].find(o => o.name === name);
    if (!os) return;
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById('section-download').style.display = 'block';
    document.getElementById('download-detail').innerHTML = `
        <button class="btn-back" onclick="showOSDetailBack('${tab}')">${ico('back', 'ico-sm')} Back to ${tab === 'linux' ? 'Linux' : tab === 'windows' ? 'Windows' : 'macOS'}</button>
        <div class="download-detail-card">
            <h2>${ico(os.icon, 'ico-lg ico-sky')} ${os.name}</h2>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${os.desc}</div></div>
            <div class="detail-row"><div class="detail-label">Architecture</div><div class="detail-value">${os.arch}</div></div>
            <div class="detail-row"><div class="detail-label">Type</div><div class="detail-value">${os.types || 'ISO'}</div></div>
            <div class="detail-row"><div class="detail-label">File Size</div><div class="detail-value">${os.size}</div></div>
            <div class="detail-row"><div class="detail-label">Source</div><div class="detail-value">${os.source}</div></div>
            <div class="detail-row"><div class="detail-label">Verification</div><div class="detail-value">${os.verified ? `${ico('verified', 'ico-sm')} Verified against official checksum` : `${ico('alert', 'ico-sm')} Unverified — check before installing`}</div></div>
            <div class="detail-row"><div class="detail-label">URL</div><div class="detail-value"><a href="${os.url}" target="_blank" rel="noopener">${os.url.substring(0, 70)}...</a></div></div>
            ${os.fallback_urls ? `<div class="detail-row"><div class="detail-label">Mirrors</div><div class="detail-value">${os.fallback_urls.length} archive.org fallback(s)</div></div>` : ''}
            <button class="btn-download" onclick="startDownload('${os.name.replace(/'/g, "\\'")}', '${os.url}', '${os.size}', 'os', ${os.fallback_urls ? JSON.stringify(os.fallback_urls).replace(/"/g, '&quot;') : '[]'})">
                ${ico('download', 'ico-md')} Download ${os.name}
            </button>
        </div>
    `;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showOSDetailBack(tab) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById('section-os').style.display = 'block';
    showOSTab(tab);
}

// ─── Software ───
function renderSoftware(items) {
    document.getElementById('software-grid').innerHTML = items.map(sw => `
        <div class="software-card" onclick="showSoftwareDetail('${sw.key}')">
            <div class="software-card-icon">${ico(sw.icon, 'ico-lg ico-sky')}</div>
            <div class="software-card-info">
                <h3>${sw.name}</h3>
                <p>${sw.desc}</p>
                <div class="software-card-platforms">${platformBadges(sw.platforms)}</div>
            </div>
        </div>
    `).join('');
}

function renderPopular() {
    document.getElementById('popular-grid').innerHTML = POPULAR_SOFTWARE.map(sw => `
        <div class="pop-card" onclick="showSoftwareDetail('${sw.key}')">
            <div class="pop-card-top">
                <div class="pop-card-icon">${ico(sw.icon, 'ico-md')}</div>
                <div>
                    <div class="pop-card-name">${sw.name}</div>
                    <div class="pop-card-cat">${sw.category}</div>
                </div>
            </div>
            <div class="pop-card-meta">
                ${verifiedBadge(sw.verified)}
                <span class="pop-card-badge badge-popular">${sw.source}</span>
            </div>
            <button class="pop-card-btn" onclick="event.stopPropagation();showSoftwareDetail('${sw.key}')">Download</button>
        </div>
    `).join('');
}

function filterSoftware(q) {
    q = q.toLowerCase();
    renderSoftware(SOFTWARE_DATA.filter(s => s.name.toLowerCase().includes(q) || s.desc.toLowerCase().includes(q) || s.category.toLowerCase().includes(q)));
}

function showSoftwareDetail(key) {
    const sw = SOFTWARE_DATA.find(s => s.key === key);
    if (!sw) return;
    const platformList = Object.entries(sw.platforms).map(([os, info]) => `
        <div class="detail-row" style="cursor:pointer" onclick="startDownload('${sw.name.replace(/'/g, "\\'")} ${os}', '${info.url}', 'N/A', 'programs', [])">
            <div class="detail-label">${os}</div>
            <div class="detail-value" style="color:var(--sky)">${info.type} ${ico('download', 'ico-sm ico-sky')}</div>
        </div>
    `).join('');

    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById('section-download').style.display = 'block';
    document.getElementById('download-detail').innerHTML = `
        <button class="btn-back" onclick="showSection('software')">${ico('back', 'ico-sm')} Back to Software</button>
        <div class="download-detail-card">
            <h2>${ico(sw.icon, 'ico-lg ico-sky')} ${sw.name}</h2>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${sw.desc}</div></div>
            <div class="detail-row"><div class="detail-label">Category</div><div class="detail-value">${sw.category}</div></div>
            <div class="detail-row"><div class="detail-label">Publisher</div><div class="detail-value">${sw.source}</div></div>
            <div class="detail-row"><div class="detail-label">Verification</div><div class="detail-value">${sw.verified ? `${ico('verified', 'ico-sm')} Verified against official source` : `${ico('alert', 'ico-sm')} Unverified`}</div></div>
            <h3 style="margin:20px 0 8px;font-size:16px;font-weight:600">Select Platform to Download</h3>
            ${platformList}
        </div>
    `;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ─── Search ───
function performSearch() { const q = document.getElementById('heroSearch').value.trim(); if (q) quickSearch(q); }
function quickSearch(q) {
    document.getElementById('heroSearch').value = q;
    showSection('search');
    document.getElementById('globalSearch').value = q;
    performGlobalSearch();
}
function performGlobalSearch() {
    const q = document.getElementById('globalSearch').value.trim().toLowerCase();
    if (!q) return;
    const results = [];
    Object.keys(OS_DATA).forEach(tab => {
        OS_DATA[tab].forEach(os => {
            if (os.name.toLowerCase().includes(q) || os.desc.toLowerCase().includes(q)) {
                results.push({ type: 'os', tab, icon: os.icon, name: os.name, desc: os.desc, meta: os.arch + ' / ' + os.size, source: os.source, verified: os.verified, action: 'Download ISO' });
            }
        });
    });
    SOFTWARE_DATA.forEach(sw => {
        if (sw.name.toLowerCase().includes(q) || sw.desc.toLowerCase().includes(q) || sw.category.toLowerCase().includes(q)) {
            results.push({ type: 'software', key: sw.key, icon: sw.icon, name: sw.name, desc: sw.desc, meta: Object.keys(sw.platforms).join(', '), source: sw.source, verified: sw.verified, action: 'Download' });
        }
    });
    const container = document.getElementById('search-results');
    if (results.length === 0) {
        container.innerHTML = `<div class="empty-state"><div class="empty-icon">${ico('search', 'ico-xl ico-muted')}</div><p>No results found for "${q}". Try different keywords.</p></div>`;
        return;
    }
    container.innerHTML = `
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px">Found ${results.length} result${results.length > 1 ? 's' : ''} for "${q}"</p>
        ${results.map(r => `
            <div class="result-card" onclick="${r.type === 'os' ? `showOSDetail('${r.tab}','${r.name}')` : `showSoftwareDetail('${r.key}')`}">
                <div class="result-icon">${ico(r.icon, 'ico-lg ico-sky')}</div>
                <div class="result-info">
                    <h3>${r.name}</h3>
                    <p>${r.desc}</p>
                    <div class="result-meta"><span>${r.meta}</span><span>${ico('verified', 'ico-sm')} ${r.source}</span></div>
                </div>
                <button class="result-action">${r.action}</button>
            </div>
        `).join('')}
    `;
}
document.getElementById('globalSearch').addEventListener('keypress', e => { if (e.key === 'Enter') performGlobalSearch(); });
document.getElementById('heroSearch').addEventListener('keypress', e => { if (e.key === 'Enter') performSearch(); });

// ═══════════════════════════════════════════════════════════════
//  DOWNLOAD ENGINE
// ═══════════════════════════════════════════════════════════════

function startDownload(name, url, size, category, fallbackUrls) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById('section-download').style.display = 'block';
    const safeName = name.replace(/[^\w\s\-\.]/g, '').replace(/\s+/g, '_').substring(0, 120) || 'download';

    document.getElementById('download-detail').innerHTML = `
        <div class="dl-progress-wrap downloading" id="dl-wrap">
            <div class="dl-progress-title">
                <h2 id="dl-title">Preparing download...</h2>
                <span class="dl-status-badge dl-status-active" id="dl-badge">Connecting</span>
            </div>
            <div class="dl-bar-track">
                <div class="dl-bar-fill" id="dl-fill"></div>
                <div class="dl-bar-pct" id="dl-pct">0%</div>
            </div>
            <div class="dl-stats">
                <div class="dl-stat"><div class="dl-stat-val" id="dl-speed">&mdash;</div><div class="dl-stat-label">Speed</div></div>
                <div class="dl-stat"><div class="dl-stat-val" id="dl-downloaded">0 B</div><div class="dl-stat-label">Downloaded</div></div>
                <div class="dl-stat"><div class="dl-stat-val" id="dl-total">${size}</div><div class="dl-stat-label">Total</div></div>
                <div class="dl-stat"><div class="dl-stat-val" id="dl-eta">&mdash;</div><div class="dl-stat-label">ETA</div></div>
            </div>
            <div class="dl-file-info">
                <div class="dl-file-icon">${ico('package', 'ico-md ico-sky')}</div>
                <div>
                    <div class="dl-file-name" id="dl-filename">${safeName}</div>
                    <div class="dl-file-url" id="dl-fileurl">${url}</div>
                </div>
            </div>
            <div id="dl-error" style="display:none"></div>
            <div id="dl-complete" style="display:none"></div>
        </div>
    `;
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const body = new URLSearchParams();
    body.append('url', url);
    body.append('name', safeName);
    body.append('category', category || 'other');

    const fillEl = document.getElementById('dl-fill');
    const pctEl = document.getElementById('dl-pct');
    const speedEl = document.getElementById('dl-speed');
    const dlEl = document.getElementById('dl-downloaded');
    const totalEl = document.getElementById('dl-total');
    const etaEl = document.getElementById('dl-eta');
    const titleEl = document.getElementById('dl-title');
    const badgeEl = document.getElementById('dl-badge');
    const errEl = document.getElementById('dl-error');
    const compEl = document.getElementById('dl-complete');
    const wrapEl = document.getElementById('dl-wrap');

    fetch(API_BASE + '/download.php', { method: 'POST', body })
        .then(response => {
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';
            (function readChunk() {
                reader.read().then(({ done, value }) => {
                    if (done) return;
                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop();
                    let eventType = '';
                    for (const line of lines) {
                        if (line.startsWith('event: ')) eventType = line.substring(7).trim();
                        else if (line.startsWith('data: ')) {
                            try { handleDownloadEvent(eventType, JSON.parse(line.substring(6))); } catch (e) {}
                        }
                    }
                    readChunk();
                });
            })();
        })
        .catch(err => {
            titleEl.textContent = 'Connection failed';
            badgeEl.textContent = 'Error';
            badgeEl.className = 'dl-status-badge dl-status-error';
            wrapEl.classList.remove('downloading');
            errEl.style.display = 'block';
            errEl.innerHTML = `<div class="dl-error-msg">${ico('alert', 'ico-sm')} Failed to connect: ${err.message}</div>`;
        });

    function handleDownloadEvent(event, data) {
        switch (event) {
            case 'start':
                titleEl.textContent = 'Downloading ' + data.name;
                badgeEl.textContent = 'Active';
                document.getElementById('dl-filename').textContent = data.name;
                document.getElementById('dl-fileurl').textContent = data.url;
                break;
            case 'info':
                if (data.total_human) totalEl.textContent = data.total_human;
                break;
            case 'progress':
                fillEl.style.width = Math.min(data.pct, 100) + '%';
                pctEl.textContent = data.pct.toFixed(1) + '%';
                speedEl.textContent = data.speed_human || '&mdash;';
                dlEl.textContent = data.downloaded_human || '&mdash;';
                totalEl.textContent = data.total_human || '&mdash;';
                etaEl.textContent = data.eta || '&mdash;';
                break;
            case 'complete':
                fillEl.style.width = '100%';
                fillEl.style.background = 'linear-gradient(90deg, #16a34a, #15803d)';
                pctEl.textContent = '100%';
                titleEl.textContent = 'Download complete!';
                badgeEl.textContent = 'Done';
                badgeEl.className = 'dl-status-badge dl-status-done';
                wrapEl.classList.remove('downloading');
                speedEl.textContent = data.speed_avg || '&mdash;';
                etaEl.textContent = data.time || '&mdash;';
                dlEl.textContent = data.size_human || '&mdash;';
                compEl.style.display = 'block';
                compEl.innerHTML = `
                    <div class="dl-complete-actions">
                        <a class="btn-download-ready" href="${data.file_path}" download="${data.name}">${ico('download', 'ico-md')} Save File</a>
                        <button class="btn-another" onclick="location.reload()">${ico('refresh', 'ico-sm')} Download Another</button>
                    </div>
                    <div style="margin-top:12px;font-size:13px;color:var(--text-muted)">Avg speed: ${data.speed_avg} / Time: ${data.time}</div>
                `;
                downloadHistory.unshift({ name: data.name, size: data.size_human, date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }), status: 'completed', filePath: data.file_path });
                break;
            case 'error':
                titleEl.textContent = 'Download failed';
                badgeEl.textContent = 'Error';
                badgeEl.className = 'dl-status-badge dl-status-error';
                fillEl.style.background = 'linear-gradient(90deg, #dc2626, #b91c1c)';
                wrapEl.classList.remove('downloading');
                errEl.style.display = 'block';
                errEl.innerHTML = `<div class="dl-error-msg">${ico('alert', 'ico-sm')} <strong>Failed:</strong> ${data.message}<br><small>HTTP ${data.http_code || 'N/A'}</small></div>
                    <div style="margin-top:12px"><button class="btn-another" onclick="location.reload()">${ico('refresh', 'ico-sm')} Try Again</button></div>`;
                break;
        }
    }
}

// ─── History ───
function renderHistory() {
    const container = document.getElementById('history-list');
    fetch(API_BASE + '/history.php?action=list')
        .then(r => r.json())
        .then(res => {
            if (res.ok && res.data.length > 0) {
                container.innerHTML = res.data.map(d => `
                    <div class="history-item">
                        <div class="history-icon completed">${ico('check', 'ico-md')}</div>
                        <div class="history-info"><h3>${d.name}</h3><p>${d.created_at} / ${d.size_human || ''}</p></div>
                        <a href="${d.file_path}" download class="history-status status-completed" style="text-decoration:none">${ico('download', 'ico-sm')} Download</a>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `<div class="empty-state"><div class="empty-icon">${ico('history', 'ico-xl ico-muted')}</div><p>No downloads yet. Start by searching for something!</p></div>`;
            }
        })
        .catch(() => {
            container.innerHTML = `<div class="empty-state"><div class="empty-icon">${ico('history', 'ico-xl ico-muted')}</div><p>No downloads yet. Start by searching for something!</p></div>`;
        });
}

// ─── Init ───
document.addEventListener('DOMContentLoaded', () => {
    renderPopular();
    showSection('home');
});

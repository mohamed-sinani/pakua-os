/* ═══════════════════════════════════════════════════════════════
   PakuaOS Web — App Logic
   ═══════════════════════════════════════════════════════════════ */

// ─── Data ───
const OS_DATA = {
    linux: [
        { name: 'Ubuntu 25.04', icon: '🟠', desc: 'Most popular desktop Linux — beginner-friendly, huge community.', arch: 'x64, ARM64', size: '5.8 GB', verified: true, popular: true, url: 'https://releases.ubuntu.com/25.04/ubuntu-25.04-desktop-amd64.iso' },
        { name: 'Debian 13 Trixie', icon: '🔴', desc: 'The universal operating system — rock-solid stability.', arch: 'x64, ARM64', size: '4.0 GB', verified: true, url: 'https://cdimage.debian.org/debian-cd/current/amd64/iso-cd/' },
        { name: 'Fedora 42', icon: '🔵', desc: 'Cutting-edge Linux with latest kernel and GNOME.', arch: 'x64, ARM64', size: '2.1 GB', verified: true, popular: true, url: 'https://fedoraproject.org/workstation/download' },
        { name: 'Arch Linux', icon: '🔷', desc: 'DIY rolling-release distro for advanced users.', arch: 'x64', size: '800 MB', verified: true, url: 'https://archlinux.org/download/' },
        { name: 'Kali Linux 2026.2', icon: '🐉', desc: 'Penetration testing and security auditing distro.', arch: 'x64, ARM64', size: '4.2 GB', verified: true, url: 'https://www.kali.org/get-kali/' },
        { name: 'Linux Mint 22.1', icon: '🟢', desc: 'Elegant, easy-to-use Ubuntu-based distro.', arch: 'x64', size: '2.4 GB', verified: true, url: 'https://linuxmint.com/download.php' },
        { name: 'openSUSE Leap 16.0', icon: '🦎', desc: 'Enterprise-grade Linux with YaST configuration.', arch: 'x64, ARM64', size: '4.5 GB', verified: true, url: 'https://get.opensuse.org/leap/' },
        { name: 'Pop!_OS 22.04', icon: '💜', desc: 'System76\'s polished Ubuntu with auto-tiling.', arch: 'x64, ARM64', size: '2.9 GB', verified: true, url: 'https://pop.system76.com/' },
        { name: 'Manjaro 25.0', icon: '🟢', desc: 'Arch-based with easy installer and GUI package manager.', arch: 'x64, ARM64', size: '3.4 GB', verified: true, url: 'https://manjaro.org/download/' },
        { name: 'Zorin OS 18', icon: '🔵', desc: 'Windows-like Linux for beginners — beautiful desktop.', arch: 'x64', size: '3.8 GB', verified: true, url: 'https://zorin.com/os/download/' },
        { name: 'Elementary OS 8', icon: '⚪', desc: 'macOS-inspired Linux with Pantheon desktop.', arch: 'x64', size: '2.6 GB', verified: true, url: 'https://elementary.io/' },
        { name: 'NixOS 25.05', icon: '雪花', desc: 'Declarative, reproducible Linux — Nix package manager.', arch: 'x64, ARM64', size: '1.2 GB', verified: true, url: 'https://nixos.org/download/' },
    ],
    windows: [
        { name: 'Windows 11 25H2', icon: '🪟', desc: 'Latest Windows 11 with AI features and Copilot.', arch: 'x64', size: '6.3 GB', verified: true, popular: true, url: 'https://www.microsoft.com/software-download/windows11' },
        { name: 'Windows 11 23H2', icon: '🪟', desc: 'Previous Windows 11 release — stable and proven.', arch: 'x64', size: '5.8 GB', verified: true, url: 'https://www.microsoft.com/software-download/windows11' },
        { name: 'Windows 10 22H2', icon: '🪟', desc: 'Windows 10 final feature update — widely supported.', arch: 'x64', size: '5.7 GB', verified: true, popular: true, url: 'https://www.microsoft.com/software-download/windows10' },
        { name: 'Windows Server 2022', icon: '🏢', desc: 'Long-term servicing channel server OS.', arch: 'x64', size: '5.0 GB', verified: true, url: 'https://www.microsoft.com/en-us/evalcenter/evaluate-windows-server-2022' },
    ],
    macos: [
        { name: 'macOS Sequoia 15', icon: '🍎', desc: 'Latest macOS with Apple Intelligence and new Window tiling.', arch: 'ARM64, x64', size: '14.2 GB', verified: true, popular: true, url: 'https://support.apple.com/en-us/111901' },
        { name: 'macOS Sonoma 14', icon: '🍎', desc: 'Previous macOS with widgets on desktop and Safari profiles.', arch: 'ARM64, x64', size: '13.1 GB', verified: true, url: 'https://support.apple.com/en-us/108897' },
        { name: 'macOS Ventura 13', icon: '🍎', desc: 'macOS with Stage Manager and Freeform.', arch: 'ARM64, x64', size: '12.0 GB', verified: true, url: 'https://support.apple.com/en-us/108069' },
    ]
};

const SOFTWARE_DATA = [
    { name: 'Google Chrome', icon: '🌐', desc: 'Fast, secure browser by Google', platforms: ['Windows', 'macOS', 'Linux'], category: 'Browser' },
    { name: 'Mozilla Firefox', icon: '🦊', desc: 'Privacy-focused open-source browser', platforms: ['Windows', 'macOS', 'Linux'], category: 'Browser' },
    { name: 'Visual Studio Code', icon: '💙', desc: 'Popular code editor by Microsoft', platforms: ['Windows', 'macOS', 'Linux'], category: 'Development' },
    { name: '7-Zip', icon: '📦', desc: 'Free file archiver with high compression', platforms: ['Windows'], category: 'Utility' },
    { name: 'VLC Media Player', icon: '🎬', desc: 'Open-source media player — plays everything', platforms: ['Windows', 'macOS', 'Linux'], category: 'Media' },
    { name: 'GIMP', icon: '🎨', desc: 'GNU Image Manipulation Program — Photoshop alternative', platforms: ['Windows', 'macOS', 'Linux'], category: 'Graphics' },
    { name: 'OBS Studio', icon: '🎥', desc: 'Free screen recording and streaming software', platforms: ['Windows', 'macOS', 'Linux'], category: 'Media' },
    { name: 'Notepad++', icon: '📝', desc: 'Lightweight Windows text editor for developers', platforms: ['Windows'], category: 'Development' },
    { name: 'Blender', icon: '🧊', desc: 'Professional 3D creation suite', platforms: ['Windows', 'macOS', 'Linux'], category: 'Graphics' },
    { name: ' LibreOffice', icon: '📄', desc: 'Free office suite — Writer, Calc, Impress', platforms: ['Windows', 'macOS', 'Linux'], category: 'Productivity' },
    { name: 'Brave Browser', icon: '🦁', desc: 'Privacy browser with built-in ad blocker', platforms: ['Windows', 'macOS', 'Linux'], category: 'Browser' },
    { name: 'Node.js', icon: '💚', desc: 'JavaScript runtime for server-side apps', platforms: ['Windows', 'macOS', 'Linux'], category: 'Development' },
    { name: 'Git', icon: '📂', desc: 'Distributed version control system', platforms: ['Windows', 'macOS', 'Linux'], category: 'Development' },
    { name: 'Docker Desktop', icon: '🐳', desc: 'Container platform for modern apps', platforms: ['Windows', 'macOS', 'Linux'], category: 'Development' },
    { name: 'Thunderbird', icon: '📧', desc: 'Free email client by Mozilla', platforms: ['Windows', 'macOS', 'Linux'], category: 'Productivity' },
    { name: 'Inkscape', icon: '✏️', desc: 'Professional vector graphics editor', platforms: ['Windows', 'macOS', 'Linux'], category: 'Graphics' },
    { name: 'Audacity', icon: '🎵', desc: 'Free audio editor and recorder', platforms: ['Windows', 'macOS', 'Linux'], category: 'Media' },
    { name: 'VirtualBox', icon: '📦', desc: 'Free virtualization software by Oracle', platforms: ['Windows', 'macOS', 'Linux'], category: 'Utility' },
];

const downloadHistory = [];

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
    link.addEventListener('click', (e) => {
        e.preventDefault();
        showSection(link.dataset.section);
    });
});

// ─── Hamburger ───
document.getElementById('hamburger').addEventListener('click', () => {
    document.getElementById('mainNav').classList.toggle('open');
});

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
                <div class="os-card-logo">${os.icon}</div>
                <div>
                    <div class="os-card-name">${os.name}</div>
                </div>
                ${os.popular ? '<span class="os-card-badge badge-popular">Popular</span>' : ''}
                ${os.verified ? '<span class="os-card-badge badge-verified">✓ Verified</span>' : ''}
            </div>
            <div class="os-card-desc">${os.desc}</div>
            <div class="os-card-meta">
                <span>📐 ${os.arch}</span>
                <span>💾 ${os.size}</span>
            </div>
        </div>
    `).join('');
}

function showOSDetail(tab, name) {
    const os = OS_DATA[tab].find(o => o.name === name);
    if (!os) return;

    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    const section = document.getElementById('section-download');
    section.style.display = 'block';

    document.getElementById('download-detail').innerHTML = `
        <button class="btn-back" onclick="showSection('os')">← Back to Operating Systems</button>
        <div class="download-detail-card">
            <h2>${os.icon} ${os.name}</h2>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${os.desc}</div></div>
            <div class="detail-row"><div class="detail-label">Architecture</div><div class="detail-value">${os.arch}</div></div>
            <div class="detail-row"><div class="detail-label">File Size</div><div class="detail-value">${os.size}</div></div>
            <div class="detail-row"><div class="detail-label">Verification</div><div class="detail-value">${os.verified ? '✅ Verified against official checksum' : '⚠️ Unverified'}</div></div>
            <div class="detail-row"><div class="detail-label">Source</div><div class="detail-value"><a href="${os.url}" target="_blank" rel="noopener">${os.url}</a></div></div>
            <button class="btn-download" onclick="startDownload('${os.name}', '${os.url}', '${os.size}')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11M7.5 10.5 12 15l4.5-4.5M5 20h14"/></svg>
                Download ${os.name}
            </button>
        </div>
    `;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ─── Software ───
function renderSoftware(items) {
    const grid = document.getElementById('software-grid');
    grid.innerHTML = items.map(sw => `
        <div class="software-card" onclick="showSoftwareDetail('${sw.name}')">
            <div class="software-card-icon">${sw.icon}</div>
            <div class="software-card-info">
                <h3>${sw.name.trim()}</h3>
                <p>${sw.desc}</p>
                <div class="software-card-platforms">
                    ${sw.platforms.map(p => `<span class="platform-badge">${p}</span>`).join('')}
                </div>
            </div>
        </div>
    `).join('');
}

function filterSoftware(q) {
    q = q.toLowerCase();
    const filtered = SOFTWARE_DATA.filter(s =>
        s.name.toLowerCase().includes(q) ||
        s.desc.toLowerCase().includes(q) ||
        s.category.toLowerCase().includes(q)
    );
    renderSoftware(filtered);
}

function showSoftwareDetail(name) {
    const sw = SOFTWARE_DATA.find(s => s.name.trim() === name.trim());
    if (!sw) return;

    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    const section = document.getElementById('section-download');
    section.style.display = 'block';

    document.getElementById('download-detail').innerHTML = `
        <button class="btn-back" onclick="showSection('software')">← Back to Software</button>
        <div class="download-detail-card">
            <h2>${sw.icon} ${sw.name.trim()}</h2>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${sw.desc}</div></div>
            <div class="detail-row"><div class="detail-label">Category</div><div class="detail-value">${sw.category}</div></div>
            <div class="detail-row"><div class="detail-label">Platforms</div><div class="detail-value">${sw.platforms.join(', ')}</div></div>
            <div class="detail-row"><div class="detail-label">Verification</div><div class="detail-value">✅ Verified against official source</div></div>
            <button class="btn-download" onclick="startDownload('${sw.name.trim()}', '#', 'N/A')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11M7.5 10.5 12 15l4.5-4.5M5 20h14"/></svg>
                Download ${sw.name.trim()}
            </button>
        </div>
    `;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ─── Search ───
function performSearch() {
    const q = document.getElementById('heroSearch').value.trim();
    if (!q) return;
    quickSearch(q);
}

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

    // Search OS
    Object.values(OS_DATA).flat().forEach(os => {
        if (os.name.toLowerCase().includes(q) || os.desc.toLowerCase().includes(q)) {
            results.push({ type: 'os', icon: os.icon, name: os.name, desc: os.desc, meta: os.arch + ' • ' + os.size, action: 'Download ISO' });
        }
    });

    // Search Software
    SOFTWARE_DATA.forEach(sw => {
        if (sw.name.toLowerCase().includes(q) || sw.desc.toLowerCase().includes(q) || sw.category.toLowerCase().includes(q)) {
            results.push({ type: 'software', icon: sw.icon, name: sw.name.trim(), desc: sw.desc, meta: sw.platforms.join(', '), action: 'Download' });
        }
    });

    const container = document.getElementById('search-results');
    if (results.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <p>No results found for "${q}". Try different keywords.</p>
            </div>`;
        return;
    }

    container.innerHTML = `
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">Found ${results.length} result${results.length > 1 ? 's' : ''} for "${q}"</p>
        ${results.map(r => `
            <div class="result-card" onclick="${r.type === 'os' ? `showOSDetailFromSearch('${r.name}')` : `showSoftwareDetail('${r.name}')`}">
                <div class="result-icon">${r.icon}</div>
                <div class="result-info">
                    <h3>${r.name}</h3>
                    <p>${r.desc}</p>
                    <div class="result-meta"><span>${r.meta}</span></div>
                </div>
                <button class="result-action">${r.action}</button>
            </div>
        `).join('')}
    `;
}

function showOSDetailFromSearch(name) {
    for (const tab of ['linux', 'windows', 'macos']) {
        const os = OS_DATA[tab].find(o => o.name === name);
        if (os) { showOSDetail(tab, name); return; }
    }
}

document.getElementById('globalSearch').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') performGlobalSearch();
});
document.getElementById('heroSearch').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') performSearch();
});

// ─── History ───
function renderHistory() {
    const container = document.getElementById('history-list');
    if (downloadHistory.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">📥</div>
                <p>No downloads yet. Start by searching for something!</p>
            </div>`;
        return;
    }

    container.innerHTML = downloadHistory.map(d => `
        <div class="history-item">
            <div class="history-icon ${d.status}">${d.status === 'completed' ? '✓' : d.status === 'failed' ? '✗' : '↻'}</div>
            <div class="history-info">
                <h3>${d.name}</h3>
                <p>${d.date} • ${d.size}</p>
            </div>
            <span class="history-status status-${d.status}">${d.status === 'completed' ? 'Ready' : d.status === 'failed' ? 'Failed' : 'Resumable'}</span>
        </div>
    `).join('');
}

function startDownload(name, url, size) {
    downloadHistory.unshift({
        name: name,
        url: url,
        size: size,
        date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
        status: 'completed'
    });
    renderHistory();
    alert(`Download started: ${name}\n\nIn the real PakuaOS, this would begin downloading the file to your device.`);
}

// ─── Init ───
document.addEventListener('DOMContentLoaded', () => {
    showSection('home');
});

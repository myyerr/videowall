<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display TV — {{ $display->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @font-face {
            font-family: 'NotoSansJP';
            src: local('Noto Sans JP SemiBold'), local('Noto Sans JP');
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #000;
            font-family: 'NotoSansJP', 'Inter', Arial, sans-serif;
            user-select: none;
        }

        /* ── MULTI-MONITOR WALL WRAPPER ─────────────────────── */
        /* Mendukung pemecahan videowall tanpa alat (URL Grid Mode) */
        #wallWrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #000;
        }

        body.grid-active #wallWrapper {
            width: calc(100vw * var(--wall-cols, 1));
            height: calc(100vh * var(--wall-rows, 1));
            transform: translate(calc(-100vw * var(--wall-col, 0)), calc(-100vh * var(--wall-row, 0)));
            transform-origin: top left;
        }

        /* ── BACKGROUND LAYERS ────────────────────────────── */
        #bgVideo {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            display: none;
        }

        #bgImage {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            display: none;
        }

        /* ── DISPLAY AREA ──────────────────────────────────── */
        #displayArea {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 3;
            pointer-events: none;
        }

        /* ── TEXT / LOGO ITEMS ─────────────────────────────── */
        .display-item {
            position: absolute;
            transform: translate(-50%, -50%);
            font-family: 'NotoSansJP', 'Inter', Arial, sans-serif;
            font-weight: 800;
            white-space: nowrap;
            text-align: center;
            line-height: 1.3;
            text-shadow:
                0 0 10px rgba(0,0,0,1),
                0 0 20px rgba(0,0,0,0.9),
                2px 2px 8px rgba(0,0,0,0.9);
        }

        .display-item.type-logo {
            text-shadow: none;
        }

        /* ── STANDBY ───────────────────────────────────────── */
        .standby-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .standby-clock {
            font-size: 8vw;
            font-weight: 800;
            color: rgba(255,255,255,0.18);
            letter-spacing: 8px;
            font-family: 'Inter', monospace;
            text-shadow: 0 0 30px rgba(0,0,0,0.8);
        }

        .standby-label {
            font-size: 1.3vw;
            font-weight: 600;
            color: rgba(255,255,255,0.15);
            letter-spacing: 6px;
            text-transform: uppercase;
        }

        /* ── FADE ANIMATIONS ───────────────────────────────── */
        .fade-in  { animation: fadeIn  0.6s ease-in  forwards; }
        .fade-out { animation: fadeOut 0.6s ease-out forwards; }

        @keyframes fadeIn  { from { opacity:0; } to { opacity:1; } }
        @keyframes fadeOut { from { opacity:1; } to { opacity:0; } }

        /* Auto-hide cursor saat standby / idle */
        body.hide-cursor {
            cursor: none;
        }

        /* Info Tile Grid Badge */
        #gridBadge {
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 99999;
            background: rgba(0,0,0,0.7);
            color: #38bdf8;
            border: 1px solid rgba(56,189,248,0.4);
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            display: none;
            transition: opacity 1s;
        }
    </style>
</head>
<body>

    <!-- Badge info posisi monitor grid jika aktif (hilang otomatis setelah 3 detik) -->
    <div id="gridBadge">GRID TILE</div>

    <!-- Master wall container -->
    <div id="wallWrapper">
        <!-- Background layers -->
        <video id="bgVideo" autoplay loop playsinline></video>
        <img id="bgImage" alt="">

        <!-- Display Area -->
        <div id="displayArea">
            <div class="standby-overlay" id="standbyView">
                <div class="standby-clock" id="standbyClock">00:00:00</div>
                <div class="standby-label">{{ $display->name }} &bull; STANDBY MODE</div>
            </div>
        </div>
    </div>

    <script>
    // ═══════════════════════════════════════════════════════════
    // CONFIG & MULTI-MONITOR SLICING
    // ═══════════════════════════════════════════════════════════
    const API_URL          = '{{ route("display.active", $display->device_token) }}';
    let currentContentId   = null;
    let currentUpdatedAt   = null;
    let isMuted            = false;
    let serverTimeOffset   = 0;
    let lastHardSeekTime   = 0;
    let initialSyncDone    = false;

    // ═══════════════════════════════════════════════════════════
    // MULTI-MONITOR VIDEO SYNC ENGINE (Master Clock + Soft Drift Sync)
    // ═══════════════════════════════════════════════════════════
    function getMasterVideoTime(duration) {
        if (!duration || duration <= 0) return 0;
        const nowSec = (Date.now() + serverTimeOffset) / 1000;
        return nowSec % duration;
    }

    // Koreksi drift secara halus via playbackRate (tanpa stutter / tanpa restart video)
    function syncVideoPlayback() {
        const bgVideo = document.getElementById('bgVideo');
        if (!bgVideo || !bgVideo.duration || bgVideo.paused || isNaN(bgVideo.duration)) return;

        const duration  = bgVideo.duration;
        const targetSec = getMasterVideoTime(duration);
        let drift       = bgVideo.currentTime - targetSec;

        // Tangani batas putaran video (loop wrap-around)
        if (drift > duration / 2) {
            drift -= duration;
        } else if (drift < -duration / 2) {
            drift += duration;
        }

        const absDrift = Math.abs(drift);

        // Jika selisih parah (> 2.5s) misal tab baru dibuka setelah sleep lama:
        // Lakukan hard seek satu kali dengan batas cooldown aman 6 detik
        if (absDrift > 2.5 && (Date.now() - lastHardSeekTime > 6000)) {
            bgVideo.currentTime = targetSec;
            lastHardSeekTime    = Date.now();
            bgVideo.playbackRate = 1.0;
            return;
        }

        // Penyesuaian kecepatan halus (Soft sync):
        // Jika selisih <= 0.06s (hanya 1-2 frame): sudah sinkron sempurna
        if (absDrift <= 0.06) {
            if (bgVideo.playbackRate !== 1.0) bgVideo.playbackRate = 1.0;
        }
        // Layar ini sedikit tertinggal: percepat sedikit agar mengejar tanpa terasa
        else if (drift < -0.06 && drift >= -0.35) {
            bgVideo.playbackRate = 1.05; // 5% lebih cepat
        } else if (drift < -0.35) {
            bgVideo.playbackRate = 1.15; // 15% lebih cepat untuk mengejar
        }
        // Layar ini sedikit mendahului: perlambat sedikit agar menunggu monitor lain
        else if (drift > 0.06 && drift <= 0.35) {
            bgVideo.playbackRate = 0.95; // 5% lebih lambat
        } else if (drift > 0.35) {
            bgVideo.playbackRate = 0.85; // 15% lebih lambat
        }
    }

    // Monitor dan ratakan sinkronisasi tiap 1 detik secara halus
    setInterval(syncVideoPlayback, 1000);

    // Baca parameter Grid Slicing: misal ?grid=2x2&col=0&row=1
    (function initGridMode() {
        const params = new URLSearchParams(window.location.search);
        const grid   = params.get('grid'); // misal: "2x2", "1x2", "3x3"
        const col    = parseInt(params.get('col') || '0', 10);
        const row    = parseInt(params.get('row') || '0', 10);

        if (grid && grid.includes('x')) {
            const [cols, rows] = grid.split('x').map(n => parseInt(n, 10));
            if (cols > 0 && rows > 0) {
                document.body.classList.add('grid-active');
                document.documentElement.style.setProperty('--wall-cols', cols);
                document.documentElement.style.setProperty('--wall-rows', rows);
                document.documentElement.style.setProperty('--wall-col', col);
                document.documentElement.style.setProperty('--wall-row', row);

                const badge = document.getElementById('gridBadge');
                badge.style.display = 'block';
                badge.textContent = `E-BANNER TILE [Col ${col+1}/${cols}, Row ${row+1}/${rows}]`;
                setTimeout(() => {
                    badge.style.opacity = '0';
                    setTimeout(() => badge.style.display = 'none', 1000);
                }, 4000);
            }
        }
    })();

    // ═══════════════════════════════════════════════════════════
    // SEAMLESS AUDIO & CURSOR MANAGEMENT (Tanpa Tombol Pengganggu)
    // ═══════════════════════════════════════════════════════════
    let cursorTimeout = null;

    // Sembunyikan kursor mouse otomatis saat tidak digerakkan agar layar videowall bersih
    window.addEventListener('mousemove', () => {
        document.body.classList.remove('hide-cursor');
        clearTimeout(cursorTimeout);
        cursorTimeout = setTimeout(() => {
            document.body.classList.add('hide-cursor');
        }, 2000);
    });

    // Otomatis aktifkan suara begitu operator klik layar atau tekan tombol apa saja (misal F11)
    function unlockAudio() {
        const bgVideo = document.getElementById('bgVideo');
        if (bgVideo && bgVideo.muted) {
            bgVideo.muted = false;
            bgVideo.play().catch(() => {});
        }
    }

    ['click', 'keydown', 'touchstart'].forEach(evt => {
        window.addEventListener(evt, unlockAudio, { passive: true });
    });

    // Shortcut Keyboard: M = Mute/Unmute, F = Fullscreen
    window.addEventListener('keydown', (e) => {
        const bgVideo = document.getElementById('bgVideo');
        if (e.key === 'm' || e.key === 'M') {
            if (bgVideo) bgVideo.muted = !bgVideo.muted;
        } else if (e.key === 'f' || e.key === 'F') {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        }
    });

    // ═══════════════════════════════════════════════════════════
    // STANDBY CLOCK
    // ═══════════════════════════════════════════════════════════
    function updateClock() {
        const now = new Date();
        const h   = String(now.getHours()).padStart(2, '0');
        const m   = String(now.getMinutes()).padStart(2, '0');
        const s   = String(now.getSeconds()).padStart(2, '0');
        const el  = document.getElementById('standbyClock');
        if (el) el.textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ═══════════════════════════════════════════════════════════
    // FETCH ACTIVE CONTENT — RESILIENT POLLING
    // ═══════════════════════════════════════════════════════════
    function fetchActiveContent() {
        const t0 = performance.now();
        fetch(API_URL, { cache: 'no-store' })
            .then(r => r.json())
            .then(res => {
                const rtt = performance.now() - t0;
                if (res.server_time) {
                    const serverMsAtArrival = (res.server_time * 1000) + (rtt / 2);
                    serverTimeOffset = serverMsAtArrival - Date.now();
                }

                if (res.status && res.data) {
                    const data       = res.data;
                    const newId      = data.id;
                    const newUpdated = data.updated_at || 0;

                    // Re-render jika ID berbeda ATAU konten baru saja di-edit
                    if (currentContentId !== newId || currentUpdatedAt !== newUpdated) {
                        currentContentId = newId;
                        currentUpdatedAt = newUpdated;
                        renderContent(data);
                    }
                } else {
                    if (currentContentId !== 'standby') {
                        currentContentId = 'standby';
                        currentUpdatedAt = null;
                        renderStandby();
                    }
                }
            })
            .catch(err => {
                console.warn('Fetch active content retry...', err);
            });
    }

    // Poll setiap 8 detik agar sinkronisasi selalu aktif & instan jika ada update
    setInterval(fetchActiveContent, 8000);

    // ═══════════════════════════════════════════════════════════
    // RENDER CONTENT
    // ═══════════════════════════════════════════════════════════
    function renderContent(data) {
        const ce = data.custom_elements || {};

        // 1. Background Video / Image
        const bgVideo = document.getElementById('bgVideo');
        const bgImg   = document.getElementById('bgImage');
        const bg      = ce.bg || {};

        if (bg.url && bg.type === 'video') {
            bgImg.style.display = 'none';
            bgVideo.style.display = 'block';

            // Cek apakah URL video berubah
            const currentSrc  = bgVideo.currentSrc || bgVideo.src || '';
            const isSrcChange = !currentSrc.includes(bg.url);

            if (isSrcChange) {
                initialSyncDone = false;
                bgVideo.src     = bg.url;
                bgVideo.load();
            }

            // Sync awal satu kali saja (Initial Seek) saat video pertama dimuat
            const performInitialSync = () => {
                if (!initialSyncDone && bgVideo.duration && !isNaN(bgVideo.duration)) {
                    initialSyncDone  = true;
                    lastHardSeekTime = Date.now();
                    const targetSec  = getMasterVideoTime(bgVideo.duration);
                    if (Math.abs(bgVideo.currentTime - targetSec) > 0.25) {
                        try {
                            bgVideo.currentTime = targetSec;
                        } catch (e) {
                            console.warn('Initial seek note:', e);
                        }
                    }
                }
            };

            bgVideo.onloadedmetadata = performInitialSync;
            bgVideo.oncanplay        = performInitialSync;

            bgVideo.muted = isMuted;
            const playPromise = bgVideo.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    performInitialSync();
                }).catch(() => {
                    // Jika unmuted diblokir browser, paksa muted agar video TETAP JALAN bareng!
                    bgVideo.muted = true;
                    bgVideo.play().then(() => performInitialSync()).catch(e => console.warn("Video muted autoplay note", e));
                });
            }
        } else if (bg.url && bg.type === 'image') {
            bgVideo.pause();
            bgVideo.style.display = 'none';
            bgImg.style.display = 'block';
            bgImg.src = bg.url;
            initialSyncDone = false;
        } else {
            bgVideo.pause();
            bgVideo.style.display = 'none';
            bgImg.style.display = 'none';
            initialSyncDone = false;
        }

        // 2. Build Elements HTML (Logos + Content Details)
        let html = '<div class="fade-in">';

        // Logos
        const logos = ce.logos || [];
        logos.forEach(l => {
            if (!l.url) return;
            const x  = l.x  || '85%';
            const y  = l.y  || '10%';
            const fs = l.fs || 8;
            html += `<img src="${l.url}" class="display-item type-logo"
                style="left:${x};top:${y};width:${fs}vw;height:auto;max-width:none;" alt="">`;
        });

        // Content text
        if (data.details && data.details.length) {
            const total = data.details.length;
            const defFS = total === 1 ? 5 : (total <= 3 ? 3.5 : 2.5);

            data.details.forEach((d, i) => {
                const pos   = parsePos(d.positions);
                const x     = (pos.x && pos.x !== '') ? pos.x : '50%';
                const y     = (pos.y && pos.y !== '') ? pos.y : ((35 + i * 10) + '%');
                const fs    = (pos.fs && parseFloat(pos.fs) > 0) ? parseFloat(pos.fs) : defFS;
                const color = d.color || '#FFFF00';
                html += `<div class="display-item"
                    style="left:${x};top:${y};font-size:${fs}vw;color:${color};">${escHtml(d.content)}</div>`;
            });
        }

        html += '</div>';
        swapDisplay(html);
    }

    function renderStandby() {
        const bgVideo = document.getElementById('bgVideo');
        if (bgVideo) {
            bgVideo.pause();
            bgVideo.removeAttribute('src');
            bgVideo.load();
            bgVideo.style.display = 'none';
            initialSyncDone = false;
        }
        
        const bgImg = document.getElementById('bgImage');
        if (bgImg) bgImg.style.display = 'none';

        swapDisplay(`
            <div class="standby-overlay fade-in">
                <div class="standby-clock" id="standbyClock"></div>
                <div class="standby-label">{{ $display->name }} &bull; STANDBY MODE</div>
            </div>
        `);
        updateClock();
    }

    function swapDisplay(html) {
        const area = document.getElementById('displayArea');
        area.classList.remove('fade-in');
        area.classList.add('fade-out');
        setTimeout(() => {
            area.innerHTML = html;
            area.classList.remove('fade-out');
            area.classList.add('fade-in');
        }, 400);
    }

    // ═══════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════
    function parsePos(raw) {
        if (!raw) return {};
        if (typeof raw === 'object') return raw;
        try { return JSON.parse(raw); } catch (e) {}
        return {};
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ═══════════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', () => {
        fetchActiveContent();
    });
    </script>
</body>
</html>
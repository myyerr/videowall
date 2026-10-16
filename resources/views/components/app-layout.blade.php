@props(['pageTitle' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Dashboard' }} — E-Banner Informatika</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg-body:     #101318;
            --bg-sidebar:  #161a22;
            --bg-card:     #1a1f28;
            --bg-input:    #1e232e;
            --bg-hover:    #222833;

            --accent:      #3b82f6;
            --accent-dim:  rgba(59,130,246,0.12);
            --success:     #22c55e;
            --warning:     #eab308;
            --danger:      #ef4444;

            --text-1:   #e8eaed;
            --text-2:   #9aa0ad;
            --text-3:   #5c6370;
            --border-1: #282d38;
            --border-2: #32384a;

            --sidebar-w: 240px;
            --sidebar-col: 64px;
            --topbar-h: 56px;
            --bottom-bar: 56px;
            --radius: 8px;
            --radius-sm: 6px;
            --speed: .18s ease;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-body);
            color: var(--text-1);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ── SIDEBAR ─────────────────────────── */
        .sidebar {
            position: fixed; left: 0; top: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-1);
            display: flex; flex-direction: column;
            z-index: 100;
            transition: width var(--speed);
            overflow: hidden;
        }
        .sidebar.collapsed { width: var(--sidebar-col); }

        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 0 16px; height: var(--topbar-h);
            border-bottom: 1px solid var(--border-1);
            text-decoration: none; flex-shrink: 0;
        }
        .brand-icon {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-icon i { color: #fff; font-size: 14px; }
        .brand-text {
            display: flex; flex-direction: column; line-height: 1.15;
            transition: opacity var(--speed);
        }
        .brand-text-main {
            font-size: 15px; font-weight: 700; color: var(--text-1);
            white-space: nowrap;
        }
        .brand-text-sub {
            font-size: 9px; font-weight: 600; color: var(--text-3);
            letter-spacing: 1.5px; text-transform: uppercase;
        }
        .sidebar.collapsed .brand-text { opacity: 0; pointer-events: none; }

        .sidebar-nav { flex: 1; padding: 12px 8px; overflow-y: auto; overflow-x: hidden; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--border-1); border-radius: 2px; }

        .nav-section-label {
            font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
            color: var(--text-3); padding: 12px 10px 6px;
            white-space: nowrap; transition: opacity var(--speed);
        }
        .sidebar.collapsed .nav-section-label { opacity: 0; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: var(--radius-sm);
            text-decoration: none; color: var(--text-2);
            font-size: 13px; font-weight: 500;
            transition: background var(--speed), color var(--speed);
            white-space: nowrap; margin-bottom: 1px;
        }
        .nav-item:hover { background: var(--bg-hover); color: var(--text-1); }
        .nav-item.active {
            background: var(--accent-dim);
            color: var(--accent);
        }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; flex-shrink: 0; }
        .nav-item span { transition: opacity var(--speed); }
        .sidebar.collapsed .nav-item span { opacity: 0; pointer-events: none; }

        /* ── SIDEBAR TOGGLE ──────────────────── */
        .sidebar-toggle {
            position: fixed; top: 14px; left: calc(var(--sidebar-w) - 14px);
            width: 28px; height: 28px;
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 200;
            transition: left var(--speed);
            color: var(--text-3); font-size: 10px;
        }
        .sidebar-toggle:hover { background: var(--bg-hover); color: var(--text-1); }
        body.sidebar-collapsed-state .sidebar-toggle { left: calc(var(--sidebar-col) - 14px); }

        /* ── MAIN ─────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-w); min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin-left var(--speed);
        }
        body.sidebar-collapsed-state .main-wrap { margin-left: var(--sidebar-col); }

        /* ── TOPBAR ───────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-1);
            display: flex; align-items: center;
            padding: 0 20px; gap: 12px;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { flex: 1; font-size: 15px; font-weight: 600; color: var(--text-1); }
        .topbar-actions { display: flex; align-items: center; gap: 8px; position: relative; }

        .user-avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 12px; color: #fff;
            cursor: pointer; flex-shrink: 0;
        }

        .user-dropdown {
            display: none;
            position: absolute; top: 42px; right: 0;
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: var(--radius);
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            min-width: 180px; z-index: 1000;
        }
        .user-dropdown.show { display: block; animation: ddIn .15s ease; }
        @keyframes ddIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

        .user-dropdown-header { padding: 10px 14px; border-bottom: 1px solid var(--border-1); }
        .user-dropdown-header strong { display: block; font-size: 13px; color: var(--text-1); }
        .user-dropdown-header small { color: var(--text-3); font-size: 11px; }

        /* ── MOBILE BOTTOM BAR ────────────────── */
        .mobile-bottom-bar {
            display: none; position: fixed; bottom: 0; left: 0; right: 0;
            height: var(--bottom-bar);
            background: var(--bg-sidebar);
            border-top: 1px solid var(--border-1);
            z-index: 200; align-items: stretch;
        }
        .mobile-bottom-bar a {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px; text-decoration: none;
            color: var(--text-3); font-size: 10px; font-weight: 600;
            transition: color var(--speed);
        }
        .mobile-bottom-bar a i { font-size: 17px; }
        .mobile-bottom-bar a.active { color: var(--accent); }
        .mobile-bottom-bar a:hover { color: var(--text-1); }

        /* ── BUTTONS ──────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 500; cursor: pointer;
            border: none; text-decoration: none;
            transition: background var(--speed), color var(--speed), border-color var(--speed);
            white-space: nowrap; font-family: inherit;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #2563eb; }
        .btn-ghost { background: transparent; color: var(--text-2); border: 1px solid var(--border-1); }
        .btn-ghost:hover { background: var(--bg-hover); color: var(--text-1); }
        .btn-danger { background: rgba(239,68,68,0.1); color: var(--danger); border: 1px solid rgba(239,68,68,0.2); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-success { background: rgba(34,197,94,0.1); color: var(--success); border: 1px solid rgba(34,197,94,0.2); }
        .btn-success:hover { background: var(--success); color: #fff; }
        .btn-warning { background: rgba(234,179,8,0.1); color: var(--warning); border: 1px solid rgba(234,179,8,0.2); }
        .btn-warning:hover { background: var(--warning); color: #000; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-lg { padding: 11px 22px; font-size: 14px; }

        /* ── CARDS ─────────────────────────────── */
        .page-content { flex: 1; padding: 20px; }
        .card { background: var(--bg-card); border: 1px solid var(--border-1); border-radius: var(--radius); overflow: hidden; }
        .card-header { padding: 14px 16px; border-bottom: 1px solid var(--border-1); display: flex; align-items: center; gap: 8px; }
        .card-title { font-size: 14px; font-weight: 600; color: var(--text-1); }
        .card-body { padding: 16px; }

        /* ── STAT CARDS ────────────────────────── */
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border-1);
            border-radius: var(--radius); padding: 16px;
            display: flex; align-items: center; gap: 14px;
            transition: border-color var(--speed);
        }
        .stat-card:hover { border-color: var(--border-2); }
        .stat-icon {
            width: 40px; height: 40px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .stat-info .stat-value { font-size: 24px; font-weight: 700; line-height: 1; }
        .stat-info .stat-label { font-size: 12px; color: var(--text-2); margin-top: 3px; }

        /* ── BADGE ─────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 4px;
            font-size: 11px; font-weight: 600;
        }
        .badge-active  { background: rgba(34,197,94,0.1); color: var(--success); }
        .badge-draft   { background: rgba(59,130,246,0.1); color: var(--accent); }
        .badge-expired { background: rgba(239,68,68,0.08); color: #f87171; }

        /* ── FORM ──────────────────────────────── */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 8px 12px;
            background: var(--bg-input); border: 1px solid var(--border-1);
            border-radius: var(--radius-sm);
            color: var(--text-1); font-size: 13px; font-family: inherit;
            transition: border-color var(--speed); outline: none;
        }
        .form-control:focus { border-color: var(--accent); }
        .form-control::placeholder { color: var(--text-3); }
        select.form-control option { background: var(--bg-card); }

        /* ── TABLE ─────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table.vw-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .vw-table thead th {
            padding: 10px 14px; background: var(--bg-input);
            color: var(--text-3); font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            text-align: left; border-bottom: 1px solid var(--border-1);
            white-space: nowrap;
        }
        .vw-table tbody td {
            padding: 11px 14px; border-bottom: 1px solid var(--border-1);
            color: var(--text-1); vertical-align: middle;
        }
        .vw-table tbody tr:hover td { background: rgba(255,255,255,0.02); }
        .vw-table tbody tr:last-child td { border-bottom: none; }

        /* ── ALERT / TOAST ─────────────────────── */
        .alert {
            padding: 10px 14px; border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 500; margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; }
        .alert-error { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; }
        .alert-info { background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); color: #60a5fa; }

        #toast-container {
            position: fixed; bottom: 70px; right: 20px;
            z-index: 9999; display: flex; flex-direction: column; gap: 8px;
        }
        .toast {
            background: var(--bg-card); border: 1px solid var(--border-1);
            border-radius: var(--radius-sm); padding: 12px 16px;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            min-width: 240px; animation: toastIn .2s ease;
        }
        .toast.success { border-left: 3px solid var(--success); color: #4ade80; }
        .toast.error { border-left: 3px solid var(--danger); color: #f87171; }
        @keyframes toastIn { from { opacity:0; transform:translateX(12px); } to { opacity:1; transform:translateX(0); } }

        /* ── OVERLAY ───────────────────────────── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90; }

        /* ── GRID / UTILS ──────────────────────── */
        .grid { display: grid; gap: 14px; }
        .grid-cols-1 { grid-template-columns: repeat(1,1fr); }
        .grid-cols-2 { grid-template-columns: repeat(2,1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3,1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4,1fr); }
        @media(max-width:1024px) { .grid-cols-4{grid-template-columns:repeat(2,1fr)} .grid-cols-3{grid-template-columns:repeat(2,1fr)} }
        @media(max-width:640px) { .grid-cols-3,.grid-cols-2{grid-template-columns:repeat(1,1fr)} }
        .flex { display:flex; } .items-center { align-items:center; } .justify-between { justify-content:space-between; }
        .gap-8{gap:8px} .gap-12{gap:12px} .gap-16{gap:16px}
        .mb-8{margin-bottom:8px} .mb-16{margin-bottom:16px} .mb-24{margin-bottom:24px}
        .mt-8{margin-top:8px} .mt-16{margin-top:16px}
        .text-sm{font-size:12px} .text-muted{color:var(--text-2)} .font-bold{font-weight:700} .rounded{border-radius:var(--radius-sm)}

        /* ── RESPONSIVE ────────────────────────── */
        @media (max-width:768px) {
            .sidebar { display: none; }
            .sidebar-toggle { display: none; }
            .main-wrap { margin-left: 0 !important; padding-bottom: var(--bottom-bar); }
            .topbar { padding: 0 14px; }
            .page-content { padding: 14px; }
            .mobile-bottom-bar { display: flex; }
            #toast-container { bottom: calc(var(--bottom-bar) + 10px); right: 10px; }
            .grid-cols-4 { grid-template-columns: repeat(2,1fr); }
        }
    </style>

    @stack('styles')
</head>
<body class="{{ Cookie::get('sidebar_collapsed') === '1' ? 'sidebar-collapsed-state' : '' }}">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── SIDEBAR ──────────────────────────── -->
<aside class="sidebar {{ Cookie::get('sidebar_collapsed') === '1' ? 'collapsed' : '' }}" id="sidebar">
    <a href="{{ route('contents.index') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-tv"></i></div>
        <div class="brand-text">
            <span class="brand-text-main">E-Banner</span>
            <span class="brand-text-sub">Informatika</span>
        </div>
    </a>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu</div>
        <a href="{{ route('contents.index') }}" class="nav-item {{ request()->routeIs('contents.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i><span>Jadwal & Request</span>
        </a>
        <a href="{{ route('displays.index') }}" class="nav-item {{ request()->routeIs('displays.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tv"></i><span>Kelola Display</span>
        </a>
        <a href="{{ route('assets.index') }}" class="nav-item {{ request()->routeIs('assets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-photo-film"></i><span>Asset Manager</span>
        </a>
    </nav>
</aside>

<!-- Sidebar toggle -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fa-solid fa-chevron-left" id="toggleIcon"></i>
</button>

<!-- ── MAIN ─────────────────────────────── -->
<div class="main-wrap" id="mainWrap">
    <header class="topbar">
        <div class="topbar-title">
            @isset($pageTitle){{ $pageTitle }}@else E-Banner Informatika @endisset
        </div>
        <div class="topbar-actions">
            <div class="user-avatar" id="userAvatarBtn" title="User Menu">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div id="userDropdown" class="user-dropdown">
                <div class="user-dropdown-header">
                    <strong>{{ auth()->user()->name ?? 'User' }}</strong>
                    <small>{{ auth()->user()->email ?? '' }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;padding:8px 10px;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;color:var(--danger);border-color:rgba(239,68,68,0.2)">
                        <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    @if(session('success'))
    <div style="padding:14px 20px 0;">
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div style="padding:14px 20px 0;">
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i>{{ session('error') }}</div>
    </div>
    @endif
    @if($errors->any())
    <div style="padding:14px 20px 0;">
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <main class="page-content">
        {{ $slot }}
    </main>
</div>

<!-- ── MOBILE BOTTOM BAR ───────────────── -->
<nav class="mobile-bottom-bar">
    <a href="{{ route('contents.index') }}" class="{{ request()->routeIs('contents.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-days"></i>
        Jadwal
    </a>
    <a href="{{ route('displays.index') }}" class="{{ request()->routeIs('displays.*') ? 'active' : '' }}">
        <i class="fa-solid fa-tv"></i>
        Display
    </a>
    <a href="{{ route('assets.index') }}" class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">
        <i class="fa-solid fa-photo-film"></i>
        Asset
    </a>
</nav>

<div id="toast-container"></div>

<script>
const sidebar    = document.getElementById('sidebar');
const toggleBtn  = document.getElementById('sidebarToggle');
const toggleIcon = document.getElementById('toggleIcon');
const overlay    = document.getElementById('sidebarOverlay');
let isCollapsed  = document.body.classList.contains('sidebar-collapsed-state');

function updateToggle() {
    sidebar.classList.toggle('collapsed', isCollapsed);
    document.body.classList.toggle('sidebar-collapsed-state', isCollapsed);
    toggleIcon.className = isCollapsed ? 'fa-solid fa-chevron-right' : 'fa-solid fa-chevron-left';
    document.cookie = 'sidebar_collapsed=' + (isCollapsed ? '1' : '0') + '; path=/; max-age=31536000';
}

if (toggleBtn) toggleBtn.addEventListener('click', () => { isCollapsed = !isCollapsed; updateToggle(); });
if (overlay) overlay.addEventListener('click', () => { overlay.classList.remove('active'); });

const userAvatarBtn = document.getElementById('userAvatarBtn');
const userDropdown = document.getElementById('userDropdown');
if (userAvatarBtn && userDropdown) {
    userAvatarBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
    });
    document.addEventListener('click', (e) => {
        if (!userDropdown.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
    });
}

function showToast(message, type = 'success') {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i> ${message}`;
    c.appendChild(t);
    
    try {
        const soundFile = type === 'success' ? '/sounds/sukses.mp3' : '/sounds/error.mp3';
        const audio = new Audio(soundFile);
        audio.play().catch(e => console.warn("Audio play prevented:", e));
    } catch(err) {}

    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity 0.3s'; setTimeout(()=>t.remove(),300); }, 3500);
}

// Auto-trigger toast & audio feedback saat reload/redirect
document.addEventListener('DOMContentLoaded', () => {
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif
    @if($errors->any())
        showToast(@json($errors->first()), 'error');
    @endif
});
</script>

@stack('scripts')
</body>
</html>

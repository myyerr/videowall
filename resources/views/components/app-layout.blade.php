@props(['pageTitle' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Videowall' }} — Videowall</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg-base:     #0A0F1E;
            --bg-surface:  #111827;
            --bg-elevated: #1A2235;
            --accent:      #6366F1;
            --accent-2:    #818CF8;
            --accent-glow: rgba(99,102,241,0.25);
            --success:     #10B981;
            --warning:     #F59E0B;
            --danger:      #EF4444;
            --text-primary:   #F1F5F9;
            --text-secondary: #94A3B8;
            --text-muted:     #475569;
            --border:         rgba(255,255,255,0.08);
            --border-accent:  rgba(99,102,241,0.4);
            --sidebar-w: 260px;
            --sidebar-col: 70px;
            --header-h:  64px;
            --radius:    12px;
            --radius-sm: 8px;
            --trans:     0.25s cubic-bezier(0.4,0,0.2,1);
            --bottom-bar: 64px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg-base); color:var(--text-primary); min-height:100vh; overflow-x:hidden; }

        /* ── SIDEBAR (desktop) ─────────────────────────── */
        .sidebar {
            position:fixed; left:0; top:0;
            width:var(--sidebar-w); height:100vh;
            background:var(--bg-surface); border-right:1px solid var(--border);
            display:flex; flex-direction:column; z-index:100;
            transition:width var(--trans); overflow:hidden;
        }
        .sidebar.collapsed { width:var(--sidebar-col); }
        .sidebar-brand {
            display:flex; align-items:center; gap:12px;
            padding:0 20px; height:var(--header-h);
            border-bottom:1px solid var(--border); text-decoration:none; flex-shrink:0;
        }
        .brand-icon {
            width:36px; height:36px;
            background:linear-gradient(135deg,var(--accent),#8B5CF6);
            border-radius:10px; display:flex; align-items:center; justify-content:center;
            flex-shrink:0; box-shadow:0 0 20px var(--accent-glow);
        }
        .brand-icon i { color:#fff; font-size:16px; }
        .brand-text {
            font-size:18px; font-weight:800;
            background:linear-gradient(135deg,#fff,var(--accent-2));
            -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            white-space:nowrap; transition:opacity var(--trans);
        }
        .sidebar.collapsed .brand-text { opacity:0; pointer-events:none; }
        .sidebar-nav { flex:1; padding:16px 12px; overflow-y:auto; overflow-x:hidden; }
        .sidebar-nav::-webkit-scrollbar { width:4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background:var(--border); border-radius:2px; }
        .nav-section-label {
            font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase;
            color:var(--text-muted); padding:8px 10px 4px; white-space:nowrap; transition:opacity var(--trans);
        }
        .sidebar.collapsed .nav-section-label { opacity:0; }
        .nav-item {
            display:flex; align-items:center; gap:12px; padding:11px 12px;
            border-radius:var(--radius-sm); text-decoration:none; color:var(--text-secondary);
            font-size:14px; font-weight:500; transition:all var(--trans);
            white-space:nowrap; position:relative; margin-bottom:2px;
        }
        .nav-item:hover { background:rgba(99,102,241,0.1); color:var(--text-primary); }
        .nav-item.active {
            background:var(--accent-glow); color:var(--accent-2);
            border:1px solid var(--border-accent);
        }
        .nav-item.active::before {
            content:''; position:absolute; left:0; top:20%; bottom:20%;
            width:3px; background:var(--accent); border-radius:0 3px 3px 0;
        }
        .nav-item i { width:20px; text-align:center; font-size:15px; flex-shrink:0; }
        .nav-item span { transition:opacity var(--trans); }
        .sidebar.collapsed .nav-item span { opacity:0; pointer-events:none; }
        .sidebar-footer { padding:12px; border-top:1px solid var(--border); flex-shrink:0; }
        .user-card {
            display:flex; align-items:center; gap:10px; padding:10px;
            border-radius:var(--radius-sm); background:var(--bg-elevated); overflow:hidden;
        }
        .user-avatar {
            width:34px; height:34px;
            background:linear-gradient(135deg,var(--accent),#8B5CF6);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:13px; color:#fff; flex-shrink:0;
        }
        .user-info { flex:1; min-width:0; transition:opacity var(--trans); }
        .sidebar.collapsed .user-info { opacity:0; }
        .user-name { font-size:13px; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .user-role { font-size:11px; color:var(--text-muted); }

        /* ── SIDEBAR TOGGLE ─────────────────────────────── */
        .sidebar-toggle {
            position:fixed; top:18px; left:calc(var(--sidebar-w) - 16px);
            width:32px; height:32px; background:var(--bg-surface);
            border:1px solid var(--border); border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; z-index:200; transition:left var(--trans);
            box-shadow:0 4px 12px rgba(0,0,0,0.3); color:var(--text-secondary); font-size:12px;
        }
        .sidebar-toggle:hover { background:var(--accent); color:#fff; border-color:var(--accent); }
        body.sidebar-collapsed-state .sidebar-toggle { left:calc(var(--sidebar-col) - 16px); }

        /* ── MAIN WRAPPER ───────────────────────────────── */
        .main-wrap {
            margin-left:var(--sidebar-w); min-height:100vh;
            display:flex; flex-direction:column; transition:margin-left var(--trans);
        }
        body.sidebar-collapsed-state .main-wrap { margin-left:var(--sidebar-col); }

        /* ── TOPBAR ─────────────────────────────────────── */
        .topbar {
            height:var(--header-h); background:var(--bg-surface);
            border-bottom:1px solid var(--border); display:flex; align-items:center;
            padding:0 24px; gap:16px; position:sticky; top:0; z-index:50;
        }
        .topbar-title { flex:1; font-size:17px; font-weight:700; color:var(--text-primary); }
        .topbar-actions { display:flex; align-items:center; gap:10px; position:relative; }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 45px;
            right: 0;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            min-width: 200px;
            z-index: 1000;
        }
        .user-dropdown.show {
            display: block;
            animation: fadeInDown 0.2s ease;
        }
        @keyframes fadeInDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
        .user-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }
        .user-dropdown-header strong { display:block; font-size:14px; color:var(--text-primary); }
        .user-dropdown-header small { color:var(--text-secondary); font-size:12px; }

        /* ── MOBILE BOTTOM BAR ──────────────────────────── */
        .mobile-bottom-bar {
            display:none;
            position:fixed; bottom:0; left:0; right:0;
            height:var(--bottom-bar);
            background:var(--bg-surface);
            border-top:1px solid var(--border);
            z-index:200;
            align-items:stretch;
        }
        .mobile-bottom-bar a {
            flex:1; display:flex; flex-direction:column;
            align-items:center; justify-content:center;
            gap:4px; text-decoration:none;
            color:var(--text-muted); font-size:10px; font-weight:600;
            transition:color var(--trans);
        }
        .mobile-bottom-bar a i { font-size:18px; }
        .mobile-bottom-bar a.active { color:var(--accent-2); }
        .mobile-bottom-bar a:hover { color:var(--text-primary); }

        /* ── BUTTONS ────────────────────────────────────── */
        .btn {
            display:inline-flex; align-items:center; gap:8px;
            padding:9px 18px; border-radius:var(--radius-sm);
            font-size:13px; font-weight:600; cursor:pointer; border:none;
            text-decoration:none; transition:all var(--trans); white-space:nowrap;
        }
        .btn-primary { background:var(--accent); color:#fff; box-shadow:0 4px 15px var(--accent-glow); }
        .btn-primary:hover { background:var(--accent-2); transform:translateY(-1px); }
        .btn-ghost { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
        .btn-ghost:hover { background:var(--bg-elevated); color:var(--text-primary); }
        .btn-danger { background:rgba(239,68,68,0.15); color:var(--danger); border:1px solid rgba(239,68,68,0.3); }
        .btn-danger:hover { background:var(--danger); color:#fff; }
        .btn-success { background:rgba(16,185,129,0.15); color:var(--success); border:1px solid rgba(16,185,129,0.3); }
        .btn-success:hover { background:var(--success); color:#fff; }
        .btn-warning { background:rgba(245,158,11,0.15); color:var(--warning); border:1px solid rgba(245,158,11,0.3); }
        .btn-warning:hover { background:var(--warning); color:#fff; }
        .btn-sm { padding:6px 12px; font-size:12px; }
        .btn-lg { padding:13px 28px; font-size:15px; }

        /* ── CARDS ──────────────────────────────────────── */
        .page-content { flex:1; padding:28px 24px; }
        .card { background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
        .card-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
        .card-title { font-size:15px; font-weight:700; color:var(--text-primary); }
        .card-body { padding:20px; }

        /* ── STAT CARDS ─────────────────────────────────── */
        .stat-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px; display:flex; align-items:center; gap:16px; transition:all var(--trans); }
        .stat-card:hover { border-color:var(--border-accent); transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.3); }
        .stat-icon { width:48px; height:48px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .stat-info .stat-value { font-size:28px; font-weight:800; line-height:1; }
        .stat-info .stat-label { font-size:12px; color:var(--text-secondary); margin-top:4px; }

        /* ── BADGE ──────────────────────────────────────── */
        .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-active  { background:rgba(16,185,129,0.15); color:var(--success); border:1px solid rgba(16,185,129,0.3); }
        .badge-draft   { background:rgba(99,102,241,0.15); color:var(--accent-2); border:1px solid rgba(99,102,241,0.3); }
        .badge-expired { background:rgba(239,68,68,0.15); color:var(--danger); border:1px solid rgba(239,68,68,0.3); }

        /* ── FORM ELEMENTS ──────────────────────────────── */
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:8px; }
        .form-control {
            width:100%; padding:10px 14px; background:var(--bg-elevated);
            border:1px solid var(--border); border-radius:var(--radius-sm);
            color:var(--text-primary); font-size:14px; font-family:'Inter',sans-serif;
            transition:border-color var(--trans),box-shadow var(--trans); outline:none;
        }
        .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); }
        .form-control::placeholder { color:var(--text-muted); }
        select.form-control option { background:var(--bg-surface); }

        /* ── TABLE ──────────────────────────────────────── */
        .table-wrap { overflow-x:auto; }
        table.vw-table { width:100%; border-collapse:collapse; font-size:13px; }
        .vw-table thead th { padding:12px 16px; background:var(--bg-elevated); color:var(--text-secondary); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; text-align:left; border-bottom:1px solid var(--border); white-space:nowrap; }
        .vw-table tbody td { padding:13px 16px; border-bottom:1px solid var(--border); color:var(--text-primary); vertical-align:middle; }
        .vw-table tbody tr:hover td { background:rgba(99,102,241,0.04); }
        .vw-table tbody tr:last-child td { border-bottom:none; }

        /* ── ALERT / TOAST ──────────────────────────────── */
        .alert { padding:12px 16px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; margin-bottom:16px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#34D399; }
        .alert-error { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#F87171; }
        .alert-info { background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.3); color:#60A5FA; }
        #toast-container { position:fixed; bottom:80px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; }
        .toast { background:var(--bg-elevated); border:1px solid var(--border); border-radius:var(--radius-sm); padding:14px 18px; font-size:13px; font-weight:500; display:flex; align-items:center; gap:10px; box-shadow:0 8px 24px rgba(0,0,0,0.4); min-width:280px; animation:slideInToast 0.3s ease; }
        .toast.success { border-left:3px solid var(--success); color:#34D399; }
        .toast.error { border-left:3px solid var(--danger); color:#F87171; }
        @keyframes slideInToast { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }

        /* ── OVERLAY ────────────────────────────────────── */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:90; }

        /* ── GRID / UTILS ───────────────────────────────── */
        .grid { display:grid; gap:16px; }
        .grid-cols-1 { grid-template-columns:repeat(1,1fr); }
        .grid-cols-2 { grid-template-columns:repeat(2,1fr); }
        .grid-cols-3 { grid-template-columns:repeat(3,1fr); }
        .grid-cols-4 { grid-template-columns:repeat(4,1fr); }
        @media(max-width:1024px) { .grid-cols-4{grid-template-columns:repeat(2,1fr)} .grid-cols-3{grid-template-columns:repeat(2,1fr)} }
        @media(max-width:640px) { .grid-cols-3,.grid-cols-2{grid-template-columns:repeat(1,1fr)} }
        .flex { display:flex; } .items-center { align-items:center; } .justify-between { justify-content:space-between; }
        .gap-8{gap:8px} .gap-12{gap:12px} .gap-16{gap:16px}
        .mb-8{margin-bottom:8px} .mb-16{margin-bottom:16px} .mb-24{margin-bottom:24px}
        .mt-8{margin-top:8px} .mt-16{margin-top:16px}
        .text-sm{font-size:12px} .text-muted{color:var(--text-secondary)} .font-bold{font-weight:700} .rounded{border-radius:var(--radius-sm)}

        /* ── RESPONSIVE ─────────────────────────────────── */
        @media (max-width:768px) {
            .sidebar { display:none; }
            .sidebar-toggle { display:none; }
            .main-wrap { margin-left:0 !important; padding-bottom:var(--bottom-bar); }
            .topbar { padding:0 16px; }
            .page-content { padding:16px; }
            .mobile-bottom-bar { display:flex; }
            #toast-container { bottom:calc(var(--bottom-bar) + 12px); right:12px; }
            .grid-cols-4 { grid-template-columns:repeat(2,1fr); }
        }
    </style>

    @stack('styles')
</head>
<body class="{{ Cookie::get('sidebar_collapsed') === '1' ? 'sidebar-collapsed-state' : '' }}">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── SIDEBAR (desktop only) ──────────────────────────── -->
<aside class="sidebar {{ Cookie::get('sidebar_collapsed') === '1' ? 'collapsed' : '' }}" id="sidebar">
    <a href="{{ route('contents.index') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-display"></i></div>
        <span class="brand-text">Videowall</span>
    </a>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
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

<!-- Sidebar toggle (desktop) -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fa-solid fa-chevron-left" id="toggleIcon"></i>
</button>

<!-- ── MAIN WRAPPER ─────────────────────────────────────── -->
<div class="main-wrap" id="mainWrap">
    <header class="topbar">
        <div class="topbar-title">
            @isset($pageTitle){{ $pageTitle }}@else Videowall @endisset
        </div>
        <div class="topbar-actions">
            <div class="user-avatar" id="userAvatarBtn" style="width: 34px; height: 34px; box-shadow: 0 0 10px rgba(99,102,241,0.3); cursor: pointer;" title="User Menu">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            
            <div id="userDropdown" class="user-dropdown">
                <div class="user-dropdown-header">
                    <strong>{{ auth()->user()->name ?? 'User' }}</strong>
                    <small>{{ auth()->user()->email ?? '' }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 10px;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;color:var(--danger);border-color:rgba(239,68,68,0.3)">
                        <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    @if(session('success'))
    <div style="padding:16px 24px 0;">
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div style="padding:16px 24px 0;">
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i>{{ session('error') }}</div>
    </div>
    @endif
    @if($errors->any())
    <div style="padding:16px 24px 0;">
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

<!-- ── MOBILE BOTTOM BAR ────────────────────────────────── -->
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

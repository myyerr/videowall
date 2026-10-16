<x-app-layout pageTitle="Jadwal & Request E-Banner">
    <x-slot name="slot">

    @push('styles')
    <style>
        /* ── STATS ───────────────────────────── */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        @media(max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; } }

        /* ── GANTT ───────────────────────────── */
        .gantt-card {
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: var(--radius);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .gantt-filters {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-1);
            display: flex; align-items: center;
            gap: 10px; flex-wrap: wrap;
        }

        .filter-label { font-size: 12px; font-weight: 600; color: var(--text-3); white-space: nowrap; }

        .date-input {
            background: var(--bg-input);
            border: 1px solid var(--border-1);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            color: var(--text-1);
            font-size: 12px;
            font-family: inherit;
            outline: none;
            width: 140px;
            cursor: pointer;
            transition: border-color .18s;
        }
        .date-input:focus { border-color: var(--accent); }

        .shortcut-group {
            display: inline-flex;
            border: 1px solid var(--border-1);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .shortcut-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 600;
            background: var(--bg-input);
            color: var(--text-3);
            border: none;
            border-right: 1px solid var(--border-1);
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
        }
        .shortcut-btn:last-child { border-right: none; }
        .shortcut-btn:hover, .shortcut-btn.active {
            background: var(--accent);
            color: #fff;
        }

        .gantt-scroll { overflow-x: auto; }

        table.gantt-tbl {
            border-collapse: collapse;
            min-width: 100%;
            table-layout: fixed;
            font-size: 11px;
        }

        .gantt-tbl .col-jam {
            width: 52px; min-width: 52px;
            position: sticky; left: 0; z-index: 3;
            background: var(--bg-input);
            color: var(--text-3);
            font-weight: 600;
            text-align: center;
            border: 1px solid var(--border-1);
            padding: 3px;
            white-space: nowrap;
        }

        .gantt-tbl thead .col-jam {
            background: var(--bg-body);
            color: var(--text-3);
            font-size: 10px;
            z-index: 5;
        }

        .gantt-tbl thead .col-date {
            width: 32px; min-width: 32px;
            text-align: center;
            padding: 5px 2px;
            background: var(--bg-input);
            color: var(--text-2);
            border: 1px solid var(--border-1);
            font-weight: 600;
            font-size: 10px;
            white-space: nowrap;
        }

        .gantt-tbl thead .col-date.today { background: var(--accent) !important; color: #fff !important; }
        .gantt-tbl thead .col-date.weekend { color: var(--warning) !important; }

        .gantt-tbl tbody tr { height: 22px; }

        .gantt-tbl tbody td.col-cell {
            text-align: center;
            border: 1px solid rgba(255,255,255,0.03);
            padding: 0;
            background: var(--bg-body);
            position: relative;
        }

        .gantt-tbl tbody td.gc-active {
            background: var(--success) !important;
            cursor: pointer;
        }
        .gantt-tbl tbody td.gc-draft {
            background: var(--accent) !important;
            cursor: pointer;
        }
        .gantt-tbl tbody td.gc-expired {
            background: rgba(100,116,139,0.3) !important;
        }

        .gantt-tbl tbody td.col-cell.today-col { background: rgba(59,130,246,0.04); }

        .gantt-legend {
            display: flex; align-items: center;
            gap: 16px; padding: 8px 16px;
            background: var(--bg-input);
            border-top: 1px solid var(--border-1);
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; color: var(--text-2);
        }

        .legend-dot {
            width: 10px; height: 10px;
            border-radius: 2px;
        }

        /* Gantt tooltip */
        .gantt-tooltip {
            display: none;
            position: fixed;
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            color: var(--text-1);
            font-size: 12px;
            border-radius: var(--radius-sm);
            padding: 10px 12px;
            z-index: 9999;
            pointer-events: none;
            max-width: 220px;
            line-height: 1.6;
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }

        /* ── REQUEST TABLE ───────────────────── */
        .request-table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: var(--radius);
        }

        @media (max-width: 768px) {
            .gantt-filters { flex-direction: column; align-items: stretch; gap: 10px; }
            .date-filter-group { display: flex; align-items: center; gap: 8px; width: 100%; }
            .date-input { flex: 1; width: 100% !important; text-align: center; }
            .shortcut-group { width: 100%; display: flex; }
            .shortcut-btn { flex: 1; text-align: center; }
            .gantt-filters > .btn { width: 100%; justify-content: center; margin-left: 0 !important; }
            .table-filters { flex-direction: column; align-items: stretch; width: 100%; margin-top: 10px; }
            .table-filters select { width: 100% !important; }
            .card-header-wrap { flex-wrap: wrap; }
            .hide-mobile { display: none !important; }
            .mobile-only-info { display: block !important; }
        }
    </style>
    @endpush

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34,197,94,0.1);">
                <i class="fa-solid fa-circle-play" style="color: var(--success);"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: var(--success);">{{ $stats['active'] }}</div>
                <div class="stat-label">Sedang Tayang</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59,130,246,0.1);">
                <i class="fa-solid fa-clock" style="color: var(--accent);"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: var(--accent);">{{ $stats['draft'] }}</div>
                <div class="stat-label">Dijadwalkan</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(234,179,8,0.1);">
                <i class="fa-solid fa-calendar-day" style="color: var(--warning);"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: var(--warning);">{{ $stats['today'] }}</div>
                <div class="stat-label">Jadwal Hari Ini</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239,68,68,0.08);">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--danger);"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: var(--danger);">{{ $stats['expired'] }}</div>
                <div class="stat-label">Expired</div>
            </div>
        </div>
    </div>

    <!-- Gantt Chart -->
    <div class="gantt-card">
        <div class="card-header">
            <i class="fa-solid fa-chart-gantt" style="color: var(--accent);"></i>
            <span class="card-title">Kalender Jadwal</span>
            <div style="margin-left: auto; font-size: 11px; color: var(--text-3);">
                {{ date('d M Y', strtotime($filterFrom)) }} — {{ date('d M Y', strtotime($filterTo)) }}
            </div>
        </div>

        <div class="gantt-filters">
            <div class="date-filter-group">
                <span class="filter-label hide-mobile"><i class="fa-solid fa-calendar"></i> Periode:</span>
                <input type="text" id="pickerFrom" class="date-input"
                       value="{{ $filterFrom }}" readonly placeholder="yyyy-mm-dd">
                <span style="font-size: 11px; color: var(--text-3);">s/d</span>
                <input type="text" id="pickerTo" class="date-input"
                       value="{{ $filterTo }}" readonly placeholder="yyyy-mm-dd">
            </div>
            
            <button class="btn btn-primary btn-sm" onclick="loadGantt()">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
            </button>
            
            <div class="shortcut-group">
                <button class="shortcut-btn" id="scDay"   onclick="ganttShortcut('day')">Hari</button>
                <button class="shortcut-btn" id="scWeek"  onclick="ganttShortcut('week')">Minggu</button>
                <button class="shortcut-btn" id="scMonth" onclick="ganttShortcut('month')">Bulan</button>
            </div>
            
            <a href="{{ route('contents.create') }}" class="btn btn-primary btn-sm" style="margin-left: auto;">
                <i class="fa-solid fa-plus"></i> Buat Request
            </a>
        </div>

        <div class="gantt-scroll">
            <table class="gantt-tbl" id="ganttTable">
                <thead>
                    <tr>
                        <th class="col-jam">Jam</th>
                        @foreach($rangeDates as $dateStr)
                            @php
                                $dateObj   = \Carbon\Carbon::parse($dateStr);
                                $dayOfWeek = $dateObj->dayOfWeek;
                                $isToday   = ($dateStr === date('Y-m-d'));
                                $isWeekend = in_array($dayOfWeek, [0, 6]);

                                $thClass = 'col-date';
                                if ($isToday) $thClass .= ' today';
                                elseif ($isWeekend) $thClass .= ' weekend';
                            @endphp
                            <th class="{{ $thClass }}" title="{{ $dateObj->format('d M Y') }} ({{ $dateObj->isoFormat('ddd') }})">
                                <div>{{ $dateObj->format('d') }}</div>
                                <div style="font-size: 9px; opacity: 0.6;">{{ $dateObj->isoFormat('ddd') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for ($h = 7; $h <= 16; $h++)
                        @php $jamLabel = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                        <tr>
                            <td class="col-jam">{{ $jamLabel }}</td>
                            @foreach($rangeDates as $dateStr)
                                @php
                                    $isToday    = ($dateStr === date('Y-m-d'));
                                    $isWeekend  = in_array(\Carbon\Carbon::parse($dateStr)->dayOfWeek, [0, 6]);
                                    $activeReq  = null;
                                    $tdClass    = 'col-cell';

                                    if ($isToday) $tdClass .= ' today-col';

                                    if (isset($ganttData[$dateStr])) {
                                        foreach ($ganttData[$dateStr] as $req) {
                                            $startH = (int) substr($req->start_time, 0, 2);
                                            $endH   = (int) substr($req->end_time, 0, 2);
                                            if ($h >= $startH && $h < $endH) {
                                                $activeReq = $req;
                                                break;
                                            }
                                        }
                                    }

                                    if ($activeReq) {
                                        if ($activeReq->status === 'active') $tdClass .= ' gc-active';
                                        elseif ($activeReq->status === 'expired') $tdClass .= ' gc-expired';
                                        else $tdClass .= ' gc-draft';
                                    }
                                @endphp
                                <td class="{{ $tdClass }}"
                                    @if($activeReq)
                                        data-title="{{ addslashes($activeReq->title) }}"
                                        data-name="{{ addslashes($activeReq->creator->name ?? '-') }}"
                                        data-start="{{ $activeReq->start_time }}"
                                        data-end="{{ $activeReq->end_time }}"
                                        data-status="{{ $activeReq->status }}"
                                        data-display="{{ addslashes($activeReq->display->name ?? '-') }}"
                                        onmouseenter="showGanttTip(event, this)"
                                        onmouseleave="hideGanttTip()"
                                    @endif
                                >&nbsp;</td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="gantt-legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: var(--success);"></span>
                Sedang Tayang
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: var(--accent);"></span>
                Terjadwal
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: rgba(100,116,139,0.3); border: 1px solid var(--border-1);"></span>
                Expired
            </div>
            <div class="legend-item" style="margin-left: auto; font-size: 10px; color: var(--text-3);">
                <i class="fa-solid fa-arrow-pointer"></i> Hover untuk detail
            </div>
        </div>
    </div>

    <!-- Tooltip -->
    <div class="gantt-tooltip" id="ganttTip"></div>

    <!-- Request Table -->
    <div class="request-table-card">
        <div class="card-header card-header-wrap">
            <i class="fa-solid fa-list-check" style="color: var(--accent);"></i>
            <span class="card-title">Semua Request</span>
            <form method="GET" action="{{ route('contents.index') }}" class="table-filters" style="margin-left: auto; display: flex; gap: 8px;">
                <select name="display_id" class="form-control" style="width: 150px; padding: 6px 8px; font-size: 12px;"
                        onchange="this.form.submit()">
                    <option value="">Semua Display</option>
                    @foreach($displays as $d)
                        <option value="{{ $d->id }}" {{ request('display_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="form-control" style="width: 130px; padding: 6px 8px; font-size: 12px;"
                        onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Sedang Tayang</option>
                    <option value="draft"   {{ request('status') === 'draft'   ? 'selected' : '' }}>Draft</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </form>
        </div>

        <div class="table-wrap">
            <table class="vw-table">
                <thead>
                    <tr>
                        <th class="hide-mobile">#</th>
                        <th>Judul & Pembuat</th>
                        <th class="hide-mobile">Display</th>
                        <th>Jadwal Tayang</th>
                        <th class="hide-mobile">Konten</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $i => $c)
                        <tr>
                            <td class="hide-mobile" style="color: var(--text-3);">{{ $contents->firstItem() + $i }}</td>
                            <td>
                                <div style="font-weight: 600; font-size: 13px;">{{ $c->title }}</div>
                                <div style="font-size: 11px; color: var(--text-3); margin-top: 2px;">
                                    <i class="fa-solid fa-user fa-xs"></i>
                                    {{ $c->creator->name ?? '—' }}
                                </div>
                                <!-- Mobile Only Info -->
                                <div style="font-size: 11px; margin-top: 4px; display: none;" class="mobile-only-info">
                                    <span style="color: var(--accent);"><i class="fa-solid fa-tv fa-xs"></i> {{ $c->display->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <span style="font-size: 12px; background: var(--accent-dim); padding: 2px 7px; border-radius: 3px; color: var(--accent);">
                                    <i class="fa-solid fa-tv fa-xs"></i>
                                    {{ $c->display->name ?? '—' }}
                                </span>
                            </td>
                            <td style="font-size: 12px;">
                                @if($c->display_date)
                                    <div style="font-weight: 600;">{{ date('d M Y', strtotime($c->display_date)) }}</div>
                                    <div style="color: var(--text-3);">
                                        {{ substr($c->start_time, 0, 5) }} – {{ substr($c->end_time, 0, 5) }}
                                    </div>
                                @else
                                    <span style="color: var(--text-3);">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="max-width: 200px;">
                                    @foreach($c->details->take(2) as $d)
                                        <div style="font-size: 11px; color: {{ $d->color ?? 'var(--text-2)' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            • {{ $d->content_text }}
                                        </div>
                                    @endforeach
                                    @if($c->details->count() > 2)
                                        <div style="font-size: 10px; color: var(--text-3);">+{{ $c->details->count() - 2 }} lagi</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($c->status === 'active')
                                    <span class="badge badge-active"><i class="fa-solid fa-circle fa-xs"></i> Tayang</span>
                                @elseif($c->status === 'draft')
                                    <span class="badge badge-draft"><i class="fa-solid fa-clock fa-xs"></i> Draft</span>
                                @else
                                    <span class="badge badge-expired"><i class="fa-solid fa-xmark fa-xs"></i> Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-8">
                                    <a href="{{ route('contents.edit', $c) }}" class="btn btn-ghost btn-sm" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    @if($c->status === 'active')
                                        <button class="btn btn-warning btn-sm"
                                                onclick="expireContent({{ $c->id }})"
                                                title="Set Expired">
                                            <i class="fa-solid fa-stop"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-danger btn-sm"
                                            onclick="deleteContent({{ $c->id }})"
                                            title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-3);">
                                <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                                Belum ada request. <a href="{{ route('contents.create') }}" style="color: var(--accent);">Buat request baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contents->hasPages())
            <div style="padding: 14px 16px; border-top: 1px solid var(--border-1); display: flex; justify-content: flex-end; gap: 8px;">
                {{ $contents->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // ── Date pickers ──────────────────────────────────────────
        document.querySelectorAll('.date-input').forEach(el => {
            el.type = 'date';
            el.style.width = '150px';
        });

        // ── Load Gantt ────────────────────────────────────────────
        function loadGantt() {
            const from = document.getElementById('pickerFrom').value;
            const to   = document.getElementById('pickerTo').value;
            if (!from || !to) return;
            window.location.href = '{{ route("contents.index") }}?from=' + from + '&to=' + to;
        }

        // ── Shortcuts ─────────────────────────────────────────────
        function fmtDate(d) {
            return d.getFullYear() + '-'
                + String(d.getMonth() + 1).padStart(2, '0') + '-'
                + String(d.getDate()).padStart(2, '0');
        }

        function ganttShortcut(mode) {
            const now = new Date(), dow = now.getDay();
            let from, to;
            if (mode === 'day') {
                from = to = fmtDate(now);
            } else if (mode === 'week') {
                const daysToMon = (dow === 0) ? 6 : dow - 1;
                const mon = new Date(now); mon.setDate(now.getDate() - daysToMon);
                const sun = new Date(mon); sun.setDate(mon.getDate() + 6);
                from = fmtDate(mon); to = fmtDate(sun);
            } else {
                const first = new Date(now.getFullYear(), now.getMonth(), 1);
                const last  = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                from = fmtDate(first); to = fmtDate(last);
            }
            document.getElementById('pickerFrom').value = from;
            document.getElementById('pickerTo').value   = to;
            window.location.href = '{{ route("contents.index") }}?from=' + from + '&to=' + to;
        }

        // Highlight active shortcut
        (function () {
            const from  = '{{ $filterFrom }}';
            const to    = '{{ $filterTo }}';
            const now   = new Date(), dow = now.getDay();
            const today = fmtDate(now);
            const daysToMon = (dow === 0) ? 6 : dow - 1;
            const mon = new Date(now); mon.setDate(now.getDate() - daysToMon);
            const sun = new Date(mon); sun.setDate(mon.getDate() + 6);
            const first = new Date(now.getFullYear(), now.getMonth(), 1);
            const last  = new Date(now.getFullYear(), now.getMonth() + 1, 0);

            if (from === today && to === today)                       document.getElementById('scDay').classList.add('active');
            else if (from === fmtDate(mon) && to === fmtDate(sun))   document.getElementById('scWeek').classList.add('active');
            else if (from === fmtDate(first) && to === fmtDate(last)) document.getElementById('scMonth').classList.add('active');
        })();

        // ── Gantt tooltip ─────────────────────────────────────────
        function showGanttTip(e, el) {
            const tip    = document.getElementById('ganttTip');
            const status = el.dataset.status;
            const statusColor = status === 'active' ? 'var(--success)' : status === 'draft' ? 'var(--accent)' : 'var(--text-3)';
            tip.innerHTML = `
                <div style="font-weight:600;margin-bottom:3px;">${el.dataset.title}</div>
                <div style="font-size:11px;color:var(--text-3);margin-bottom:5px;">${el.dataset.display}</div>
                <div style="font-size:11px;"><i class="fa-regular fa-user"></i> ${el.dataset.name}</div>
                <div style="font-size:11px;"><i class="fa-regular fa-clock"></i> ${el.dataset.start} – ${el.dataset.end}</div>
                <div style="font-size:11px;margin-top:3px;color:${statusColor};">● ${status.charAt(0).toUpperCase() + status.slice(1)}</div>
            `;
            tip.style.display = 'block';
            positionTip(e);
        }

        document.addEventListener('mousemove', e => {
            const tip = document.getElementById('ganttTip');
            if (tip.style.display === 'block') positionTip(e);
        });

        function positionTip(e) {
            const tip = document.getElementById('ganttTip');
            let x = e.clientX + 15, y = e.clientY + 15;
            if (x + 230 > window.innerWidth) x = e.clientX - 240;
            if (y + 130 > window.innerHeight) y = e.clientY - 130;
            tip.style.left = x + 'px';
            tip.style.top  = y + 'px';
        }

        function hideGanttTip() {
            document.getElementById('ganttTip').style.display = 'none';
        }

        // ── Actions ───────────────────────────────────────────────
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function expireContent(id) {
            if (!confirm('Set request ini menjadi expired secara manual?')) return;
            fetch('/contents/' + id + '/expire', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 1000); }
                else showToast(data.message, 'error');
            });
        }

        function deleteContent(id) {
            if (!confirm('Hapus request ini?')) return;
            fetch('/contents/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 1000); }
                else showToast(data.message, 'error');
            });
        }
    </script>
    @endpush

    </x-slot>
</x-app-layout>
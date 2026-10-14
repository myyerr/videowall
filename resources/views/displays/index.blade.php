<x-app-layout pageTitle="Kelola Display / TV">
    <x-slot name="slot">

    @push('styles')
    <style>
        .display-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px; }

        .display-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: all 0.2s;
        }

        .display-card:hover { border-color: var(--border-accent); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }

        .display-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }

        .display-icon {
            width: 40px; height: 40px;
            background: rgba(99,102,241,0.15);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent-2);
            font-size: 18px;
        }

        .display-status {
            width: 8px; height: 8px;
            border-radius: 50%;
            margin-left: auto;
        }
        .status-active  { background: var(--success); box-shadow: 0 0 8px rgba(16,185,129,0.6); }
        .status-inactive { background: var(--text-muted); }

        .display-card-body { padding: 16px 20px; }
        .display-card-foot { padding: 12px 20px; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; }

        .token-display {
            background: var(--bg-base);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 11px;
            font-family: monospace;
            color: var(--text-muted);
            word-break: break-all;
            cursor: pointer;
            transition: all 0.2s;
        }

        .token-display:hover { border-color: var(--accent); color: var(--text-secondary); }

        /* Page header */
        .page-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 12px;
        }

        .page-header-title {
            font-size: 18px;
            font-weight: 700;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-overlay.active { display: flex; }

        .modal-box {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            width: 560px;
            max-width: 100%;
            padding: 0;
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-body { padding: 20px; overflow-y: auto; }
        .modal-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0; }

        @media (max-width: 640px) {
            .modal-body .grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    @endpush

    <!-- Page header + trigger -->
    <div class="page-header-row">
        <div class="page-header-title">Daftar Display</div>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <i class="fa-solid fa-plus"></i> Tambah Display
        </button>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:16px;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Create Display Modal -->
    <div class="modal-overlay" id="createDisplayModal">
        <div class="modal-box">
            <div class="modal-header">
                <span style="font-size:15px;font-weight:700;">
                    <i class="fa-solid fa-plus" style="color: var(--accent-2);"></i>
                    Tambah Display Baru
                </span>
                <button type="button" onclick="closeCreateModal()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:18px;">✕</button>
            </div>
            <form method="POST" action="{{ route('displays.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="grid grid-cols-3" style="gap: 16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Display *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                   placeholder="Contoh: Videowall Lobby Lt.1" required {{ $errors->any() ? 'autofocus' : '' }}>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                                   placeholder="Contoh: Lobby Gedung A">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeCreateModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah Display
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Display Cards -->
    @if($displays->count())
        <div class="display-cards">
            @foreach($displays as $display)
            <div class="display-card">
                <div class="display-card-header">
                    <div class="display-icon"><i class="fa-solid fa-tv"></i></div>
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">{{ $display->name }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">
                            <i class="fa-solid fa-location-dot fa-xs"></i>
                            {{ $display->location ?: 'Lokasi belum diset' }}
                        </div>
                    </div>
                    <div class="display-status {{ $display->status === 'active' ? 'status-active' : 'status-inactive' }}"
                         title="{{ $display->status }}"></div>
                </div>
                <div class="display-card-body">
                    <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 6px;">Device Token (klik untuk copy):</div>
                    <div class="token-display" onclick="copyToken('{{ $display->device_token }}', this)">
                        {{ $display->device_token }}
                    </div>
                    <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                        <a href="{{ route('display.show', $display->device_token) }}" target="_blank"
                           class="btn btn-primary" style="width:100%;justify-content:center;padding:10px;font-size:13px;box-shadow:0 8px 20px rgba(99,102,241,0.3);">
                            <i class="fa-solid fa-desktop"></i> Buka Layar TV (Single)
                        </a>
                        <button type="button" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;border-color:rgba(99,102,241,0.4);color:var(--accent-2);padding:7px;"
                                onclick="openGridModal('{{ $display->name }}', '{{ route('display.show', $display->device_token) }}')">
                            <i class="fa-solid fa-table-cells"></i> Mode Multi-Monitor / Grid
                        </button>
                    </div>
                </div>
                <div class="display-card-foot">
                    <!-- Edit inline via form -->
                    <form method="POST" action="{{ route('displays.update', $display) }}" style="display:flex;gap:8px;align-items:center;flex:1;">
                        @csrf @method('PUT')
                        <select name="status" class="form-control" style="padding:6px;font-size:12px;flex:1;"
                                onchange="this.form.submit()">
                            <option value="active"   {{ $display->status === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $display->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <input type="hidden" name="name"     value="{{ $display->name }}">
                        <input type="hidden" name="location" value="{{ $display->location }}">
                    </form>

                    <form method="POST" action="{{ route('displays.regenerate-token', $display) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm" title="Regenerate Token" onclick="return confirm('Regenerate token? URL lama tidak akan berfungsi.')">
                            <i class="fa-solid fa-rotate"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('displays.destroy', $display) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Hapus display ini?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:60px;color:var(--text-muted);background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius);">
            <i class="fa-solid fa-tv" style="font-size:48px;opacity:0.2;display:block;margin-bottom:16px;"></i>
            <div>Belum ada display. Tambahkan display pertama di atas.</div>
        </div>
    @endif

    <!-- Multi-Monitor Grid Wall Modal -->
    <div class="modal-overlay" id="gridModalOverlay">
        <div class="modal-box" style="width:680px;">
            <div class="modal-header">
                <div>
                    <span style="font-size:15px;font-weight:700;">
                        <i class="fa-solid fa-table-cells" style="color: var(--accent-2);"></i>
                        Mode Multi-Monitor / E-Banner Matrix
                    </span>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;" id="gridDisplayName"></div>
                </div>
                <button type="button" onclick="closeGridModal()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:18px;">✕</button>
            </div>
            <div class="modal-body" style="padding:20px;max-height:75vh;overflow-y:auto;">
                <div style="font-size:12px;color:var(--text-secondary);margin-bottom:14px;line-height:1.6;background:rgba(99,102,241,0.08);padding:10px 14px;border-radius:var(--radius-sm);border:1px solid rgba(99,102,241,0.2);">
                    <i class="fa-solid fa-circle-info" style="color:var(--accent-2);"></i>
                    <strong>Tanpa Alat Hardware Tambahan:</strong> Buka link tile di masing-masing monitor (misal Smart TV / Browser TV Box). Setiap layar akan otomatis menampilkan bagian yang pas dan tersambung menjadi satu e-banner raksasa.
                </div>

                <div style="display:flex;gap:10px;margin-bottom:16px;">
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid2x2" onclick="switchGridPreset('2x2')">Matrix 2x2 (4 Layar)</button>
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid2x1" onclick="switchGridPreset('2x1')">Banner 2x1 (2 Layar Horizontal)</button>
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid1x2" onclick="switchGridPreset('1x2')">Tower 1x2 (2 Layar Vertikal)</button>
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid3x3" onclick="switchGridPreset('3x3')">Matrix 3x3 (9 Layar)</button>
                </div>

                <div id="gridTileContainer" style="display:flex;flex-direction:column;gap:10px;"></div>
            </div>
            <div class="modal-footer" style="padding:12px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
                <button type="button" class="btn btn-ghost" onclick="closeGridModal()">Tutup</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToken(token, el) {
            navigator.clipboard.writeText(token).then(() => {
                const orig = el.textContent;
                el.textContent = '✓ Tersalin!';
                el.style.color = '#10B981';
                setTimeout(() => { el.textContent = orig; el.style.color = ''; }, 2000);
            });
        }

        function copyLinkText(url, btn) {
            navigator.clipboard.writeText(url).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin';
                btn.style.color = '#10B981';
                setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
            });
        }

        let activeBaseUrl = '';
        let currentPreset = '2x2';

        function openGridModal(name, baseUrl) {
            activeBaseUrl = baseUrl;
            document.getElementById('gridDisplayName').textContent = `Display: ${name}`;
            document.getElementById('gridModalOverlay').classList.add('active');
            switchGridPreset('2x2');
        }

        function closeGridModal() {
            document.getElementById('gridModalOverlay').classList.remove('active');
        }

        function switchGridPreset(preset) {
            currentPreset = preset;
            ['2x2','2x1','1x2','3x3'].forEach(p => {
                const el = document.getElementById('tabGrid' + p);
                if (el) el.className = (p === preset) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-ghost';
            });

            const [cols, rows] = preset.split('x').map(n => parseInt(n, 10));
            const c = document.getElementById('gridTileContainer');
            c.innerHTML = '';

            for (let r = 0; r < rows; r++) {
                for (let cl = 0; cl < cols; cl++) {
                    const tileUrl = `${activeBaseUrl}?grid=${preset}&col=${cl}&row=${r}`;
                    const label   = `Layar [Kolom ${cl+1}, Baris ${r+1}]` + 
                                    (preset==='2x2' ? (r===0&&cl===0 ? ' (Kiri Atas)' : r===0&&cl===1 ? ' (Kanan Atas)' : r===1&&cl===0 ? ' (Kiri Bawah)' : ' (Kanan Bawah)') : '');
                    
                    const div = document.createElement('div');
                    div.style.background = 'var(--bg-base)';
                    div.style.border = '1px solid var(--border)';
                    div.style.borderRadius = 'var(--radius-sm)';
                    div.style.padding = '10px 14px';
                    div.style.display = 'flex';
                    div.style.alignItems = 'center';
                    div.style.justifyContent = 'space-between';
                    div.style.gap = '12px';

                    div.innerHTML = `
                        <div style="flex:1;overflow:hidden;">
                            <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-bottom:2px;">
                                <i class="fa-solid fa-tv" style="color:var(--accent-2);margin-right:4px;"></i> ${label}
                            </div>
                            <div style="font-size:11px;font-family:monospace;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                ${tileUrl}
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0;">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="copyLinkText('${tileUrl}', this)">
                                <i class="fa-regular fa-copy"></i> Copy
                            </button>
                            <a href="${tileUrl}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka
                            </a>
                        </div>
                    `;
                    c.appendChild(div);
                }
            }
        }

        function openCreateModal() {
            document.getElementById('createDisplayModal').classList.add('active');
        }

        function closeCreateModal() {
            document.getElementById('createDisplayModal').classList.remove('active');
        }

        @if($errors->any())
            openCreateModal();
        @endif

        document.getElementById('createDisplayModal').addEventListener('click', function(e) {
            if (e.target === this) closeCreateModal();
        });
        document.getElementById('gridModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeGridModal();
        });
    </script>
    @endpush

    </x-slot>
</x-app-layout>
<x-app-layout pageTitle="Kelola Display / TV">
    <x-slot name="slot">

    @push('styles')
    <style>
        .page-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }
        .page-header-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-1);
        }

        .display-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .display-card {
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: border-color var(--speed);
        }
        .display-card:hover { border-color: var(--border-2); }

        .display-card-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .display-icon {
            width: 36px; height: 36px;
            background: var(--accent-dim);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
            font-size: 15px;
            flex-shrink: 0;
        }

        .display-status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            margin-left: auto;
            flex-shrink: 0;
        }
        .status-active  { background: var(--success); }
        .status-inactive { background: var(--text-3); }

        .display-card-body { padding: 16px; flex: 1; }
        .display-card-foot {
            padding: 10px 16px;
            border-top: 1px solid var(--border-1);
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            background: var(--bg-sidebar);
            align-items: center;
        }

        .token-display {
            background: var(--bg-input);
            border: 1px solid var(--border-1);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            font-size: 11px;
            font-family: monospace;
            color: var(--text-2);
            word-break: break-all;
            cursor: pointer;
            transition: all var(--speed);
        }
        .token-display:hover { border-color: var(--accent); color: var(--text-1); }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            z-index: 999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-overlay.active { display: flex; }

        .modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: var(--radius);
            width: 520px;
            max-width: 100%;
            overflow: hidden;
            box-shadow: 0 16px 36px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title { font-size: 14px; font-weight: 600; color: var(--text-1); }

        .modal-body { padding: 18px; }
        .modal-footer {
            padding: 12px 18px;
            border-top: 1px solid var(--border-1);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            background: var(--bg-sidebar);
        }

        .grid-helper-box {
            font-size: 12px;
            color: var(--text-2);
            margin-bottom: 14px;
            line-height: 1.5;
            background: var(--accent-dim);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(59,130,246,0.25);
        }

        @media (max-width: 640px) {
            .display-cards { grid-template-columns: 1fr; }
        }
    </style>
    @endpush

    <!-- Page header + trigger -->
    <div class="page-header-row">
        <div class="page-header-title">Daftar Display & Videowall</div>
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
                <span class="modal-title">
                    <i class="fa-solid fa-plus" style="color:var(--accent);margin-right:4px;"></i>
                    Tambah Display Baru
                </span>
                <button type="button" onclick="closeCreateModal()" style="background:none;border:none;color:var(--text-3);cursor:pointer;font-size:16px;">✕</button>
            </div>
            <form method="POST" action="{{ route('displays.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Display *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                               placeholder="Contoh: Videowall Lobby Utama" required {{ $errors->any() ? 'autofocus' : '' }}>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                               placeholder="Contoh: Gedung Rektorat Lt. 1">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active (Dapat Menayangkan)</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeCreateModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Simpan Display
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
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight: 600; font-size: 14px; color: var(--text-1); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $display->name }}
                        </div>
                        <div style="font-size: 11px; color: var(--text-3); margin-top: 1px;">
                            <i class="fa-solid fa-location-dot fa-xs"></i>
                            {{ $display->location ?: 'Lokasi belum diset' }}
                        </div>
                    </div>
                    <div class="display-status-dot {{ $display->status === 'active' ? 'status-active' : 'status-inactive' }}"
                         title="{{ ucfirst($display->status) }}"></div>
                </div>
                <div class="display-card-body">
                    <div style="font-size: 11px; color: var(--text-3); margin-bottom: 6px;">Device Token:</div>
                    <div class="token-display" onclick="copyToken('{{ $display->device_token }}', this)" title="Klik untuk copy token">
                        {{ $display->device_token }}
                    </div>
                    <div style="margin-top: 14px; display: flex; flex-direction: column; gap: 8px;">
                        <a href="{{ route('display.show', $display->device_token) }}" target="_blank"
                           class="btn btn-primary" style="width:100%;justify-content:center;">
                            <i class="fa-solid fa-desktop"></i> Buka Layar TV (Single)
                        </a>
                        <button type="button" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;"
                                onclick="openGridModal('{{ $display->name }}', '{{ route('display.show', $display->device_token) }}')">
                            <i class="fa-solid fa-table-cells"></i> Mode Multi-Monitor / Grid
                        </button>
                    </div>
                </div>
                <div class="display-card-foot">
                    <!-- Inline status toggle -->
                    <form method="POST" action="{{ route('displays.update', $display) }}" style="display:flex;gap:8px;align-items:center;flex:1;">
                        @csrf @method('PUT')
                        <select name="status" class="form-control" style="padding:4px 8px;font-size:12px;height:30px;"
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
                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Display" onclick="return confirm('Hapus display ini?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card" style="text-align:center;padding:50px 20px;color:var(--text-3);">
            <i class="fa-solid fa-tv" style="font-size:36px;opacity:0.25;display:block;margin-bottom:12px;"></i>
            <div style="font-size:14px;font-weight:500;margin-bottom:10px;">Belum ada display terdaftar</div>
            <button class="btn btn-primary btn-sm" onclick="openCreateModal()">
                <i class="fa-solid fa-plus"></i> Tambah Display Pertama
            </button>
        </div>
    @endif

    <!-- Multi-Monitor Grid Wall Modal -->
    <div class="modal-overlay" id="gridModalOverlay">
        <div class="modal-box" style="width:680px;">
            <div class="modal-header">
                <div>
                    <span class="modal-title">
                        <i class="fa-solid fa-table-cells" style="color:var(--accent);margin-right:6px;"></i>
                        Mode Multi-Monitor / Grid Matrix
                    </span>
                    <div style="font-size:11px;color:var(--text-3);margin-top:2px;" id="gridDisplayName"></div>
                </div>
                <button type="button" onclick="closeGridModal()" style="background:none;border:none;color:var(--text-3);cursor:pointer;font-size:16px;">✕</button>
            </div>
            <div class="modal-body" style="max-height:75vh;overflow-y:auto;">
                <div class="grid-helper-box">
                    <i class="fa-solid fa-circle-info" style="color:var(--accent);margin-right:4px;"></i>
                    <strong>Tanpa Hardware Splitter Khusus:</strong> Buka tautan tile di masing-masing Smart TV atau mini PC browser. Semua layar akan otomatis menampilkan potongan presisi dan sinkron menjadi satu banner besar.
                </div>

                <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid2x2" onclick="switchGridPreset('2x2')">Matrix 2x2 (4 Layar)</button>
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid2x1" onclick="switchGridPreset('2x1')">Banner 2x1 (2 Horisontal)</button>
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid1x2" onclick="switchGridPreset('1x2')">Tower 1x2 (2 Vertikal)</button>
                    <button type="button" class="btn btn-sm btn-ghost" id="tabGrid3x3" onclick="switchGridPreset('3x3')">Matrix 3x3 (9 Layar)</button>
                </div>

                <div id="gridTileContainer" style="display:flex;flex-direction:column;gap:8px;"></div>
            </div>
            <div class="modal-footer">
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
                el.style.color = 'var(--success)';
                setTimeout(() => { el.textContent = orig; el.style.color = ''; }, 2000);
            });
        }

        function copyLinkText(url, btn) {
            navigator.clipboard.writeText(url).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin';
                btn.style.color = 'var(--success)';
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
                    div.style.background = 'var(--bg-input)';
                    div.style.border = '1px solid var(--border-1)';
                    div.style.borderRadius = 'var(--radius-sm)';
                    div.style.padding = '8px 12px';
                    div.style.display = 'flex';
                    div.style.alignItems = 'center';
                    div.style.justifyContent = 'space-between';
                    div.style.gap = '10px';

                    div.innerHTML = `
                        <div style="flex:1;overflow:hidden;min-width:0;">
                            <div style="font-size:12px;font-weight:600;color:var(--text-1);margin-bottom:2px;">
                                <i class="fa-solid fa-tv" style="color:var(--accent);margin-right:4px;"></i> ${label}
                            </div>
                            <div style="font-size:11px;font-family:monospace;color:var(--text-3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                ${tileUrl}
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0;">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="copyLinkText('${tileUrl}', this)">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                            <a href="${tileUrl}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
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
<x-app-layout pageTitle="Asset Manager">
    <x-slot name="slot">

    @push('styles')
    <style>
        .asset-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        /* Type tabs */
        .type-tabs-scroll {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            background: var(--bg-card);
            padding: 4px;
            border-radius: var(--radius);
            border: 1px solid var(--border-1);
        }
        .type-tabs-scroll::-webkit-scrollbar { display: none; }

        .type-tab {
            padding: 7px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--text-2);
            transition: all var(--speed);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .type-tab:hover { color: var(--text-1); background: var(--bg-hover); }
        .type-tab.active {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
        }

        .asset-section { display: none; margin-bottom: 24px; }
        .asset-section.active { display: block; }

        .asset-grid-display {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
        }

        .asset-card {
            background: var(--bg-card);
            border: 1px solid var(--border-1);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: border-color var(--speed);
        }
        .asset-card:hover { border-color: var(--border-2); }

        .asset-preview {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            background: #000;
            display: block;
        }
        .asset-preview.logo-preview {
            aspect-ratio: 1;
            object-fit: contain;
            padding: 16px;
            background: var(--bg-input);
        }

        .asset-info {
            padding: 10px 12px;
            border-top: 1px solid var(--border-1);
            flex: 1;
        }
        .asset-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-1);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .asset-meta {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .asset-actions {
            padding: 8px 12px;
            border-top: 1px solid var(--border-1);
            display: flex;
            justify-content: flex-end;
            background: rgba(0,0,0,0.1);
        }

        /* Upload modal */
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
            width: 460px;
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

        .drop-zone {
            border: 1px dashed var(--border-2);
            border-radius: var(--radius-sm);
            padding: 30px 16px;
            text-align: center;
            cursor: pointer;
            transition: all var(--speed);
            color: var(--text-3);
            background: var(--bg-input);
            margin-top: 6px;
        }
        .drop-zone:hover, .drop-zone.over {
            border-color: var(--accent);
            background: var(--accent-dim);
            color: var(--text-1);
        }
        .preview-filename {
            font-size: 12px;
            font-weight: 500;
            color: var(--accent);
            margin-top: 8px;
            word-break: break-all;
        }

        @media (max-width: 640px) {
            .asset-header-row { flex-direction: column; align-items: stretch; }
            .type-tabs-scroll { width: 100%; }
            .asset-grid-display { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        }
    </style>
    @endpush

    <div class="asset-header-row">
        <div class="type-tabs-scroll">
            <button class="type-tab active" onclick="switchTab('logo')">
                <i class="fa-solid fa-star"></i> Logo ({{ $logos->count() }})
            </button>
            <button class="type-tab" onclick="switchTab('video')">
                <i class="fa-solid fa-film"></i> Video BG ({{ $videos->count() }})
            </button>
            <button class="type-tab" onclick="switchTab('image')">
                <i class="fa-solid fa-image"></i> Gambar BG ({{ $images->count() }})
            </button>
        </div>
        <button class="btn btn-primary" onclick="openUploadModal()">
            <i class="fa-solid fa-cloud-arrow-up"></i> Upload Asset
        </button>
    </div>

    <!-- Logo section -->
    <div class="asset-section active" id="section-logo">
        @if($logos->count())
            <div class="asset-grid-display">
                @foreach($logos as $asset)
                <div class="asset-card">
                    <img src="{{ asset('storage/' . $asset->file_path) }}"
                         class="asset-preview logo-preview" alt="{{ $asset->name }}">
                    <div class="asset-info">
                        <div class="asset-name">{{ $asset->name }}</div>
                        <div class="asset-meta">{{ $asset->formatted_size ?? '—' }}</div>
                    </div>
                    <div class="asset-actions">
                        <button class="btn btn-danger btn-sm" onclick="deleteAsset({{ $asset->id }}, '{{ addslashes($asset->name) }}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align:center;padding:48px 20px;color:var(--text-3);">
                <i class="fa-solid fa-star" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                <div style="font-size:13px;margin-bottom:12px;">Belum ada logo terdaftar.</div>
                <button class="btn btn-ghost btn-sm" onclick="openUploadModal('logo')" style="display:inline-flex;">
                    <i class="fa-solid fa-plus"></i> Upload Logo Pertama
                </button>
            </div>
        @endif
    </div>

    <!-- Video section -->
    <div class="asset-section" id="section-video">
        @if($videos->count())
            <div class="asset-grid-display">
                @foreach($videos as $asset)
                <div class="asset-card">
                    <video class="asset-preview" muted preload="metadata"
                           onmouseenter="this.play()" onmouseleave="this.pause(); this.currentTime=0;">
                        <source src="{{ asset('storage/' . $asset->file_path) }}">
                    </video>
                    <div class="asset-info">
                        <div class="asset-name">{{ $asset->name }}</div>
                        <div class="asset-meta">{{ $asset->formatted_size ?? '—' }}</div>
                    </div>
                    <div class="asset-actions">
                        <button class="btn btn-danger btn-sm" onclick="deleteAsset({{ $asset->id }}, '{{ addslashes($asset->name) }}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align:center;padding:48px 20px;color:var(--text-3);">
                <i class="fa-solid fa-film" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                <div style="font-size:13px;margin-bottom:12px;">Belum ada video background.</div>
                <button class="btn btn-ghost btn-sm" onclick="openUploadModal('video_background')" style="display:inline-flex;">
                    <i class="fa-solid fa-plus"></i> Upload Video
                </button>
            </div>
        @endif
    </div>

    <!-- Image section -->
    <div class="asset-section" id="section-image">
        @if($images->count())
            <div class="asset-grid-display">
                @foreach($images as $asset)
                <div class="asset-card">
                    <img src="{{ asset('storage/' . $asset->file_path) }}"
                         class="asset-preview" alt="{{ $asset->name }}">
                    <div class="asset-info">
                        <div class="asset-name">{{ $asset->name }}</div>
                        <div class="asset-meta">{{ $asset->formatted_size ?? '—' }}</div>
                    </div>
                    <div class="asset-actions">
                        <button class="btn btn-danger btn-sm" onclick="deleteAsset({{ $asset->id }}, '{{ addslashes($asset->name) }}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align:center;padding:48px 20px;color:var(--text-3);">
                <i class="fa-solid fa-image" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                <div style="font-size:13px;margin-bottom:12px;">Belum ada gambar background.</div>
                <button class="btn btn-ghost btn-sm" onclick="openUploadModal('image_background')" style="display:inline-flex;">
                    <i class="fa-solid fa-plus"></i> Upload Gambar
                </button>
            </div>
        @endif
    </div>

    <!-- Upload Modal -->
    <div class="modal-overlay" id="uploadModal">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">Upload Asset</span>
                <button onclick="closeUploadModal()" style="background:none;border:none;color:var(--text-3);cursor:pointer;font-size:16px;">✕</button>
            </div>
            <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Asset *</label>
                        <input type="text" name="name" id="assetName" class="form-control" required placeholder="Contoh: Logo Kampus">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Asset *</label>
                        <select name="type" id="assetType" class="form-control" onchange="updateAccept()">
                            <option value="logo">Logo (PNG/JPG/SVG/WebP)</option>
                            <option value="image_background">Gambar Background (JPG/PNG/WebP)</option>
                            <option value="video_background">Video Background (MP4/WebM)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">File *</label>
                        <div class="drop-zone" id="dropZone" onclick="document.getElementById('assetFile').click()">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                            <div>Klik atau drag & drop file ke sini</div>
                            <div id="selectedFileName" class="preview-filename"></div>
                        </div>
                        <input type="file" id="assetFile" name="file" style="display:none;" required onchange="onFileSelected(this)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeUploadModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Tabs
        let currentTab = 'logo';
        function switchTab(type) {
            currentTab = type;
            document.querySelectorAll('.asset-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.type-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('section-' + type).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Upload Modal
        function openUploadModal(type) {
            if (type) document.getElementById('assetType').value = type;
            updateAccept();
            document.getElementById('uploadModal').classList.add('active');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.remove('active');
            document.getElementById('selectedFileName').textContent = '';
            document.getElementById('assetName').value = '';
        }

        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) closeUploadModal();
        });

        function onFileSelected(input) {
            const file = input.files[0];
            if (file) {
                document.getElementById('selectedFileName').textContent = file.name;
                if (!document.getElementById('assetName').value) {
                    document.getElementById('assetName').value = file.name.replace(/\.[^.]+$/, '');
                }
            }
        }

        function updateAccept() {
            const type = document.getElementById('assetType').value;
            const input = document.getElementById('assetFile');
            if (type === 'video_background') input.accept = 'video/mp4,video/webm,video/mov';
            else if (type === 'image_background') input.accept = 'image/jpeg,image/png,image/webp';
            else input.accept = 'image/png,image/jpeg,image/webp,image/svg+xml';
        }

        // Drag & drop
        const dz = document.getElementById('dropZone');
        ['dragenter','dragover'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.classList.add('over'); }));
        ['dragleave','drop'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.classList.remove('over'); }));
        dz.addEventListener('drop', ev => {
            const file = ev.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('assetFile').files = dt.files;
                onFileSelected(document.getElementById('assetFile'));
            }
        });

        // Delete
        function deleteAsset(id, name) {
            if (!confirm(`Hapus asset "${name}"? Pastikan tidak sedang digunakan.`)) return;
            fetch('/assets/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 600);
                } else showToast(data.message, 'error');
            });
        }
    </script>
    @endpush

    </x-slot>
</x-app-layout>
<x-app-layout pageTitle="Asset Manager">
    <x-slot name="slot">

    @push('styles')
    <style>
        .asset-section { margin-bottom: 32px; }

        .asset-section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .asset-grid-display {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 14px;
        }

        @media (max-width: 640px) {
            .asset-grid-display {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }

        @media (max-width: 380px) {
            .asset-grid-display {
                grid-template-columns: 1fr;
            }
        }

        .asset-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: all 0.2s;
        }

        .asset-card:hover {
            border-color: var(--border-accent);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .asset-preview {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            background: var(--bg-base);
            display: block;
        }

        .asset-preview.logo-preview {
            aspect-ratio: 1;
            object-fit: contain;
            padding: 16px;
        }

        .asset-info {
            padding: 10px 12px;
            border-top: 1px solid var(--border);
        }

        .asset-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .asset-meta {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .asset-actions {
            padding: 8px 12px;
            display: flex;
            justify-content: flex-end;
        }

        /* Upload modal */
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
            width: 480px;
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

        .drop-zone {
            border: 2px dashed var(--border);
            border-radius: var(--radius-sm);
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .drop-zone:hover, .drop-zone.over {
            border-color: var(--accent);
            background: var(--accent-glow);
            color: var(--text-primary);
        }

        .preview-filename {
            font-size: 12px;
            color: var(--accent-2);
            margin-top: 8px;
        }

        @media (max-width: 480px) {
            .drop-zone {
                padding: 28px 16px;
            }

            .modal-footer {
                flex-wrap: wrap;
            }

            .modal-footer .btn {
                flex: 1 1 auto;
                justify-content: center;
            }
        }

        /* Type tabs */
        .type-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }

        .type-tab {
            padding: 8px 20px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--bg-elevated);
            color: var(--text-secondary);
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .type-tab.active {
            background: var(--accent-glow);
            border-color: var(--border-accent);
            color: var(--accent-2);
        }

        .asset-section { display: none; }
        .asset-section.active { display: block; }

        /* Header row: tabs + upload button */
        .asset-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 24px;
        }

        .type-tabs-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            flex: 1;
            min-width: 0;
        }

        .type-tabs-scroll::-webkit-scrollbar { display: none; }

        .asset-header-row .btn-primary {
            flex-shrink: 0;
        }

        @media (max-width: 640px) {
            .asset-header-row {
                flex-wrap: wrap;
            }

            .type-tabs-scroll {
                flex: 1 1 100%;
                order: 2;
            }

            .asset-header-row .btn-primary {
                flex: 1 1 100%;
                order: 1;
                justify-content: center;
                padding: 12px;
            }

            .type-tab {
                padding: 8px 14px;
                font-size: 12px;
            }

            .asset-name {
                font-size: 11px;
            }

            .asset-meta {
                font-size: 9px;
            }
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
            <div style="text-align:center;padding:60px;color:var(--text-muted);">
                <i class="fa-solid fa-star" style="font-size:40px;opacity:0.2;display:block;margin-bottom:12px;"></i>
                Belum ada logo. <button class="btn btn-ghost btn-sm" onclick="openUploadModal('logo')" style="display:inline-flex;">Upload sekarang</button>
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
            <div style="text-align:center;padding:60px;color:var(--text-muted);">
                <i class="fa-solid fa-film" style="font-size:40px;opacity:0.2;display:block;margin-bottom:12px;"></i>
                Belum ada video background.
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
            <div style="text-align:center;padding:60px;color:var(--text-muted);">
                <i class="fa-solid fa-image" style="font-size:40px;opacity:0.2;display:block;margin-bottom:12px;"></i>
                Belum ada gambar background.
            </div>
        @endif
    </div>

    <!-- Upload Modal -->
    <div class="modal-overlay" id="uploadModal">
        <div class="modal-box">
            <div class="modal-header">
                <span style="font-size:15px;font-weight:700;">Upload Asset</span>
                <button onclick="closeUploadModal()" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:18px;">✕</button>
            </div>
            <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Asset *</label>
                        <input type="text" name="name" id="assetName" class="form-control" required placeholder="Nama deskriptif">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe *</label>
                        <select name="type" id="assetType" class="form-control" onchange="updateAccept()">
                            <option value="logo">Logo (PNG/JPG/SVG/WebP)</option>
                            <option value="image_background">Gambar Background (JPG/PNG/WebP)</option>
                            <option value="video_background">Video Background (MP4/WebM)</option>
                        </select>
                    </div>
                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('assetFile').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:32px;margin-bottom:10px;"></i>
                        <div>Klik atau drag & drop file di sini</div>
                        <div id="selectedFileName" class="preview-filename"></div>
                    </div>
                    <input type="file" id="assetFile" name="file" style="display:none;" required onchange="onFileSelected(this)">
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
            event.target.classList.add('active');
        }

        // Upload Modal
        function openUploadModal(type) {
            if (type) document.getElementById('assetType').value = type;
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
                    setTimeout(() => location.reload(), 800);
                } else showToast(data.message, 'error');
            });
        }
    </script>
    @endpush

    </x-slot>
</x-app-layout>
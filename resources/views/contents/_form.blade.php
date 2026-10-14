@push('styles')
<style>
    /* ── WIZARD ──────────────────────────────────────────── */
    .wizard-steps { display:flex; align-items:center; gap:0; margin-bottom:28px; }
    .step-item { display:flex; align-items:center; gap:10px; flex:1; }
    .step-num { width:32px; height:32px; border-radius:50%; background:var(--bg-elevated); border:2px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:var(--text-muted); flex-shrink:0; transition:all 0.3s; }
    .step-label { font-size:13px; font-weight:600; color:var(--text-muted); transition:color 0.3s; }
    .step-line { flex:1; height:2px; background:var(--border); margin:0 8px; max-width:60px; }
    .step-item.active .step-num { background:var(--accent); border-color:var(--accent); color:#fff; }
    .step-item.active .step-label { color:var(--text-primary); }
    .step-item.done .step-num { background:var(--success); border-color:var(--success); color:#fff; }
    .step-item.done .step-label { color:var(--success); }
    .step-panel { display:none; }
    .step-panel.active { display:block; animation:fadeInPanel 0.3s ease; }
    @keyframes fadeInPanel { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

    /* ── LAYOUT ──────────────────────────────────────────── */
    .create-grid { display:grid; grid-template-columns:1fr 400px; gap:20px; }
    @media(max-width:1100px) { .create-grid { grid-template-columns:1fr; } }

    /* ── ASSET PICKER ────────────────────────────────────── */
    .asset-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(90px,1fr)); gap:10px; max-height:220px; overflow-y:auto; padding:4px; }
    .asset-item { border:2px solid var(--border); border-radius:var(--radius-sm); padding:8px; cursor:pointer; text-align:center; transition:all 0.2s; background:var(--bg-elevated); position:relative; }
    .asset-item:hover { border-color:var(--accent); }
    .asset-item.selected { border-color:var(--accent); background:var(--accent-glow); }
    .asset-item.selected::after { content:'✓'; position:absolute; top:4px; right:6px; font-size:12px; font-weight:700; color:var(--accent); }
    .asset-thumb { width:100%; aspect-ratio:1; object-fit:contain; border-radius:4px; }
    .asset-name { font-size:10px; color:var(--text-secondary); margin-top:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    /* ── CONTENT ROWS ────────────────────────────────────── */
    .content-row { display:flex; gap:10px; align-items:flex-start; background:var(--bg-elevated); border:1px solid var(--border); border-radius:var(--radius-sm); padding:12px; margin-bottom:8px; transition:border-color 0.2s; }
    .content-row:hover { border-color:var(--border-accent); }
    .row-num { width:24px; height:24px; background:var(--accent-glow); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--accent-2); flex-shrink:0; margin-top:8px; }

    /* ── TV PREVIEW ──────────────────────────────────────── */
    .preview-sticky { position:sticky; top:calc(var(--header-h) + 16px); }
    .tv-container { position:relative; width:100%; aspect-ratio:16/9; background:#000; border:3px solid #1E293B; border-radius:10px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.6); }
    .tv-bg { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:1; }
    .tv-content-layer { position:absolute; inset:0; z-index:3; }
    .tv-standby { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; z-index:2; transition:opacity 0.3s; }
    .tv-standby-text { font-size:3vw; color:rgba(255,255,255,0.12); letter-spacing:6px; font-weight:800; text-transform:uppercase; }
    @media(max-width:768px) { .tv-standby-text { font-size:5vw; } }

    /* ── FULLSCREEN EDITOR ───────────────────────────────── */
    #fsEditor { display:none; position:fixed; inset:0; width:100vw; height:100vh; background:#000; z-index:99999; overflow:hidden; }
    #fsEditor.visible { display:block; }
    
    #fsContainer {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100vw;
        height: 100vh;
        max-width: calc(100vh * 16 / 9);
        max-height: calc(100vw * 9 / 16);
        container-type: inline-size;
        container-name: fsc;
        overflow: hidden;
    }
    
    .fs-bg { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:1; }
    #fsCanvas { position:absolute; inset:0; z-index:3; touch-action:none; }

    .fs-toolbar { position:absolute; bottom:16px; left:50%; transform:translateX(-50%); z-index:99999; background:rgba(0,0,0,0.88); border:1px solid rgba(255,255,255,0.15); border-radius:12px; padding:10px 20px; display:flex; align-items:center; gap:14px; white-space:nowrap; backdrop-filter:blur(10px); }
    @media(max-width:640px) {
        .fs-toolbar { gap:8px; padding:8px 12px; font-size:11px; bottom:8px; }
        .fs-hint { display:none; }
    }
    .fs-hint { color:#888; font-size:11px; }

    /* Draggable elements — NO outline (selection shown by #fsResizeHandles ONLY) */
    .drag-item { position:absolute; transform:translate(-50%,-50%); font-weight:800; white-space:nowrap; text-align:center; line-height:1.3; cursor:grab; user-select:none; text-shadow:0 0 10px rgba(0,0,0,1),2px 2px 8px rgba(0,0,0,0.9); }
    .drag-item:active { cursor:grabbing; }
    .drag-item.type-logo { text-shadow:none; }

    /* 4-corner resize handles overlay — fixed on screen so no z-index fighting */
    #fsResizeHandles {
        position:fixed; display:none; z-index:999999;
        border:2px solid rgba(99,102,241,0.85);
        pointer-events:none;
        border-radius:2px;
    }
    .fs-corner-handle {
        position:absolute; width:12px; height:12px;
        background:#fff; border:2px solid #6366F1; border-radius:2px;
        pointer-events:all;
    }
    .fs-corner-handle.nw { top:-6px; left:-6px; cursor:nw-resize; }
    .fs-corner-handle.ne { top:-6px; right:-6px; cursor:ne-resize; }
    .fs-corner-handle.sw { bottom:-6px; left:-6px; cursor:sw-resize; }
    .fs-corner-handle.se { bottom:-6px; right:-6px; cursor:se-resize; }

    /* Snap lines */
    .snap-line { position:absolute; pointer-events:none; z-index:99; display:none; background:rgba(0,220,255,0.85); }
    #snapV { top:0; left:50%; width:2px; height:100%; transform:translateX(-50%); }
    #snapH { left:0; top:50%; height:2px; width:100%; transform:translateY(-50%); }

    /* Grid */
    #fsGrid { position:absolute; inset:0; pointer-events:none; z-index:98; display:none; }
    #fsGrid.show { display:block; }
    .fsg-v { position:absolute; top:0; height:100%; width:2px; background:rgba(255,255,255,0.4); box-shadow:0 0 4px rgba(0,0,0,0.5); }
    .fsg-h { position:absolute; left:0; width:100%; height:2px; background:rgba(255,255,255,0.4); box-shadow:0 0 4px rgba(0,0,0,0.5); }

    /* Resize bar — fixed so it's always visible above editor */
    #resizeBar { position:fixed; bottom:80px; left:50%; transform:translateX(-50%); z-index:999999; background:rgba(0,0,0,0.88); border:1px solid rgba(255,255,255,0.15); border-radius:10px; padding:8px 18px; display:none; align-items:center; gap:12px; color:#fff; font-size:13px; backdrop-filter:blur(10px); white-space:nowrap; }
    #resizeBar.visible { display:flex; }
    .resize-btn { background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; border-radius:6px; width:28px; height:28px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .resize-btn:hover { background:rgba(255,255,255,0.25); }
    @media(max-width:640px) {
        #resizeBar { bottom:70px; padding:6px 12px; gap:8px; font-size:12px; }
    }

    /* Upload zone */
    .upload-zone { border:2px dashed var(--border); border-radius:var(--radius-sm); padding:20px; text-align:center; cursor:pointer; transition:all 0.2s; color:var(--text-muted); font-size:13px; }
    .upload-zone:hover, .upload-zone.dragover { border-color:var(--accent); background:var(--accent-glow); color:var(--text-primary); }
    .bg-preview-thumb { width:100%; height:80px; object-fit:cover; border-radius:var(--radius-sm); border:1px solid var(--border); margin-top:8px; }

    /* Logo chips */
    .logo-chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
    .logo-chip { display:inline-flex; align-items:center; gap:6px; background:var(--accent-glow); border:1px solid var(--border-accent); border-radius:20px; padding:4px 10px; font-size:11px; color:var(--accent-2); }
    .logo-chip img { width:18px; height:18px; object-fit:contain; }
    .logo-chip .remove-chip { cursor:pointer; opacity:0.6; }
    .logo-chip .remove-chip:hover { opacity:1; }

    /* bg type tab */
    .bg-type-btn.active { background:var(--accent) !important; color:#fff !important; border-color:var(--accent) !important; }

    /* Mobile form improvements */
    @media(max-width:768px) {
        .wizard-steps { margin-bottom:16px; }
        .step-label { font-size:11px; }
        .create-grid { grid-template-columns:1fr; gap:12px; }
        .asset-grid { grid-template-columns:repeat(auto-fill,minmax(72px,1fr)); max-height:180px; }
        .preview-sticky { position:static; }
    }
</style>
@endpush

<!-- Wizard Steps -->
<div class="wizard-steps">
    <div class="step-item active" id="step-indicator-1">
        <div class="step-num">1</div>
        <div class="step-label">Jadwal & Display</div>
    </div>
    <div class="step-line"></div>
    <div class="step-item" id="step-indicator-2">
        <div class="step-num">2</div>
        <div class="step-label">Background & Logo</div>
    </div>
    <div class="step-line"></div>
    <div class="step-item" id="step-indicator-3">
        <div class="step-num">3</div>
        <div class="step-label">Konten & Layout</div>
    </div>
</div>

<form id="mainForm" method="POST"
      action="{{ isset($content) ? route('contents.update', $content) : route('contents.store') }}">
    @csrf
    @if(isset($content)) @method('PUT') @endif
    <input type="hidden" name="custom_elements_json" id="customElementsJson">

    <!-- ── STEP 1 ──────────────────────────────────────────── -->
    <div class="step-panel active" id="panel-1">
        <div class="card" style="max-width:640px;">
            <div class="card-header">
                <i class="fa-solid fa-calendar" style="color:var(--accent-2);"></i>
                <span class="card-title">Info Jadwal</span>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i><div>{{ $errors->first() }}</div></div>
                @endif

                <div class="form-group">
                    <label class="form-label">Display / TV Tujuan *</label>
                    <select name="display_id" id="displayId" class="form-control" required>
                        <option value="">— Pilih Display —</option>
                        @foreach($displays as $d)
                            <option value="{{ $d->id }}" {{ (old('display_id', isset($content) ? $content->display_id : '') == $d->id) ? 'selected' : '' }}>
                                {{ $d->name }}{{ $d->location ? ' ('.$d->location.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Judul Request *</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title', isset($content) ? $content->title : '') }}"
                           placeholder="Contoh: Sosialisasi Safety Bulan Juni" required>
                </div>

                <div class="grid grid-cols-3" style="gap:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Tanggal Tayang *</label>
                        <input type="date" name="display_date" id="displayDate" class="form-control"
                               value="{{ old('display_date', isset($content) ? $content->display_date?->format('Y-m-d') : '') }}"
                               required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Jam Mulai *</label>
                        <input type="time" name="start_time" id="startTime" class="form-control"
                               value="{{ old('start_time', isset($content) ? substr($content->start_time, 0, 5) : '') }}"
                               required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Jam Selesai *</label>
                        <input type="time" name="end_time" id="endTime" class="form-control"
                               value="{{ old('end_time', isset($content) ? substr($content->end_time, 0, 5) : '') }}"
                               required>
                    </div>
                </div>

                <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:10px;">
                    <a href="{{ route('contents.index') }}" class="btn btn-ghost">Batal</a>
                    <button type="button" class="btn btn-primary" onclick="goToStep(2)">
                        Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── STEP 2: Background & Logo ───────────────────────── -->
    <div class="step-panel" id="panel-2">
        <div class="create-grid">
            <div>
                <!-- Background Card -->
                <div class="card" style="margin-bottom:16px;">
                    <div class="card-header">
                        <i class="fa-solid fa-image" style="color:var(--accent-2);"></i>
                        <span class="card-title">Background</span>
                        <span style="font-size:11px;color:var(--text-muted);margin-left:auto;">Video atau Gambar</span>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom:12px;">
                            <div style="display:flex;gap:8px;margin-bottom:10px;">
                                <button type="button" class="btn btn-ghost btn-sm bg-type-btn active" data-type="video" onclick="switchBgTab('video')">
                                    <i class="fa-solid fa-film"></i> Video
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm bg-type-btn" data-type="image" onclick="switchBgTab('image')">
                                    <i class="fa-solid fa-image"></i> Gambar
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm" onclick="clearBg()" style="margin-left:auto;">
                                    <i class="fa-solid fa-xmark"></i> Hapus BG
                                </button>
                            </div>

                            <div id="bgVideoPanel">
                                <div class="asset-grid" id="videoAssetGrid">
                                    <div class="asset-item" data-url="" data-type="none" onclick="selectBg(this)">
                                        <div style="width:100%;aspect-ratio:1;display:flex;align-items:center;justify-content:center;background:var(--bg-base);border-radius:4px;color:var(--text-muted);font-size:10px;">Hitam</div>
                                        <div class="asset-name">Tanpa BG</div>
                                    </div>
                                    @foreach($videos as $v)
                                    <div class="asset-item" data-url="{{ asset('storage/'.$v->file_path) }}" data-type="video" onclick="selectBg(this)">
                                        <video style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:4px;" muted preload="metadata">
                                            <source src="{{ asset('storage/'.$v->file_path) }}">
                                        </video>
                                        <div class="asset-name">{{ $v->name }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div id="bgImagePanel" style="display:none;">
                                <div class="asset-grid" id="imageAssetGrid">
                                    @foreach($images as $img)
                                    <div class="asset-item" data-url="{{ asset('storage/'.$img->file_path) }}" data-type="image" onclick="selectBg(this)">
                                        <img src="{{ asset('storage/'.$img->file_path) }}" class="asset-thumb" alt="">
                                        <div class="asset-name">{{ $img->name }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="upload-zone" id="bgDropZone" onclick="document.getElementById('bgFileInput').click()">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size:24px;margin-bottom:8px;"></i>
                            <div>Upload background baru</div>
                            <div style="font-size:11px;margin-top:4px;">Klik atau drag & drop</div>
                        </div>
                        <input type="file" id="bgFileInput" accept="video/mp4,video/webm,image/jpeg,image/png,image/webp" style="display:none;">
                        <div id="bgUploadProgress" style="display:none;margin-top:8px;font-size:12px;color:var(--text-secondary);">
                            <i class="fa-solid fa-spinner fa-spin"></i> Mengupload...
                        </div>

                    </div>
                </div>

                <!-- Logo Card -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-star" style="color:var(--accent-2);"></i>
                        <span class="card-title">Logo</span>
                        <span style="font-size:11px;color:var(--text-muted);margin-left:auto;">Bisa pilih lebih dari satu</span>
                    </div>
                    <div class="card-body">
                        <div class="asset-grid" id="logoAssetGrid">
                            @foreach($logos as $logo)
                            <div class="asset-item logo-selectable"
                                 data-id="{{ $logo->id }}"
                                 data-url="{{ asset('storage/'.$logo->file_path) }}"
                                 data-name="{{ $logo->name }}"
                                 onclick="toggleLogo(this)">
                                <img src="{{ asset('storage/'.$logo->file_path) }}" class="asset-thumb" alt="{{ $logo->name }}">
                                <div class="asset-name">{{ $logo->name }}</div>
                            </div>
                            @endforeach
                        </div>

                        <div style="margin-top:12px;">
                            <div style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;">Logo terpilih:</div>
                            <div class="logo-chips" id="selectedLogoChips">
                                <span style="font-size:11px;color:var(--text-muted);font-style:italic;" id="noLogoText">Belum ada logo dipilih</span>
                            </div>
                        </div>

                        <div class="upload-zone" style="margin-top:12px;" onclick="document.getElementById('logoFileInput').click()">
                            <i class="fa-solid fa-plus" style="margin-right:6px;"></i> Upload logo baru
                        </div>
                        <input type="file" id="logoFileInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;">
                        <div id="logoUploadProgress" style="display:none;margin-top:8px;font-size:12px;color:var(--text-secondary);">
                            <i class="fa-solid fa-spinner fa-spin"></i> Mengupload...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="preview-sticky">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-tv" style="color:var(--accent-2);"></i>
                        <span class="card-title">Preview</span>
                    </div>
                    <div class="card-body">
                        <div class="tv-container" id="miniTvContainer">
                            <video id="miniVideoBg" class="tv-bg" autoplay loop muted playsinline style="display:none;object-fit:cover;"></video>
                            <img id="miniImgBg" class="tv-bg" alt="" style="display:none;object-fit:cover;">
                            <div class="tv-standby" id="standby1">
                                <div class="tv-standby-text">E-BANNER</div>
                            </div>
                            <div class="tv-content-layer" id="miniPreviewContent"></div>
                        </div>
                        <div style="margin-top:12px;">
                            <button type="button" class="btn btn-primary" style="width:100%;" onclick="openFullscreenEditor()">
                                <i class="fa-solid fa-up-right-and-down-left-from-center"></i> Fullscreen Editor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:16px;">
            <button type="button" class="btn btn-ghost" onclick="goToStep(1)">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </button>
            <button type="button" class="btn btn-primary" onclick="goToStep(3)">
                Lanjut <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- ── STEP 3: Konten Teks ──────────────────────────────── -->
    <div class="step-panel" id="panel-3">
        <div class="create-grid">
            <div>
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-font" style="color:var(--accent-2);"></i>
                        <span class="card-title">Konten Teks</span>
                        <button type="button" class="btn btn-ghost btn-sm" style="margin-left:auto;" onclick="addContentRow()">
                            <i class="fa-solid fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="contentRowsContainer"></div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:8px;">
                            <i class="fa-solid fa-circle-info"></i>
                            Atur posisi & ukuran teks lewat Fullscreen Editor →
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview step 3 -->
            <div class="preview-sticky">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-tv" style="color:var(--accent-2);"></i>
                        <span class="card-title">Preview</span>
                    </div>
                    <div class="card-body">
                        <div class="tv-container" id="miniTvContainer2">
                            <video id="miniVideoBg2" class="tv-bg" autoplay loop muted playsinline style="display:none;object-fit:cover;"></video>
                            <img id="miniImgBg2" class="tv-bg" alt="" style="display:none;object-fit:cover;">
                            <div class="tv-standby" id="standby2">
                                <div class="tv-standby-text">E-BANNER</div>
                            </div>
                            <div class="tv-content-layer" id="miniPreviewContent2"></div>
                        </div>
                        <div style="margin-top:12px;">
                            <button type="button" class="btn btn-primary" style="width:100%;" onclick="openFullscreenEditor()">
                                <i class="fa-solid fa-up-right-and-down-left-from-center"></i> Fullscreen Editor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:16px;">
            <button type="button" class="btn btn-ghost" onclick="goToStep(2)">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </button>
            <button type="submit" class="btn btn-primary btn-lg" onclick="prepareSubmit()">
                <i class="fa-solid fa-paper-plane"></i> Simpan Request
            </button>
        </div>
    </div>
</form>

<!-- ── FULLSCREEN EDITOR ─────────────────────────────────── -->
<div id="fsEditor">
    <div id="fsContainer">
        <video id="fsBgVideo" class="fs-bg" autoplay loop playsinline style="display:none;object-fit:cover;"></video>
        <img id="fsBgImg" class="fs-bg" alt="" style="display:none;object-fit:cover;">
        <div id="snapV" class="snap-line"></div>
        <div id="snapH" class="snap-line"></div>
        <div id="fsGrid">
            <div class="fsg-v" style="left:33.33%"></div>
            <div class="fsg-v" style="left:66.67%"></div>
            <div class="fsg-h" style="top:33.33%"></div>
            <div class="fsg-h" style="top:66.67%"></div>
        </div>
        <div id="fsCanvas"></div>
    </div>

    <div class="fs-toolbar">
        <span class="fs-hint"><i class="fa-solid fa-hand-pointer"></i> Klik pilih &nbsp;|&nbsp; <i class="fa-solid fa-up-down-left-right"></i> Drag &nbsp;|&nbsp; <i class="fa-solid fa-expand"></i> Corner resize</span>
        <button style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.2);color:rgba(255,255,255,0.7);border-radius:5px;padding:5px 12px;font-size:12px;cursor:pointer;"
                id="gridToggleBtn" onclick="toggleGrid()">&#8862; GRID</button>
        <button class="btn btn-ghost btn-sm" onclick="toggleBrowserFs()" id="browserFsBtn">
            <i class="fa-solid fa-expand"></i>
        </button>
        <button class="btn btn-success btn-sm" onclick="saveEditorLayout()">
            <i class="fa-solid fa-floppy-disk"></i> Simpan & Tutup
        </button>
    </div>

    <!-- Editor overlay elements placed inside fsEditor to share its z-index context -->
    <div id="fsResizeHandles" style="pointer-events:none;">
        <div class="fs-corner-handle nw" data-pos="nw"></div>
        <div class="fs-corner-handle ne" data-pos="ne"></div>
        <div class="fs-corner-handle sw" data-pos="sw"></div>
        <div class="fs-corner-handle se" data-pos="se"></div>
    </div>
    <div id="resizeBar" onpointerdown="event.stopPropagation()" onclick="event.stopPropagation()">
        <span style="color:#aaa;font-size:12px;">Ukuran:</span>
        <button type="button" class="resize-btn" onclick="resizeActive(-0.3)">−</button>
        <input id="sizeDisplay" type="number" step="0.1" min="0.5" max="50"
               style="width:60px;text-align:center;background:#222;color:#fff;border:1px solid #555;border-radius:4px;padding:3px;font-size:13px;"
               oninput="applySizeInput()">
        <span style="color:#aaa;font-size:11px;">vw</span>
        <button type="button" class="resize-btn" onclick="resizeActive(+0.3)">+</button>
        <span style="color:#555;">|</span>
        <span id="selLabel" style="font-size:11px;color:#6366F1;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span>
        <span style="color:#555;">|</span>
        <span style="font-size:11px;color:#aaa;">Warna:</span>
        <input type="color" id="colorPickerFs" value="#FFFF00"
               style="width:28px;height:28px;border:none;border-radius:4px;cursor:pointer;padding:0;"
               oninput="setActiveColor(this.value)" onclick="event.stopPropagation()">
        <button type="button" style="background:#500;border:none;color:#fff;border-radius:4px;padding:4px 10px;font-size:12px;cursor:pointer;"
                onclick="removeActive()">Hapus</button>
    </div>
</div>

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════
let currentStep  = 1;
let bgState      = { url:'', type:'none' };
let selectedLogos = [];
let contentRows   = [];
let activeEl      = null;
let fsGridOn      = false;
let rowIdCounter  = 0;
const SNAP = 1.5;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ═══════════════════════════════════════════════════════════
// WIZARD
// ═══════════════════════════════════════════════════════════
function goToStep(n) {
    if (n === 2) {
        const v = ['displayId','startTime','endTime'].map(id => document.getElementById(id).value);
        const title = document.querySelector('input[name="title"]').value.trim();
        const date  = document.getElementById('displayDate').value;
        if (!v[0] || !title || !date || !v[1] || !v[2]) {
            showToast('Lengkapi semua field terlebih dahulu.', 'error'); return;
        }
    }
    document.getElementById('panel-' + currentStep).classList.remove('active');
    document.getElementById('step-indicator-' + currentStep).classList.remove('active');
    document.getElementById('step-indicator-' + currentStep).classList.add('done');
    currentStep = n;
    document.getElementById('panel-' + n).classList.add('active');
    document.getElementById('step-indicator-' + n).classList.remove('done');
    document.getElementById('step-indicator-' + n).classList.add('active');
    window.scrollTo({ top:0, behavior:'smooth' });
    if (n >= 2) syncPreview();
    if (n === 3) renderContentRows();
}

// ═══════════════════════════════════════════════════════════
// BACKGROUND
// ═══════════════════════════════════════════════════════════
function switchBgTab(type) {
    document.getElementById('bgVideoPanel').style.display = type === 'video' ? 'block' : 'none';
    document.getElementById('bgImagePanel').style.display = type === 'image' ? 'block' : 'none';
    document.querySelectorAll('.bg-type-btn').forEach(b => b.classList.toggle('active', b.dataset.type === type));
}

function selectBg(el) {
    document.querySelectorAll('#videoAssetGrid .asset-item, #imageAssetGrid .asset-item')
        .forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');
    bgState.url  = el.dataset.url;
    bgState.type = el.dataset.type;
    syncPreview();
}

function clearBg() {
    bgState = { url:'', type:'none' };
    document.querySelectorAll('.asset-item').forEach(i => i.classList.remove('selected'));
    syncPreview();
}



document.getElementById('bgFileInput').addEventListener('change', function () {
    const file = this.files[0]; if (!file) return;
    uploadAsset(file, file.type.startsWith('video') ? 'video_background' : 'image_background', file.name, 'bgUploadProgress')
        .then(asset => {
            if (!asset) return;
            bgState.url  = asset.url;
            bgState.type = asset.type === 'video_background' ? 'video' : 'image';
            syncPreview();
            addAssetToGrid(asset);
            showToast('Background berhasil diupload!', 'success');
        });
});

const bgDrop = document.getElementById('bgDropZone');
['dragenter','dragover'].forEach(e => bgDrop.addEventListener(e, ev => { ev.preventDefault(); bgDrop.classList.add('dragover'); }));
['dragleave','drop'].forEach(e => bgDrop.addEventListener(e, ev => { ev.preventDefault(); bgDrop.classList.remove('dragover'); }));
bgDrop.addEventListener('drop', ev => {
    const file = ev.dataTransfer.files[0];
    if (file) { const dt = new DataTransfer(); dt.items.add(file); document.getElementById('bgFileInput').files = dt.files; document.getElementById('bgFileInput').dispatchEvent(new Event('change')); }
});

document.getElementById('logoFileInput').addEventListener('change', function () {
    const file = this.files[0]; if (!file) return;
    uploadAsset(file, 'logo', file.name, 'logoUploadProgress')
        .then(asset => { if (asset) { addLogoToGrid(asset); showToast('Logo berhasil diupload!', 'success'); } });
});

function uploadAsset(file, type, name, progressId) {
    const prog = document.getElementById(progressId); prog.style.display = 'block';
    const fd = new FormData();
    fd.append('name', name.replace(/\.[^.]+$/, ''));
    fd.append('type', type); fd.append('file', file); fd.append('_token', csrfToken);
    return fetch('/assets/ajax-upload', { method:'POST', body:fd })
        .then(r => r.json())
        .then(data => { prog.style.display = 'none'; return data.asset; })
        .catch(() => { prog.style.display = 'none'; showToast('Upload gagal.', 'error'); return null; });
}

function addAssetToGrid(asset) {
    const grid = asset.type === 'video_background'
        ? document.getElementById('videoAssetGrid')
        : document.getElementById('imageAssetGrid');
    const div = document.createElement('div');
    div.className = 'asset-item';
    div.dataset.url  = asset.url;
    div.dataset.type = asset.type === 'video_background' ? 'video' : 'image';
    div.innerHTML = asset.type === 'video_background'
        ? `<video style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:4px;" muted preload="metadata"><source src="${asset.url}"></video><div class="asset-name">${asset.name}</div>`
        : `<img src="${asset.url}" class="asset-thumb" alt=""><div class="asset-name">${asset.name}</div>`;
    div.onclick = () => selectBg(div);
    grid.appendChild(div);
}

function addLogoToGrid(asset) {
    const grid = document.getElementById('logoAssetGrid');
    const div = document.createElement('div');
    div.className = 'asset-item logo-selectable';
    div.dataset.id = asset.id; div.dataset.url = asset.url; div.dataset.name = asset.name;
    div.innerHTML = `<img src="${asset.url}" class="asset-thumb" alt="${asset.name}"><div class="asset-name">${asset.name}</div>`;
    div.onclick = () => toggleLogo(div);
    grid.appendChild(div);
}

// ═══════════════════════════════════════════════════════════
// LOGOS — FIX: no noText.remove() anti-pattern
// ═══════════════════════════════════════════════════════════
function toggleLogo(el) {
    const id   = String(el.dataset.id || el.dataset.url);
    const url  = el.dataset.url;
    const name = el.dataset.name;
    const idx  = selectedLogos.findIndex(l => String(l.id) === id);

    if (idx >= 0) {
        selectedLogos.splice(idx, 1);
        el.classList.remove('selected');
    } else {
        const i = selectedLogos.length;
        selectedLogos.push({ id, url, name, x: (75 + i * 8) + '%', y:'10%', fs:8 });
        el.classList.add('selected');
    }
    renderLogoChips();
    syncPreview();
}

function renderLogoChips() {
    const chips = document.getElementById('selectedLogoChips');
    if (!selectedLogos.length) {
        chips.innerHTML = '<span style="font-size:11px;color:var(--text-muted);font-style:italic;" id="noLogoText">Belum ada logo dipilih</span>';
        return;
    }
    // Langsung overwrite innerHTML — tidak pakai .remove() yang bisa crash
    chips.innerHTML = selectedLogos.map((l, i) => `
        <span class="logo-chip">
            <img src="${l.url}" alt="">
            ${l.name}
            <i class="fa-solid fa-xmark remove-chip" onclick="removeLogo(${i})"></i>
        </span>
    `).join('');
}

function removeLogo(i) {
    const logo = selectedLogos[i];
    selectedLogos.splice(i, 1);
    document.querySelectorAll('.logo-selectable').forEach(el => {
        if (String(el.dataset.id) === String(logo.id) || el.dataset.url === logo.url)
            el.classList.remove('selected');
    });
    renderLogoChips();
    syncPreview();
}

// ═══════════════════════════════════════════════════════════
// CONTENT ROWS
// ═══════════════════════════════════════════════════════════
function addContentRow(text='', color='#FFFF00', x='', y='', fs='') {
    const id = ++rowIdCounter;
    contentRows.push({ id, text, color, x, y, fs });
    renderContentRows();
}

function removeContentRow(id) {
    contentRows = contentRows.filter(r => r.id !== id);
    renderContentRows(); syncPreview();
}

function renderContentRows() {
    const container = document.getElementById('contentRowsContainer');
    if (!contentRows.length) { addContentRow(); return; }
    container.innerHTML = '';
    contentRows.forEach((row, i) => {
        const div = document.createElement('div');
        div.className = 'content-row';
        div.dataset.rowId = row.id;
        div.innerHTML = `
            <div class="row-num">${i+1}</div>
            <div style="flex:1;">
                <input type="text" class="form-control row-text-input" data-row-id="${row.id}"
                       value="${row.text.replace(/"/g,'&quot;')}"
                       placeholder="Tulis teks yang ditampilkan..."
                       oninput="updateRowText(${row.id}, this.value)">
                <div style="display:flex;gap:8px;align-items:center;margin-top:8px;">
                    <label style="font-size:11px;color:var(--text-muted);">Warna:</label>
                    <input type="color" value="${row.color}" onchange="updateRowColor(${row.id}, this.value)"
                           style="width:36px;height:28px;border:none;border-radius:4px;cursor:pointer;padding:2px;background:var(--bg-base);">
                    <span style="font-size:11px;color:var(--text-muted);">
                        ${row.x ? `Posisi: (${row.x}, ${row.y}) | ${row.fs}vw` : 'Atur posisi di Fullscreen Editor'}
                    </span>
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm"
                    onclick="removeContentRow(${row.id})" ${contentRows.length <= 1 ? 'disabled' : ''}>
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
    });
}

function updateRowText(id, val) { const r=contentRows.find(r=>r.id===id); if(r){r.text=val;syncPreview();} }
function updateRowColor(id, val) { const r=contentRows.find(r=>r.id===id); if(r){r.color=val;syncPreview();} }

// ═══════════════════════════════════════════════════════════
// PREVIEW SYNC
// ═══════════════════════════════════════════════════════════
function syncPreview() {
    syncMiniPreview('miniVideoBg','miniImgBg','miniPreviewContent','miniTvContainer','standby1');
    syncMiniPreview('miniVideoBg2','miniImgBg2','miniPreviewContent2','miniTvContainer2','standby2');
}

// Play video reliably — wait for canplay event, then call play()
function playVideoReliably(videoEl) {
    if (videoEl._canPlayHandler) {
        videoEl.removeEventListener('canplay', videoEl._canPlayHandler);
        videoEl._canPlayHandler = null;
    }
    const handler = () => {
        videoEl.removeEventListener('canplay', handler);
        videoEl._canPlayHandler = null;
        const p = videoEl.play();
        if (p !== undefined) p.catch(() => {});
    };
    videoEl._canPlayHandler = handler;
    videoEl.addEventListener('canplay', handler);
    if (videoEl.readyState >= 3) handler();
}

function syncMiniPreview(vidId, imgId, contentId, containerId, standbyId) {
    const videoEl   = document.getElementById(vidId);
    const imgEl     = document.getElementById(imgId);
    const contentEl = document.getElementById(contentId);
    const standbyEl = document.getElementById(standbyId);
    if (!videoEl || !imgEl || !contentEl) return;

    // ── Background ──────────────────────────────────────────
    if (bgState.url && bgState.type === 'video') {
        videoEl.style.display = 'block'; imgEl.style.display = 'none';
        // Always reload if src changed
        if (videoEl._lastSrc !== bgState.url) {
            videoEl._lastSrc = bgState.url;
            videoEl.src = bgState.url;
            videoEl.load();
        }
        // Always try to play (covers paused state, autoplay policy retry)
        if (videoEl.paused) {
            playVideoReliably(videoEl);
        }
        if (standbyEl) standbyEl.style.display = 'none';
    } else if (bgState.url && bgState.type === 'image') {
        imgEl.style.display = 'block'; videoEl.style.display = 'none';
        videoEl.pause(); videoEl.currentTime = 0;
        if (imgEl.src !== bgState.url) imgEl.src = bgState.url;
        if (standbyEl) standbyEl.style.display = 'none';
    } else {
        videoEl.style.display = 'none'; imgEl.style.display = 'none';
        videoEl.pause(); videoEl.currentTime = 0;
        if (standbyEl) standbyEl.style.display = 'flex';
    }

    // ── Content elements ────────────────────────────────────
    const container = document.getElementById(containerId);
    const scaleX = container ? container.offsetWidth / window.innerWidth : 1;
    let html = '';

    // Logos
    selectedLogos.forEach(l => {
        if (!l.url) return;
        html += `<img src="${l.url}" style="position:absolute;left:${l.x};top:${l.y};width:${l.fs * scaleX}vw;height:auto;max-width:none;transform:translate(-50%,-50%);">`;
    });

    // Text rows
    const active = contentRows.filter(r => r.text.trim());
    const defFS  = active.length === 1 ? 5 : (active.length <= 3 ? 3.5 : 2.5);
    active.forEach((r, i) => {
        const x  = r.x  || '50%';
        const y  = r.y  || (35 + i * 10) + '%';
        const fs = (r.fs && parseFloat(r.fs) > 0) ? parseFloat(r.fs) * scaleX : defFS * scaleX;
        html += `<div style="position:absolute;left:${x};top:${y};font-size:${fs}vw;color:${r.color};font-weight:800;white-space:nowrap;transform:translate(-50%,-50%);text-shadow:0 0 8px rgba(0,0,0,0.9);">${r.text}</div>`;
    });

    contentEl.innerHTML = html;
}

// ═══════════════════════════════════════════════════════════
// FULLSCREEN EDITOR
// ═══════════════════════════════════════════════════════════
function openFullscreenEditor() {
    const editor = document.getElementById('fsEditor');
    const fsBgV  = document.getElementById('fsBgVideo');
    const fsBgI  = document.getElementById('fsBgImg');

    if (bgState.url && bgState.type === 'video') {
        fsBgV.style.display = 'block'; fsBgI.style.display = 'none';
        if (fsBgV._lastSrc !== bgState.url) {
            fsBgV._lastSrc = bgState.url;
            fsBgV.src = bgState.url;
            fsBgV.load();
        }
        
        // Sync time with mini TV preview
        const miniV = document.getElementById('miniVideoBg');
        if (miniV && miniV.readyState >= 1) {
            const syncTime = () => {
                try { fsBgV.currentTime = miniV.currentTime; } catch(e){}
                fsBgV.removeEventListener('loadedmetadata', syncTime);
            };
            if (fsBgV.readyState >= 1) syncTime();
            else fsBgV.addEventListener('loadedmetadata', syncTime);
        }

        // User clicked button = user gesture, play() is allowed
        fsBgV.play().catch(() => {});
    } else if (bgState.url && bgState.type === 'image') {
        fsBgI.style.display = 'block'; fsBgV.style.display = 'none';
        fsBgI.src = bgState.url;
    } else {
        fsBgV.style.display = 'none'; fsBgI.style.display = 'none';
    }

    // Build canvas items
    const canvas = document.getElementById('fsCanvas');
    canvas.querySelectorAll('.draggable-fs').forEach(el => el.remove());
    deselectAll();

    // Logos
    selectedLogos.forEach((l, i) => {
        const el = document.createElement('img');
        el.src = l.url;
        el.className = 'drag-item type-logo draggable-fs';
        el.dataset.type = 'logo'; el.dataset.logoIdx = i;
        el.style.cssText = `left:${l.x||'85%'};top:${l.y||'10%'};width:${l.fs||8}cqw;height:auto;max-width:none;`;
        el.draggable = false;
        canvas.appendChild(el);
        makeDraggable(el);
    });

    // Text rows
    const active = contentRows.filter(r => r.text.trim());
    const defFS  = active.length === 1 ? 5 : (active.length <= 3 ? 3.5 : 2.5);
    active.forEach((r, i) => {
        const el = document.createElement('div');
        el.className = 'drag-item draggable-fs';
        el.dataset.type = 'content'; el.dataset.rowId = r.id;
        const x  = r.x || '50%';
        const y  = r.y || (35 + i * 10) + '%';
        const fs = (r.fs && parseFloat(r.fs) > 0) ? parseFloat(r.fs) : defFS;
        el.style.cssText = `left:${x};top:${y};font-size:${fs}cqw;color:${r.color};`;
        el.textContent = r.text;
        canvas.appendChild(el);
        makeDraggable(el);
    });

    editor.classList.add('visible');
}

function saveEditorLayout() {
    const canvas = document.getElementById('fsCanvas');
    canvas.querySelectorAll('.draggable-fs').forEach(el => {
        if (el.dataset.type === 'logo') {
            const idx = parseInt(el.dataset.logoIdx);
            if (selectedLogos[idx]) {
                selectedLogos[idx].x  = el.style.left;
                selectedLogos[idx].y  = el.style.top;
                selectedLogos[idx].fs = parseFloat(el.style.width) || selectedLogos[idx].fs;
            }
        } else if (el.dataset.type === 'content') {
            const id  = parseInt(el.dataset.rowId);
            const row = contentRows.find(r => r.id === id);
            if (row) { 
                row.x = el.style.left; 
                row.y = el.style.top; 
                row.fs = parseFloat(el.style.fontSize); 
                if (el.dataset.hexColor) row.color = el.dataset.hexColor;
                else if (el.style.color && el.style.color.startsWith('rgb')) row.color = rgbToHex(el.style.color);
            }
        }
    });
    document.getElementById('fsEditor').classList.remove('visible');
    if (document.fullscreenElement) document.exitFullscreen();
    deselectAll();
    syncPreview();
    renderContentRows();
    showToast('Layout berhasil disimpan!', 'success');
}

// ── Drag — Pointer Events (mouse + touch + stylus unified) ────────────────
function makeDraggable(el) {
    let isDragging = false;
    let justSelectedByPointerdown = false;
    let startClientX, startClientY;
    let activePointerId = null;

    el.addEventListener('pointerdown', e => {
        if (e.target.classList.contains('sel-handle')) return;
        e.preventDefault();
        e.stopPropagation();
        el.setPointerCapture(e.pointerId); // Keep tracking even if pointer leaves element
        activePointerId = e.pointerId;
        isDragging = false;
        startClientX = e.clientX; startClientY = e.clientY;
        const parent = document.getElementById('fsCanvas');
        let sX = e.clientX, sY = e.clientY;

        if (activeEl !== el) {
            deselectAll();
            activeEl = el;
            updateResizeBar();
            updateSelectionBox();
            justSelectedByPointerdown = true;
        } else {
            justSelectedByPointerdown = false;
        }

        function onMove(e) {
            if (e.pointerId !== activePointerId) return;
            const totalDx = Math.abs(e.clientX - startClientX);
            const totalDy = Math.abs(e.clientY - startClientY);
            if (totalDx > 4 || totalDy > 4) isDragging = true;

            const dx = e.clientX - sX, dy = e.clientY - sY;
            sX = e.clientX; sY = e.clientY;
            let newL = Math.min(Math.max((parseFloat(el.style.left)/100*parent.clientWidth + dx)/parent.clientWidth*100, 0), 100);
            let newT = Math.min(Math.max((parseFloat(el.style.top)/100*parent.clientHeight + dy)/parent.clientHeight*100, 0), 100);
            if (Math.abs(newL - 50) < SNAP) { newL = 50; document.getElementById('snapV').style.display = 'block'; }
            else document.getElementById('snapV').style.display = 'none';
            if (Math.abs(newT - 50) < SNAP) { newT = 50; document.getElementById('snapH').style.display = 'block'; }
            else document.getElementById('snapH').style.display = 'none';
            el.style.left = newL.toFixed(2) + '%';
            el.style.top  = newT.toFixed(2) + '%';
            if (activeEl === el) updateSelectionBox();
        }
        function onUp(e) {
            if (e.pointerId !== activePointerId) return;
            el.removeEventListener('pointermove', onMove);
            el.removeEventListener('pointerup', onUp);
            el.removeEventListener('pointercancel', onUp);
            document.getElementById('snapV').style.display = 'none';
            document.getElementById('snapH').style.display = 'none';
            activePointerId = null;

            // Tap (no drag): deselect if was already selected before this pointerdown
            if (!isDragging && !justSelectedByPointerdown && activeEl === el) {
                deselectAll();
            }
            isDragging = false;
            justSelectedByPointerdown = false;
        }
        el.addEventListener('pointermove', onMove);
        el.addEventListener('pointerup', onUp);
        el.addEventListener('pointercancel', onUp);
    });
}

// Click empty canvas = deselect
document.getElementById('fsCanvas').addEventListener('click', e => {
    if (e.target === document.getElementById('fsCanvas')) deselectAll();
});

function deselectAll() {
    if (activeEl) activeEl = null;
    document.getElementById('resizeBar').classList.remove('visible');
    hideSelectionBox();
    document.getElementById('snapV').style.display = 'none';
    document.getElementById('snapH').style.display = 'none';
}

// ── Resize Bar ────────────────────────────────────────────
function updateResizeBar() {
    if (!activeEl) return;
    const bar    = document.getElementById('resizeBar');
    const isLogo = activeEl.dataset.type === 'logo';
    bar.classList.add('visible');
    document.getElementById('selLabel').textContent = (activeEl.textContent || 'Logo').substring(0, 25);
    const size = isLogo ? parseFloat(activeEl.style.width) : parseFloat(activeEl.style.fontSize);
    document.getElementById('sizeDisplay').value = (size || 5).toFixed(1);
    if (!isLogo) document.getElementById('colorPickerFs').value = rgbToHex(activeEl.style.color) || '#FFFF00';
}

function resizeActive(delta) {
    if (!activeEl) return;
    const isLogo = activeEl.dataset.type === 'logo';
    let cur = parseFloat(isLogo ? activeEl.style.width : activeEl.style.fontSize) || 3;
    cur = Math.max(0.5, Math.min(50, cur + delta));
    if (isLogo) activeEl.style.width = cur + 'cqw';
    else activeEl.style.fontSize = cur + 'cqw';
    document.getElementById('sizeDisplay').value = cur.toFixed(1);
    updateSelectionBox();
}

function applySizeInput() {
    if (!activeEl) return;
    const val = parseFloat(document.getElementById('sizeDisplay').value) || 3;
    if (activeEl.dataset.type === 'logo') activeEl.style.width = val + 'cqw';
    else activeEl.style.fontSize = val + 'cqw';
    updateSelectionBox();
}

function setActiveColor(color) {
    if (activeEl && activeEl.dataset.type === 'content') {
        activeEl.style.color = color;
        activeEl.dataset.hexColor = color;
    }
}

// ── Selection Box (ala Canva — referenced from old_reference) ──────────────
// Fixed position, 6px padding, 4 corner handles wired at DOMContentLoaded
const SEL_PAD = 6;

function updateSelectionBox() {
    if (!activeEl) { hideSelectionBox(); return; }
    const rect = activeEl.getBoundingClientRect();
    const box  = document.getElementById('fsResizeHandles');
    box.style.display  = 'block';
    box.style.position = 'fixed';
    box.style.left     = (rect.left   - SEL_PAD) + 'px';
    box.style.top      = (rect.top    - SEL_PAD) + 'px';
    box.style.width    = (rect.width  + SEL_PAD * 2) + 'px';
    box.style.height   = (rect.height + SEL_PAD * 2) + 'px';
}

function hideSelectionBox() {
    document.getElementById('fsResizeHandles').style.display = 'none';
}

// Wire corner handles — Pointer Events for mouse + touch
document.querySelectorAll('.fs-corner-handle').forEach(handle => {
    handle.addEventListener('pointerdown', e => {
        e.stopPropagation(); e.preventDefault();
        if (!activeEl) return;
        handle.setPointerCapture(e.pointerId);
        const isLogo  = activeEl.dataset.type === 'logo';
        const startX  = e.clientX;
        const startSz = parseFloat(isLogo ? activeEl.style.width : activeEl.style.fontSize) || 4;
        const dir     = handle.dataset.pos;
        
        // 1 cqw = 1% of fsCanvas width
        const canvas  = document.getElementById('fsCanvas');
        const cqwUnit = canvas.clientWidth / 100;

        function onMove(e) {
            const dx = e.clientX - startX;
            const delta = (dir === 'ne' || dir === 'se') ? dx : -dx;
            const newSz = Math.max(0.5, Math.min(50, Math.round((startSz + delta / cqwUnit) * 10) / 10));
            if (isLogo) activeEl.style.width = newSz + 'cqw';
            else activeEl.style.fontSize = newSz + 'cqw';
            document.getElementById('sizeDisplay').value = newSz.toFixed(1);
            updateSelectionBox();
        }
        function onUp() {
            handle.removeEventListener('pointermove', onMove);
            handle.removeEventListener('pointerup', onUp);
            handle.removeEventListener('pointercancel', onUp);
            updateResizeBar();
        }
        handle.addEventListener('pointermove', onMove);
        handle.addEventListener('pointerup', onUp);
        handle.addEventListener('pointercancel', onUp);
    });
});

// ── Grid & Fullscreen ─────────────────────────────────────
function toggleGrid() {
    fsGridOn = !fsGridOn;
    document.getElementById('fsGrid').classList.toggle('show', fsGridOn);
    document.getElementById('gridToggleBtn').style.color = fsGridOn ? '#0cf' : '';
}

function toggleBrowserFs() {
    const editor = document.getElementById('fsEditor');
    const isMobile = window.innerWidth <= 768;
    if (!document.fullscreenElement) {
        // On mobile portrait: try to lock to landscape instead of fullscreen
        if (isMobile && screen.orientation && screen.orientation.lock) {
            screen.orientation.lock('landscape').then(() => {
                editor.requestFullscreen().catch(() => {});
            }).catch(() => {
                // If lock fails, just try fullscreen
                editor.requestFullscreen().catch(() => {});
            });
        } else {
            editor.requestFullscreen().catch(() => {});
        }
        document.getElementById('browserFsBtn').innerHTML = '<i class="fa-solid fa-compress"></i>';
    } else {
        document.exitFullscreen();
        if (screen.orientation && screen.orientation.unlock) screen.orientation.unlock();
        document.getElementById('browserFsBtn').innerHTML = '<i class="fa-solid fa-expand"></i>';
    }
}

document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
        if (screen.orientation && screen.orientation.unlock) screen.orientation.unlock();
        document.getElementById('browserFsBtn').innerHTML = '<i class="fa-solid fa-expand"></i>';
    }
});

document.addEventListener('keydown', e => {
    if (!document.getElementById('fsEditor').classList.contains('visible')) return;
    if (e.key === 'g' || e.key === 'G') toggleGrid();
    if (e.key === 'Escape') saveEditorLayout();
    if (e.key === 'Delete' && activeEl) { activeEl.remove(); deselectAll(); }
});

// ── Helpers ────────────────────────────────────────────────
function rgbToHex(rgb) {
    if (!rgb) return '#FFFF00';
    if (rgb.startsWith('#')) return rgb;
    const m = rgb.match(/\d+/g);
    if (!m || m.length < 3) return '#FFFF00';
    return '#' + m.slice(0,3).map(x => parseInt(x).toString(16).padStart(2,'0')).join('');
}

// ═══════════════════════════════════════════════════════════
// SUBMIT
// ═══════════════════════════════════════════════════════════
function prepareSubmit() {
    const customElements = { bg: bgState, logos: selectedLogos };
    document.getElementById('customElementsJson').value = JSON.stringify(customElements);

    const form = document.getElementById('mainForm');
    form.querySelectorAll('.detail-hidden').forEach(el => el.remove());

    const active = contentRows.filter(r => r.text.trim());
    active.forEach((row, i) => {
        function addH(name, val) {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = `details[${i}][${name}]`;
            inp.value = val; inp.className = 'detail-hidden';
            form.appendChild(inp);
        }
        addH('content_text', row.text);
        addH('color', row.color);
        if (row.x) { 
            addH('positions][x', row.x); 
            addH('positions][y', row.y); 
            addH('positions][fs', row.fs); 
        }
    });
}

// ═══════════════════════════════════════════════════════════
// INIT — Pre-fill for edit mode
// ═══════════════════════════════════════════════════════════
@if(isset($content))
(function initEdit() {
    const ce = @json($content->custom_elements ?? []);
    if (ce) {
        if (ce.bg && ce.bg.url) {
            bgState = ce.bg;
            if (bgState.type === 'image') switchBgTab('image');
            document.querySelectorAll('.asset-item').forEach(el => {
                if (el.dataset.url === bgState.url) el.classList.add('selected');
            });
            syncPreview();
        }
        if (ce.logos && ce.logos.length) {
            ce.logos.forEach(l => {
                selectedLogos.push(l);
                document.querySelectorAll('.logo-selectable').forEach(el => {
                    if (el.dataset.url === l.url) el.classList.add('selected');
                });
            });
            renderLogoChips();
        }
    }
    const details = @json($content->details ?? []);
    details.forEach(d => {
        const pos = d.positions || {};
        addContentRow(d.content_text, d.color || '#FFFF00', pos.x || '', pos.y || '', pos.fs || '');
    });
})();
@else
addContentRow();
@endif
</script>
@endpush

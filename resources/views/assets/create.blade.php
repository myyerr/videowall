<x-app-layout pageTitle="Upload Asset Baru">
    <x-slot name="slot">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-cloud-arrow-up" style="color:var(--accent);"></i>
                    <span class="card-title">Upload Asset Baru</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Nama Asset *</label>
                            <input type="text" name="name" class="form-control" required
                                   value="{{ old('name') }}" placeholder="Contoh: Logo Kampus">
                            @error('name') <p class="text-danger" style="font-size:12px;margin-top:4px;color:var(--danger);">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipe Asset *</label>
                            <select name="type" id="assetType" class="form-control" required>
                                <option value="logo" {{ old('type') == 'logo' ? 'selected' : '' }}>Logo (gambar)</option>
                                <option value="video_background" {{ old('type') == 'video_background' ? 'selected' : '' }}>Video Background</option>
                                <option value="image_background" {{ old('type') == 'image_background' ? 'selected' : '' }}>Gambar Background</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">File Asset *</label>
                            <input type="file" name="file" id="fileInput" class="form-control" required>
                            <p id="fileHint" style="font-size:11px;color:var(--text-3);margin-top:6px;">Format gambar: PNG, JPG, JPEG, SVG, WEBP</p>
                            @error('file') <p class="text-danger" style="font-size:12px;margin-top:4px;color:var(--danger);">{{ $message }}</p> @enderror
                        </div>

                        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;">
                            <a href="{{ route('assets.index') }}" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.getElementById('assetType').addEventListener('change', function () {
                const hint = document.getElementById('fileHint');
                if (this.value === 'video_background') {
                    hint.textContent = 'Format video: MP4, WEBM, MOV (Maksimal 100MB)';
                } else {
                    hint.textContent = 'Format gambar: PNG, JPG, JPEG, SVG, WEBP (Maksimal 20MB)';
                }
            });
        </script>
        @endpush
    </x-slot>
</x-app-layout>
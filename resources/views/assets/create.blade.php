<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Asset Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Asset</label>
                        <input type="text" name="name" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                            placeholder="Contoh: Logo Himatif">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipe Asset</label>
                        <select name="type" id="assetType" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            <option value="logo">Logo (gambar)</option>
                            <option value="video_background">Video Background</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">File</label>
                        <input type="file" name="file" required
                            class="mt-1 block w-full">
                        <p id="fileHint" class="text-xs text-gray-500 mt-1">Format gambar: PNG, JPG, JPEG, SVG, WEBP</p>
                        @error('file') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('assetType').addEventListener('change', function () {
            const hint = document.getElementById('fileHint');
            if (this.value === 'video_background') {
                hint.textContent = 'Format video: MP4, WEBM, MOV';
            } else {
                hint.textContent = 'Format gambar: PNG, JPG, JPEG, SVG, WEBP';
            }
        });
    </script>
</x-app-layout>
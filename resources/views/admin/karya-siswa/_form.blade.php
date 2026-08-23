@php
    $sw = $studentWork ?? null;
@endphp

<div class="space-y-5">
    {{-- Judul Karya --}}
    <div>
        <label for="title" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
            Judul Karya <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="title" name="title" value="{{ old('title', $sw?->title) }}"
               placeholder="Masukkan judul karya..."
               class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all py-2.5 px-3" required>
        @error('title')
            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Grid 2 Kolom: Kontributor & Pembimbing --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Nama Kontributor --}}
        <div>
            <label for="contributor_name" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
                Nama Kelas / Siswa <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="contributor_name" name="contributor_name" value="{{ old('contributor_name', $sw?->contributor_name) }}"
                   placeholder="Nama kelas/siswa pembuat..."
                   class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all py-2.5 px-3" required>
            @error('contributor_name')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pembimbing --}}
        <div>
            <label for="supervisor_id" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
                Guru Pembimbing
            </label>
            <select id="supervisor_id" name="supervisor_id"
                    class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer py-2.5 px-3">
                <option value="">— Tidak ada —</option>
                @foreach ($supervisors ?? [] as $s)
                    <option value="{{ $s->id }}" @selected(old('supervisor_id', $sw?->supervisor_id) == $s->id)>
                        {{ $s->name }} @if(!$s->is_active && old('supervisor_id', $sw?->supervisor_id) != $s->id) (nonaktif) @endif
                    </option>
                @endforeach
            </select>
            @error('supervisor_id')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Deskripsi Karya --}}
    <div>
        <label for="description" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
            Deskripsi Karya
        </label>
        <textarea id="description" name="description" rows="4"
                  placeholder="Jelaskan detail mengenai karya ini..."
                  class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all py-2.5 px-3">{{ old('description', $sw?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Grid 2 Kolom: Demo URL & Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Demo URL --}}
        <div>
            <label for="demo_url" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
                Link Demo / Website
            </label>
            <input type="url" id="demo_url" name="demo_url" value="{{ old('demo_url', $sw?->demo_url) }}"
                   placeholder="https://..."
                   class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all py-2.5 px-3">
            @error('demo_url')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status Publikasi --}}
        <div>
            <label for="status" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
                Status Publikasi <span class="text-rose-500">*</span>
            </label>
            <select id="status" name="status"
                    class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer py-2.5 px-3" required>
                <option value="draft" @selected(old('status', is_object($sw?->status) ? $sw->status->value : ($sw?->status ?? 'draft')) === 'draft')>Draft</option>
                <option value="published" @selected(old('status', is_object($sw?->status) ? $sw->status->value : $sw?->status) === 'published')>Published</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Featured Checkbox Card --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 sm:p-4">
        <label class="flex items-start gap-3 cursor-pointer select-none">
            <input type="checkbox" name="is_featured" value="1"
                   @checked(old('is_featured', $sw?->is_featured))
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5 cursor-pointer">
            <div>
                <span class="block text-xs sm:text-sm font-semibold text-gray-800">Tampilkan sebagai Featured (Karya Unggulan)</span>
                <span class="block text-xs text-gray-500 mt-0.5">Karya ini akan disorot pada halaman utama galeri karya siswa.</span>
            </div>
        </label>
    </div>

    {{-- Existing Gallery (Khusus Mode Edit) --}}
    @if ($sw && isset($sw->galleries) && $sw->galleries->count() > 0)
        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/30">
            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
                Galeri Foto Saat Ini
            </label>
            <p class="text-xs text-gray-500 mb-3">Centang foto yang ingin dihapus dari galeri:</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                @foreach ($sw->galleries as $gallery)
                    <div class="relative border border-gray-200 rounded-lg overflow-hidden bg-white p-2 shadow-sm flex flex-col items-center">
                        <img src="{{ Storage::url($gallery->image_path ?? $gallery->image) }}" alt="Existing Gallery" class="w-full h-20 object-cover rounded mb-2">
                        <label class="flex items-center gap-1.5 text-xs text-rose-600 font-medium cursor-pointer select-none">
                            <input type="checkbox" name="remove_gallery_ids[]" value="{{ $gallery->id }}" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            <span>Hapus</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Upload Gambar Baru --}}
    <div>
        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">
            Unggah Gambar Baru
        </label>

        <label for="images" class="mt-1 flex flex-col items-center justify-center px-4 py-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/50 hover:bg-gray-100/80 transition-colors cursor-pointer">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="text-xs text-indigo-600 font-semibold">
                    <span>Klik untuk pilih file gambar</span>
                </div>
                <p class="text-[11px] text-gray-500">Maksimal total 5 gambar (@2MB, format JPEG/PNG/WebP)</p>
            </div>
            <input id="images" name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewImages(event)">
        </label>

        {{-- Container Live Preview --}}
        <div id="image-preview-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-3 hidden"></div>

        @error('images')
            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    function previewImages(event) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        const files = Array.from(event.target.files);

        const MAX_TOTAL_SIZE = 8 * 1024 * 1024;
        const MAX_FILE_SIZE = 2 * 1024 * 1024;
        const MAX_FILES_COUNT = 5;

        if (files.length > MAX_FILES_COUNT) {
            alert('Maksimal hanya boleh mengunggah 5 file gambar sekaligus.');
            event.target.value = '';
            container.classList.add('hidden');
            return;
        }

        let totalSize = 0;
        for (let file of files) {
            if (file.size > MAX_FILE_SIZE) {
                alert(`File "${file.name}" terlalu besar! Maksimal ukuran per gambar adalah 2 MB.`);
                event.target.value = '';
                container.classList.add('hidden');
                return;
            }
            totalSize += file.size;
        }

        if (totalSize > MAX_TOTAL_SIZE) {
            alert('Total ukuran seluruh gambar melebihi batas!');
            event.target.value = '';
            container.classList.add('hidden');
            return;
        }

        if (files.length > 0) {
            container.classList.remove('hidden');
            files.forEach((file) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const card = document.createElement('div');
                        card.className = 'relative border border-gray-200 rounded-lg overflow-hidden bg-white p-1 shadow-sm';
                        card.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-20 object-cover rounded">
                            <p class="text-[10px] text-gray-500 truncate mt-1 text-center font-medium">${file.name}</p>
                        `;
                        container.appendChild(card);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            container.classList.add('hidden');
        }
    }
</script>
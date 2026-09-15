@php
    $sw = $studentWork ?? $student_work ?? null;
    $coverUrl = $sw?->cover_url ?? ($sw?->cover?->url ?? null);
@endphp

<div class="space-y-5" 
     x-data="{
        coverFileName: '',
        onCoverSelected(event) {
            const file = event.target.files[0];
            if (!file) {
                this.resetCover();
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran berkas gambar sampul terlalu besar! Maksimal 5 MB.');
                event.target.value = '';
                this.resetCover();
                return;
            }

            this.coverFileName = file.name;

            if (file.type.startsWith('image/')) {
                // Panggil Modal Cropper Global khusus Cover (Rasio 16:9)
                $dispatch('open-cropper', {
                    title: 'Potong Sampul Utama Karya Siswa (16:9)',
                    aspectRatio: 16 / 9,
                    file: file,
                    targetInput: $refs.coverInput,
                    targetPreview: $refs.coverPreviewImg,
                    onCropComplete: () => {
                        $refs.coverPreviewBox.classList.remove('hidden');
                        if ($refs.coverPreviewEmpty) $refs.coverPreviewEmpty.classList.add('hidden');
                    }
                });
            }
        },
        resetCover() {
            if ($refs.coverInput) $refs.coverInput.value = '';
            if ($refs.coverPreviewImg) $refs.coverPreviewImg.src = '#';
            this.coverFileName = '';
            if ($refs.coverPreviewBox) $refs.coverPreviewBox.classList.add('hidden');
            if ($refs.coverPreviewEmpty) $refs.coverPreviewEmpty.classList.remove('hidden');
        }
     }">

    <!-- Input Tersembunyi Otomatis untuk ID Galeri yang Dihapus -->
    <template x-for="id in removedGalleries" :key="id">
        <input type="hidden" name="remove_gallery_ids[]" :value="id">
    </template>

    {{-- Judul Karya --}}
    <div>
        <label for="title" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Judul Karya <span class="text-rose-500 ml-1">*</span>
        </label>
        <input type="text" id="title" name="title" value="{{ old('title', $sw?->title) }}"
               placeholder="Masukkan judul karya..."
               class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('title') border-rose-300 bg-rose-50/30 @enderror" required>
        @error('title')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Grid 2 Kolom: Kontributor & Pembimbing --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Nama Kontributor --}}
        <div>
            <label for="contributor_name" class="block text-sm font-semibold text-slate-900 mb-1.5">
                Nama Kelas / Siswa
            </label>
            <input type="text" id="contributor_name" name="contributor_name" value="{{ old('contributor_name', $sw?->contributor_name) }}"
                   placeholder="Nama kelas/siswa pembuat..."
                   class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('contributor_name') border-rose-300 bg-rose-50/30 @enderror">
            @error('contributor_name')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Guru Pembimbing --}}
        <div>
            <label for="supervisor_id" class="block text-sm font-semibold text-slate-900 mb-1.5">
                Guru Pembimbing
            </label>
            <select id="supervisor_id" name="supervisor_id"
                    class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('supervisor_id') border-rose-300 bg-rose-50/30 @enderror">
                <option value="">— Tidak ada —</option>
                @foreach ($supervisors ?? [] as $s)
                    <option value="{{ $s->id }}" @selected(old('supervisor_id', $sw?->supervisor_id) == $s->id)>
                        {{ $s->name }} @if(isset($s->is_active) && !$s->is_active && old('supervisor_id', $sw?->supervisor_id) != $s->id) (nonaktif) @endif
                    </option>
                @endforeach
            </select>
            @error('supervisor_id')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Deskripsi Karya --}}
    <div>
        <label for="description" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Deskripsi Karya
        </label>
        <textarea id="description" name="description" rows="4"
                  placeholder="Jelaskan detail mengenai karya ini..."
                  class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('description') border-rose-300 bg-rose-50/30 @enderror">{{ old('description', $sw?->description) }}</textarea>
        @error('description')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Grid 2 Kolom: Demo URL & Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Demo URL --}}
        <div>
            <label for="demo_url" class="block text-sm font-semibold text-slate-900 mb-1.5">
                Link Demo / Website
            </label>
            <input type="url" id="demo_url" name="demo_url" value="{{ old('demo_url', $sw?->demo_url) }}"
                   placeholder="https://..."
                   class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('demo_url') border-rose-300 bg-rose-50/30 @enderror">
            @error('demo_url')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status Publikasi --}}
        <div>
            <label for="status" class="block text-sm font-semibold text-slate-900 mb-1.5">
                Status Publikasi <span class="text-rose-500 ml-1">*</span>
            </label>
            <select id="status" name="status"
                    class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('status') border-rose-300 bg-rose-50/30 @enderror" required>
                @foreach (\App\Enums\PublishStatus::cases() as $status)
                    @php
                        $statusVal = is_object($sw?->status) ? $sw->status->value : ($sw?->status ?? 'draft');
                    @endphp
                    <option value="{{ $status->value }}" @selected(old('status', $statusVal) === $status->value)>
                        {{ method_exists($status, 'label') ? $status->label() : ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Featured Checkbox Card --}}
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
        <label class="flex items-start gap-3 cursor-pointer select-none">
            <input type="checkbox" name="is_featured" value="1"
                   @checked(old('is_featured', $sw?->is_featured))
                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5 cursor-pointer">
            <div>
                <span class="block text-sm font-semibold text-slate-800">Tampilkan sebagai Featured (Karya Unggulan)</span>
                <span class="block text-xs text-slate-500 mt-0.5">Karya ini akan disorot pada halaman utama galeri karya siswa.</span>
            </div>
        </label>
    </div>

    {{-- SECTION 1: GAMBAR SAMPUL / COVER --}}
    <div class="pt-4 border-t border-slate-200 space-y-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Foto Sampul Utama (Cover)</h3>
            <p class="text-xs text-slate-500">Gambar ini akan tampil sebagai thumbnail utama di kartu daftar karya (Maks. 5MB, JPEG/PNG/WebP)</p>
        </div>

        <div class="flex flex-col sm:flex-row items-start gap-4">
            <div x-ref="coverPreviewBox" id="cover-preview-box" class="relative w-36 h-24 rounded-xl overflow-hidden border border-slate-300 shrink-0 shadow-xs {{ $coverUrl ? '' : 'hidden' }}">
                <img x-ref="coverPreviewImg" id="cover-preview-img" src="{{ $coverUrl ?? '#' }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Cover+Error';">
                <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 text-white text-[10px] text-center py-0.5 font-medium">Cover Aktif</div>
            </div>

            <div x-ref="coverPreviewEmpty" id="cover-preview-empty" class="w-36 h-24 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400 font-medium shrink-0 {{ $coverUrl ? 'hidden' : '' }}">
                Belum Ada
            </div>

            {{-- Input Unggah Cover Baru --}}
            <div class="flex-1 w-full">
                <label for="cover" class="flex flex-col items-center justify-center px-4 py-4 border-2 border-slate-300 border-dashed rounded-xl bg-slate-50/50 hover:bg-slate-100/80 transition cursor-pointer">
                    <div class="flex items-center gap-2 text-indigo-600 text-xs font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Pilih Gambar Sampul Baru</span>
                    </div>
                    <span x-text="coverFileName ? coverFileName : 'Pilih file jika ingin mengisi/mengubah sampul'" class="text-[11px] text-slate-400 mt-1 truncate max-w-xs"></span>
                    <input id="cover" name="cover" type="file" x-ref="coverInput" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onCoverSelected($event)">
                </label>
                @error('cover')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- SECTION 2: GALERI FOTO DOKUMENTASI (Maksimal 5 Foto) --}}
    <div class="pt-4 border-t border-slate-200 space-y-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Galeri Foto Pendukung (Maksimal 5 Foto)</h3>
            <p class="text-xs text-slate-500">Kumpulan foto dokumentasi/detail karya (Maksimal 5MB per file, JPEG/PNG/WebP)</p>
        </div>

        {{-- Foto Galeri Tersimpan --}}
        @if ($sw && isset($sw->galleries) && $sw->galleries->where('is_cover', false)->count() > 0)
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-600">
                    Foto Galeri Tersimpan (Klik foto untuk menandai batal/hapus):
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @foreach ($sw->galleries->where('is_cover', false) as $gallery)
                        @php
                            $media = $gallery->media ?? $gallery;
                            $galUrl = $media->url ?? (isset($media->file_path) ? \Illuminate\Support\Facades\Storage::url(ltrim($media->file_path, '/')) : null);
                        @endphp
                        @if ($galUrl)
                            <div @click="toggleRemoveGallery({{ $gallery->id }})"
                                 class="relative group rounded-xl overflow-hidden border-2 cursor-pointer transition-all duration-150 h-24 select-none"
                                 :class="removedGalleries.includes({{ $gallery->id }}) ? 'border-rose-400 ring-2 ring-rose-300/60' : 'border-slate-200 hover:border-indigo-400'">
                                
                                <img src="{{ $galUrl }}" 
                                     onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Foto+Galeri';"
                                     class="w-full h-full object-cover">
                                
                                {{-- Overlay Hapus --}}
                                <div x-show="removedGalleries.includes({{ $gallery->id }})" 
                                     class="absolute inset-0 bg-rose-950/60 backdrop-blur-[1px] flex flex-col items-center justify-center text-white text-xs font-semibold gap-1 p-2 text-center transition" x-cloak>
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="text-[10px] font-bold">Akan Dihapus</span>
                                </div>

                                {{-- Icon Silang 'X' --}}
                                <div x-show="!removedGalleries.includes({{ $gallery->id }})" 
                                     class="absolute top-1.5 right-1.5 bg-slate-900/60 text-white rounded-full p-1 opacity-80 group-hover:opacity-100 transition hover:bg-rose-600 shadow-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Unggah Foto Galeri Baru --}}
        <div>
            <label for="images" class="mt-1 flex flex-col items-center justify-center px-4 py-5 border-2 border-slate-300 border-dashed rounded-2xl bg-slate-50/50 hover:bg-slate-100/80 transition cursor-pointer">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-7 w-7 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="text-xs text-indigo-600 font-semibold">
                        <span>Pilih Gambar Galeri Baru</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Maksimal 5 foto galeri tambahan (Maks. 5MB per file)</p>
                </div>
                <input id="images" name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewGalleryImages(event)">
            </label>

            {{-- Container Live Preview Galeri Baru --}}
            <div id="gallery-preview-container" class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-3 hidden"></div>

            @error('images')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
            @error('images.*')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<script>
    function previewGalleryImages(event) {
        const container = document.getElementById('gallery-preview-container');
        if (!container) return;
        container.innerHTML = '';
        const files = Array.from(event.target.files);

        const MAX_FILE_SIZE = 5 * 1024 * 1024;
        const MAX_FILES_COUNT = 5;

        if (files.length > MAX_FILES_COUNT) {
            alert('Maksimal hanya boleh mengunggah 5 file foto galeri sekaligus.');
            event.target.value = '';
            container.classList.add('hidden');
            return;
        }

        for (let file of files) {
            if (file.size > MAX_FILE_SIZE) {
                alert(`File "${file.name}" terlalu besar! Maksimal ukuran per gambar galeri adalah 5 MB.`);
                event.target.value = '';
                container.classList.add('hidden');
                return;
            }
        }

        if (files.length > 0) {
            container.classList.remove('hidden');
            files.forEach((file) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const card = document.createElement('div');
                        card.className = 'relative border border-slate-200 rounded-xl overflow-hidden bg-white p-1.5 shadow-xs';
                        card.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg">
                            <p class="text-[10px] text-slate-500 truncate mt-1 text-center font-medium">${file.name}</p>
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
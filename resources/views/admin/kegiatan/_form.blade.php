@php 
    $act = $activity ?? null; 
@endphp

<div x-data="{
    coverPreview: null,
    removedGalleries: [],
    
    handleCoverChange(e) {
        const file = e.target.files[0];
        if (file) {
            this.coverPreview = URL.createObjectURL(file);
        }
    },
    
    toggleRemoveGallery(id) {
        if (this.removedGalleries.includes(id)) {
            this.removedGalleries = this.removedGalleries.filter(item => item !== id);
        } else {
            this.removedGalleries.push(id);
        }
    }
}" class="space-y-6">

    <!-- Input Tersembunyi untuk ID Galeri yang Dihapus -->
    <template x-for="id in removedGalleries" :key="id">
        <input type="hidden" name="remove_gallery_ids[]" :value="id">
    </template>

    <!-- Grid Judul & Tanggal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Judul Kegiatan <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title', $act->title ?? '') }}"
                   placeholder="Masukkan judul kegiatan..."
                   required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="event_date" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Tanggal Kegiatan <span class="text-red-500">*</span>
            </label>
            <input type="date"
                   name="event_date"
                   id="event_date"
                   value="{{ old('event_date', isset($act) && $act->event_date ? $act->event_date->format('Y-m-d') : '') }}"
                   required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('event_date') border-red-500 @enderror">
            @error('event_date')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Deskripsi / Konten -->
    <div>
        <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Kegiatan</label>
        <textarea name="content"
                  id="content"
                  rows="5"
                  placeholder="Tuliskan deskripsi lengkap mengenai kegiatan ini..."
                  class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none @error('content') border-red-500 @enderror">{{ old('content', $act->content ?? '') }}</textarea>
        @error('content')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Publikasi -->
    <div>
        <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">
            Status Publikasi <span class="text-red-500">*</span>
        </label>
        <select name="status" id="status" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('status') border-red-500 @enderror">
            @foreach (\App\Enums\PublishStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $act->status->value ?? 'draft') === $status->value)>
                    {{ ucfirst($status->value) }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Cover Kegiatan -->
    <div class="pt-4 border-t border-gray-100">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Kegiatan</label>
        <p class="text-xs text-gray-500 mb-3">Format: JPEG, PNG, WEBP (Maksimal 2MB)</p>

        <!-- Preview Cover -->
        <div class="mb-3 flex items-center gap-4">
            <template x-if="coverPreview">
                <div>
                    <span class="block text-xs font-medium text-gray-500 mb-1">Preview Baru:</span>
                    <img :src="coverPreview" class="w-40 h-24 object-cover rounded-lg border border-indigo-200 shadow-sm">
                </div>
            </template>

            @if (isset($act) && $act->cover)
                <div x-show="!coverPreview">
                    <span class="block text-xs font-medium text-gray-500 mb-1">Cover Saat Ini:</span>
                    <img src="{{ Storage::url($act->cover->file_path) }}" 
                         onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Cover+Tidak+Ditemukan';"
                         alt="Cover Saat Ini" 
                         class="w-40 h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                </div>
            @endif
        </div>

        <input type="file"
               name="cover"
               accept="image/jpeg,image/png,image/webp"
               @change="handleCoverChange"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none">
        @error('cover')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Gambar Galeri -->
    <div class="pt-4 border-t border-gray-100">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar Galeri (Dokumentasi)</label>
        <p class="text-xs text-gray-500 mb-3">Maksimal total 8 gambar galeri (@2MB, JPEG/PNG/WEBP)</p>

        <!-- Galeri Foto Saat Ini -->
        @if (isset($act) && $act->galleries->count() > 0)
            <div class="mb-4">
                <span class="block text-xs font-semibold text-gray-600 mb-2">
                    Foto Galeri Tersimpan (Klik foto untuk menandai batal/hapus):
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ($act->galleries as $gallery)
                        @if ($gallery->media)
                            <div @click="toggleRemoveGallery({{ $gallery->id }})"
                                 class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-video bg-gray-100 cursor-pointer transition transform active:scale-95"
                                 :class="{ 'ring-2 ring-red-500 opacity-60': removedGalleries.includes({{ $gallery->id }}) }">
                                
                                <img src="{{ Storage::url($gallery->media->file_path) }}" 
                                     onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Foto+Galeri';"
                                     class="w-full h-full object-cover">
                                
                                <div x-show="removedGalleries.includes({{ $gallery->id }})" 
                                     class="absolute inset-0 bg-red-900/70 flex flex-col items-center justify-center text-white text-xs font-bold gap-1 p-1 text-center">
                                    <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Akan Dihapus</span>
                                </div>

                                <div x-show="!removedGalleries.includes({{ $gallery->id }})" 
                                     class="absolute top-1.5 right-1.5 bg-black/60 text-white rounded-full p-1 opacity-80 group-hover:opacity-100 transition hover:bg-red-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <input type="file"
               name="images[]"
               multiple
               accept="image/jpeg,image/png,image/webp"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none">
        @error('images')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button Bar Dinamis -->
    <div class="pt-5 border-t border-gray-200 flex items-center justify-between gap-3">
        <a href="{{ route('admin.kegiatan.index') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            Batal
        </a>

        <button type="submit" 
                :class="removedGalleries.length > 0 ? 'bg-rose-600 hover:bg-rose-700 ring-2 ring-rose-500/30' : 'bg-indigo-600 hover:bg-indigo-700'"
                class="inline-flex items-center justify-center gap-2 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 text-sm">
            
            <template x-if="removedGalleries.length > 0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </template>

            <template x-if="removedGalleries.length === 0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </template>

            <span x-text="removedGalleries.length > 0 ? `Hapus (${removedGalleries.length}) Gambar & Simpan` : '{{ isset($act) ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}'"></span>
        </button>
    </div>
</div>
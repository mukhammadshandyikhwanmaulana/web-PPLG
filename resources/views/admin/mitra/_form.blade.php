@php 
    $p = $partner ?? $p ?? null; 
    
    // Penanganan URL Logo Dinamis (Melalui Accessor, Relasi Media, atau Storage Path)
    $currentLogoUrl = $p?->logo_url;
    if (!$currentLogoUrl && $p?->logo_path) {
        $currentLogoUrl = \Illuminate\Support\Facades\Storage::disk($p->disk ?? 'public')->url($p->logo_path);
    } elseif (!$currentLogoUrl && isset($p->media) && $p->media?->path) {
        $currentLogoUrl = \Illuminate\Support\Facades\Storage::disk($p->media->disk ?? 'public')->url($p->media->path);
    }
@endphp

<div class="space-y-5"
     x-data="{
         onLogoSelected(event) {
             const file = event.target.files[0];
             if (!file) return;

             if (file.size > 2 * 1024 * 1024) {
                 alert('Ukuran berkas logo terlalu besar! Maksimal 2 MB.');
                 event.target.value = '';
                 return;
             }

             // Jalankan Cropper jika berupa file gambar raster (JPG, PNG, WEBP), bukan SVG
             if (file.type.startsWith('image/') && file.type !== 'image/svg+xml') {
                 $dispatch('open-cropper', {
                     title: 'Potong / Sesuaikan Logo Mitra',
                     aspectRatio: NaN, // Rasio bebas (fleksibel untuk logo persegi/persegi panjang)
                     file: file,
                     targetInput: $refs.logoInput,
                     targetPreview: $refs.logoPreviewImg,
                     onCropComplete: () => {
                         if ($refs.logoPreviewContainer) {
                             $refs.logoPreviewContainer.classList.remove('hidden');
                         }
                     }
                 });
             } else {
                 // Untuk SVG / Non-croppable images, tampilkan pratinjau langsung via FileReader
                 const reader = new FileReader();
                 reader.onload = (e) => {
                     if ($refs.logoPreviewImg) $refs.logoPreviewImg.src = e.target.result;
                     if ($refs.logoPreviewContainer) $refs.logoPreviewContainer.classList.remove('hidden');
                 };
                 reader.readAsDataURL(file);
             }
         }
     }">

    {{-- Nama Mitra --}}
    <div>
        <label for="name" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Nama Mitra <span class="text-rose-500 ml-1">*</span>
        </label>
        <input type="text" name="name" id="name" value="{{ old('name', $p?->name) }}" required
               placeholder="Nama perusahaan/mitra industri..."
               class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('name') border-rose-300 bg-rose-50/30 @enderror">
        @error('name') 
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Website URL --}}
    <div>
        <label for="website_url" class="block text-sm font-semibold text-slate-900 mb-1.5">Website URL</label>
        <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $p?->website_url) }}"
               placeholder="https://example.com"
               class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('website_url') border-rose-300 bg-rose-50/30 @enderror">
        @error('website_url') 
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Logo Mitra --}}
    <div>
        <label for="logo" class="block text-sm font-semibold text-slate-900 mb-1">Logo Mitra</label>
        <p class="text-xs text-slate-400 mb-2.5">Opsional (Maksimal 2MB, format JPEG, PNG, WEBP, SVG)</p>

        <div class="flex items-start gap-4 mb-3">
            <div x-ref="logoPreviewContainer" class="{{ $currentLogoUrl ? 'block' : 'hidden' }}">
                <span class="block text-xs font-medium text-slate-500 mb-1">Pratinjau Logo:</span>
                <img x-ref="logoPreviewImg"
                     id="logo-preview" 
                     src="{{ $currentLogoUrl ?? '#' }}" 
                     alt="Preview Logo" 
                     onerror="this.onerror=null; this.src='https://placehold.co/300x200/f8fafc/94a3b8?text=No+Logo';"
                     class="w-32 h-24 object-contain bg-slate-50 rounded-xl border border-slate-200 shadow-xs p-1">
            </div>
        </div>

        <input type="file" 
               name="logo" 
               id="logo" 
               x-ref="logoInput"
               accept="image/jpeg,image/png,image/webp,image/svg+xml"
               @change="onLogoSelected($event)"
               class="w-full text-sm text-slate-500 p-1 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl cursor-pointer bg-slate-50/50 shadow-xs focus:outline-none flex items-center @error('logo') border-rose-300 bg-rose-50/30 @enderror">
        @error('logo') 
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Urutan Tampil --}}
    <div>
        <label for="sort_order" class="block text-sm font-semibold text-slate-900 mb-1">Urutan Tampil</label>
        <p class="text-xs text-slate-400 mb-1.5">Angka urutan posisi tampilan (misal: 1, 2, 3)</p>
        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $p?->sort_order) }}" min="1" placeholder="Otomatis (urutan terakhir)"
               class="w-full sm:w-64 text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('sort_order') border-rose-300 bg-rose-50/30 @enderror">
        @error('sort_order') 
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Status Publikasi --}}
    <div>
        <label for="status" class="block text-sm font-semibold text-slate-900 mb-1.5">
            Status Publikasi <span class="text-rose-500 ml-1">*</span>
        </label>
        <select name="status" id="status" required
                class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('status') border-rose-300 bg-rose-50/30 @enderror">
            @foreach (\App\Enums\PublishStatus::cases() as $status)
                @php
                    $statusVal = is_object($p?->status) ? $p->status->value : ($p?->status ?? 'draft');
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
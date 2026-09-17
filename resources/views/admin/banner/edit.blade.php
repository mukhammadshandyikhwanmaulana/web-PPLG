@extends('layouts.admin.app')

@section('title', 'Edit Gambar Hero')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.banner.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Hero
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Gambar Hero</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ganti foto atau kelola status tampil gambar hero ini.</p>
        </div>
    </div>

    {{-- Form Card Utama --}}
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6"
         x-data="{
            hasNewImage: false,
            onFileSelected(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ukuran berkas terlalu besar! Maksimal 10 MB.');
                        event.target.value = '';
                        return;
                    }

                    // Triggers Cropper modal global (Aspect ratio 16:9 untuk Hero)
                    window.dispatchEvent(new CustomEvent('open-cropper', {
                        detail: {
                            title: 'Potong Gambar Hero Pengganti (16:9)',
                            aspectRatio: 16 / 9,
                            file: file,
                            targetInput: $refs.heroInput,
                            onCropComplete: (croppedFile) => {
                                if ($refs.heroPreviewImg) {
                                    $refs.heroPreviewImg.src = URL.createObjectURL(croppedFile);
                                }
                            }
                        }
                    }));

                    this.hasNewImage = true;
                }
            },
            resetImage() {
                if ($refs.heroInput) $refs.heroInput.value = '';
                if ($refs.heroPreviewImg) $refs.heroPreviewImg.src = '#';
                this.hasNewImage = false;
            }
         }">
        <form method="POST" action="{{ route('admin.banner.update', $banner) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Foto Aktif Saat Ini --}}
            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-1.5">Gambar Hero Saat Ini</label>
                @php
                    $imageUrl = $banner->image_path ? Storage::disk('public')->url(ltrim($banner->image_path, '/')) : $banner->image_url;
                @endphp
                <div class="max-w-lg rounded-2xl overflow-hidden border border-slate-300 bg-slate-50 p-2">
                    <img src="{{ $imageUrl }}" alt="Hero Image" class="w-full h-56 object-cover rounded-xl shadow-xs">
                </div>
            </div>

            {{-- Ganti Foto Baru --}}
            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-1.5">Unggah Gambar Pengganti (Opsional)</label>
                <div class="border-2 border-dashed border-slate-300 hover:border-slate-400 bg-slate-50/50 hover:bg-slate-50 rounded-2xl p-4 sm:p-6 transition-all text-center relative">
                    <input id="image" x-ref="heroInput" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/jpg" class="hidden" @change="onFileSelected($event)">

                    <div x-show="!hasNewImage" class="space-y-2 cursor-pointer" @click="$refs.heroInput.click()">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white border border-slate-300 flex items-center justify-center mx-auto text-slate-500 shadow-xs">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-slate-700"><span class="text-indigo-600 font-semibold underline hover:text-indigo-700">Klik di sini</span> jika ingin mengganti gambar di atas</p>
                        <p class="text-[11px] sm:text-xs text-slate-400">PNG, JPG, WEBP (Potongan Presisi 16:9)</p>
                    </div>

                    <div x-show="hasNewImage" x-cloak class="space-y-3">
                        <p class="text-xs font-semibold text-emerald-600">Preview Gambar Baru Pengganti:</p>
                        <div class="max-w-lg mx-auto">
                            <img x-ref="heroPreviewImg" src="#" alt="Preview New Image" class="w-full h-56 object-cover rounded-xl border border-slate-300 shadow-xs">
                        </div>
                        <button type="button" @click="resetImage()" class="text-xs font-medium text-rose-600 hover:underline cursor-pointer">Batal Ganti Gambar</button>
                    </div>
                </div>
                @error('image') <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Toggle Status --}}
            <div class="pt-2">
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-semibold text-slate-900">Tampilkan Gambar Ini di Slider</span>
                </label>
            </div>

            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.banner.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
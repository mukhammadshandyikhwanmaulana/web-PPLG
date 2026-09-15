@extends('layouts.admin.app')

@section('title', 'Edit Fasilitas')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.fasilitas.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Fasilitas
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Fasilitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Isi formulir di bawah ini untuk memperbarui data fasilitas.</p>
        </div>
    </div>

    {{-- Form Card --}}
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

                    // Panggil Event Cropper Modal Global (Rasio 4:3 untuk Fasilitas)
                    $dispatch('open-cropper', {
                        title: 'Potong Foto Fasilitas Pengganti (4:3)',
                        aspectRatio: 4 / 3,
                        file: file,
                        targetInput: $refs.photoInput,
                        targetPreview: $refs.photoPreviewImg
                    });

                    this.hasNewImage = true;
                }
            },
            resetImage() {
                if ($refs.photoInput) $refs.photoInput.value = '';
                if ($refs.photoPreviewImg) $refs.photoPreviewImg.src = '#';
                this.hasNewImage = false;
            }
         }">
        <form method="POST" action="{{ route('admin.fasilitas.update', $facility) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama Fasilitas --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Nama Fasilitas <span class="text-rose-500 ml-1">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $facility->name) }}" required class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('name') border-rose-300 bg-rose-50/30 @enderror">
                @error('name') <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900 mb-1.5">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('description') border-rose-300 bg-rose-50/30 @enderror">{{ old('description', $facility->description) }}</textarea>
                @error('description') <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Foto Fasilitas --}}
            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-1.5">Foto Fasilitas</label>
                
                @php 
                    $mediaPhoto = $facility->photoMedia ?? $facility->photo; 
                    $photoPath = $mediaPhoto?->path ?? $mediaPhoto?->file_path;
                    $diskName = $mediaPhoto?->disk ?? 'public';
                    $photoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::disk($diskName)->url(ltrim($photoPath, '/')) : null;
                @endphp

                @if ($photoUrl)
                    <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-300 flex items-center gap-3">
                        <img src="{{ $photoUrl }}" alt="{{ $facility->name }}" class="w-16 h-16 rounded-lg object-cover border border-slate-300 shrink-0 shadow-xs">
                        <div class="min-w-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-1">Foto Aktif</span>
                            <p class="text-xs text-slate-500">Pilih berkas di bawah jika ingin mengganti foto ini.</p>
                        </div>
                    </div>
                @endif

                <div class="border-2 border-dashed border-slate-300 hover:border-slate-400 bg-slate-50/50 hover:bg-slate-50 rounded-2xl p-4 sm:p-6 transition-all text-center relative">
                    <input id="photo" x-ref="photoInput" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onFileSelected($event)">

                    <div x-show="!hasNewImage" class="space-y-2 cursor-pointer" @click="$refs.photoInput.click()">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white border border-slate-300 flex items-center justify-center mx-auto text-slate-500 shadow-xs">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-slate-700"><span class="text-indigo-600 font-semibold underline hover:text-indigo-700">Klik di sini</span> untuk memilih foto pengganti</p>
                        <p class="text-[11px] sm:text-xs text-slate-400">PNG, JPG, WEBP (Potongan Presisi 4:3)</p>
                    </div>

                    <div x-show="hasNewImage" x-cloak class="space-y-3">
                        <p class="text-xs font-semibold text-emerald-600">Preview Foto Baru:</p>
                        <img x-ref="photoPreviewImg" src="#" alt="Preview Baru" class="max-h-40 sm:max-h-52 rounded-xl mx-auto shadow-xs border border-slate-300 object-cover">
                        <button type="button" @click="resetImage()" class="text-xs font-medium text-rose-600 hover:underline cursor-pointer">Batal Ganti Foto</button>
                    </div>
                </div>
                @error('photo') <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Urutan Tampil --}}
            <div class="max-w-xs">
                <label for="sort_order" class="block text-sm font-semibold text-slate-900 mb-1.5">Urutan Tampil</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $facility->sort_order) }}" min="1" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('sort_order') border-rose-300 bg-rose-50/30 @enderror">
                @error('sort_order') <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Area Tombol CTA --}}
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.fasilitas.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
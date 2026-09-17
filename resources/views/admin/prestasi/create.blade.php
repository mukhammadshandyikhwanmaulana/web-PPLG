@extends('layouts.admin.app')

@section('title', 'Tambah Prestasi')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.prestasi.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Prestasi
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Tambah Prestasi Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Isi formulir di bawah ini untuk menambahkan data prestasi.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6"
         x-data="{
            filePreview: null,
            fileName: '',
            isPdf: false,
            onFileSelected(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.resetFile();
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran berkas terlalu besar! Maksimal 5 MB.');
                    event.target.value = '';
                    this.resetFile();
                    return;
                }

                this.fileName = file.name;
                this.isPdf = (file.type === 'application/pdf');

                if (this.isPdf) {
                    this.filePreview = null;
                } else if (file.type.startsWith('image/')) {
                    // Panggil Cropper Modal Global untuk Gambar Prestasi (Rasio 16:9)
                    window.dispatchEvent(new CustomEvent('open-cropper', {
                        detail: {
                            title: 'Potong Dokumen / Gambar Prestasi (16:9)',
                            aspectRatio: 16 / 9,
                            file: file,
                            targetInput: $refs.docInput,
                            onCropComplete: (croppedFile) => {
                                if ($refs.docPreviewImg) {
                                    $refs.docPreviewImg.src = URL.createObjectURL(croppedFile);
                                }
                            }
                        }
                    }));
                }
            },
            resetFile() {
                if ($refs.docInput) $refs.docInput.value = '';
                if ($refs.docPreviewImg) $refs.docPreviewImg.src = '#';
                this.fileName = '';
                this.filePreview = null;
                this.isPdf = false;
            }
         }">
        <form action="{{ route('admin.prestasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-900 mb-1.5">Judul Prestasi <span class="text-rose-500 ml-1">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Masukkan judul prestasi..." class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('title') border-rose-300 bg-rose-50/30 @enderror">
                @error('title')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contributor Name --}}
            <div>
                <label for="contributor_name" class="block text-sm font-semibold text-slate-900 mb-1.5">Nama Kontributor/Siswa</label>
                <input type="text" name="contributor_name" id="contributor_name" value="{{ old('contributor_name') }}" placeholder="Nama siswa/tim..." class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('contributor_name') border-rose-300 bg-rose-50/30 @enderror">
                @error('contributor_name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Level --}}
                <div>
                    <label for="level" class="block text-sm font-semibold text-slate-900 mb-1.5">Tingkat / Level</label>
                    <select name="level" id="level" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('level') border-rose-300 bg-rose-50/30 @enderror">
                        <option value="">-- Pilih Level --</option>
                        @foreach(\App\Enums\AchievementLevel::cases() as $levelEnum)
                            <option value="{{ $levelEnum->value }}" {{ old('level') === $levelEnum->value ? 'selected' : '' }}>
                                {{ method_exists($levelEnum, 'label') ? $levelEnum->label() : ucfirst($levelEnum->value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('level')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Achievement Date --}}
                <div>
                    <label for="achievement_date" class="block text-sm font-semibold text-slate-900 mb-1.5">Tanggal Prestasi</label>
                    <input type="date" name="achievement_date" id="achievement_date" value="{{ old('achievement_date') }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('achievement_date') border-rose-300 bg-rose-50/30 @enderror">
                    @error('achievement_date')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900 mb-1.5">Deskripsi</label>
                <textarea name="description" id="description" rows="4" placeholder="Penjelasan mengenai prestasi..." class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-y @error('description') border-rose-300 bg-rose-50/30 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Input & Live Preview --}}
            <div>
                <label for="document" class="block text-sm font-semibold text-slate-900 mb-1">Dokumen / Bukti</label>
                <p class="text-xs text-slate-400 mb-2">(Format JPG, JPEG, PNG, WebP, PDF — Maksimal 5MB)</p>

                {{-- Preview PDF yang dipilih --}}
                <div x-show="fileName && isPdf" x-cloak class="mb-3 p-3 border border-rose-200 rounded-xl bg-rose-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 text-sm text-slate-700">
                        <svg class="w-6 h-6 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium text-slate-800 break-all" x-text="fileName"></span>
                        <span class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-md font-semibold shrink-0">PDF</span>
                    </div>
                    <button type="button" @click="resetFile()" class="text-xs font-medium text-rose-600 hover:underline cursor-pointer ml-3">Hapus</button>
                </div>

                {{-- Preview Gambar Hasil Crop --}}
                <div x-show="fileName && !isPdf" x-cloak class="mb-3 flex items-center gap-3">
                    <img x-ref="docPreviewImg" src="#" alt="Preview Berkas" class="w-32 h-20 object-cover rounded-xl border border-slate-300 shadow-xs">
                    <button type="button" @click="resetFile()" class="text-xs font-medium text-rose-600 hover:underline cursor-pointer">Hapus Berkas</button>
                </div>

                <input type="file" name="document" id="document" 
                       x-ref="docInput"
                       accept="image/jpeg,image/png,image/webp,image/jpg,application/pdf" 
                       @change="onFileSelected($event)"
                       class="w-full text-sm text-slate-500 p-1 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl cursor-pointer bg-slate-50/50 shadow-xs focus:outline-none flex items-center @error('document') border-rose-300 bg-rose-50/30 @enderror">
                @error('document')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-900 mb-1.5">Status Publikasi <span class="text-rose-500 ml-1">*</span></label>
                <select name="status" id="status" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('status') border-rose-300 bg-rose-50/30 @enderror" required>
                    @foreach(\App\Enums\PublishStatus::cases() as $statusEnum)
                        <option value="{{ $statusEnum->value }}" {{ old('status', 'draft') === $statusEnum->value ? 'selected' : '' }}>
                            {{ method_exists($statusEnum, 'label') ? $statusEnum->label() : ucfirst($statusEnum->value) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Area Tombol CTA --}}
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.prestasi.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
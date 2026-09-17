@extends('layouts.guru.app')

@section('title', 'Catat Prestasi Baru')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4"
     x-data="{
         filePreview: null,
         fileName: '',
         isPdf: false,
         handleFileChange(event) {
             const file = event.target.files[0];
             if (!file) {
                 this.filePreview = null;
                 this.fileName = '';
                 this.isPdf = false;
                 return;
             }

             if (file.size > 5 * 1024 * 1024) {
                 alert('Ukuran berkas terlalu besar! Maksimal 5 MB.');
                 event.target.value = '';
                 this.filePreview = null;
                 this.fileName = '';
                 this.isPdf = false;
                 return;
             }

             this.fileName = file.name;
             this.isPdf = file.type === 'application/pdf';

             if (!this.isPdf && file.type.startsWith('image/')) {
                 $dispatch('open-cropper', {
                     title: 'Potong Gambar Bukti Prestasi',
                     aspectRatio: null,
                     file: file,
                     targetInput: $refs.docInput,
                     onCropComplete: (croppedFile) => {
                         this.fileName = croppedFile.name;
                         if (this.filePreview && this.filePreview.startsWith('blob:')) {
                             URL.revokeObjectURL(this.filePreview);
                         }
                         this.filePreview = URL.createObjectURL(croppedFile);
                     }
                 });
             } else {
                 this.filePreview = null;
             }
         }
     }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('guru.prestasi.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Prestasi
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Catat Prestasi Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tambahkan data pencapaian lomba atau kejuaraan jurusan PPLG.</p>
        </div>
    </div>

    <!-- Alert Error Validation Global -->
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center gap-2 shadow-xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Mohon periksa kembali inputan Anda. Beberapa bidang belum terisi dengan benar.</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6">
        <form action="{{ route('guru.prestasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-900 mb-1.5">Nama Prestasi / Juara <span class="text-rose-500 ml-1">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Juara 1 LKS Web Technologies Tingkat Provinsi" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('title') border-rose-300 bg-rose-50/30 @enderror">
                @error('title')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contributor Name --}}
            <div>
                <label for="contributor_name" class="block text-sm font-semibold text-slate-900 mb-1.5">Nama Peraih / Tim <span class="text-rose-500 ml-1">*</span></label>
                <input type="text" name="contributor_name" id="contributor_name" value="{{ old('contributor_name') }}" required placeholder="Contoh: Muhammad Budi (Siswa) / Pak Rudi (Guru)" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('contributor_name') border-rose-300 bg-rose-50/30 @enderror">
                @error('contributor_name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Level --}}
                <div>
                    <label for="level" class="block text-sm font-semibold text-slate-900 mb-1.5">Tingkat Prestasi <span class="text-rose-500 ml-1">*</span></label>
                    <select name="level" id="level" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('level') border-rose-300 bg-rose-50/30 @enderror" required>
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
                    <label for="achievement_date" class="block text-sm font-semibold text-slate-900 mb-1.5">Tanggal Perolehan <span class="text-rose-500 ml-1">*</span></label>
                    <input type="date" name="achievement_date" id="achievement_date" value="{{ old('achievement_date', date('Y-m-d')) }}" required class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white cursor-pointer @error('achievement_date') border-rose-300 bg-rose-50/30 @enderror">
                    @error('achievement_date')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900 mb-1.5">Keterangan / Deskripsi Singkat</label>
                <textarea name="description" id="description" rows="3" placeholder="Catatan penyelenggara lomba, tempat, atau detail tambahan..." class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('description') border-rose-300 bg-rose-50/30 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Input & Live Preview --}}
            <div>
                <label for="document" class="block text-sm font-semibold text-slate-900 mb-1">Dokumen / Bukti</label>
                <p class="text-xs text-slate-400 mb-2">(Format JPG, JPEG, PNG, WebP, PDF — Maksimal 5MB)</p>

                {{-- Live Preview Berkas Baru --}}
                <div x-show="fileName" x-cloak class="mb-3 p-3 border border-indigo-200 rounded-xl bg-indigo-50/50">
                    <p class="text-xs text-indigo-700 font-semibold mb-2">Berkas yang Dipilih:</p>

                    <template x-if="isPdf">
                        <div class="flex items-center gap-2 text-sm text-slate-700">
                            <svg class="w-6 h-6 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium text-slate-800 break-all" x-text="fileName"></span>
                            <span class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-md font-semibold shrink-0">PDF</span>
                        </div>
                    </template>

                    <template x-if="filePreview">
                        <div>
                            <img :src="filePreview" class="w-32 h-24 object-cover rounded-lg border border-indigo-200 shadow-xs">
                            <p class="text-[11px] text-slate-500 mt-1 truncate" x-text="fileName"></p>
                        </div>
                    </template>
                </div>

                <input type="file" name="document" id="document" 
                       x-ref="docInput"
                       accept="image/jpeg,image/png,image/webp,application/pdf" 
                       @change="handleFileChange($event)"
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
                <a href="{{ route('guru.prestasi.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
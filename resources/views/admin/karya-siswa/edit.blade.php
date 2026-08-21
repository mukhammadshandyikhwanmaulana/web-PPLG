@extends('layouts.admin.app')

@section('title', 'Edit Karya Siswa')

@section('content')
<div class="max-w-4xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8 space-y-5">

    {{-- Header & Navigasi Kembali --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.karya-siswa.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 hover:text-indigo-600 font-medium mb-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Karya
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Edit Karya Siswa</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Perbarui informasi dan kelola galeri gambar karya siswa.</p>
        </div>
    </div>

    {{-- Form Container Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('admin.karya-siswa.update', $studentWork) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Include Form Input Utama --}}
            @include('admin.karya-siswa._form', ['studentWork' => $studentWork])

            {{-- Galeri Gambar Saat Ini --}}
            @if ($studentWork->galleries && $studentWork->galleries->isNotEmpty())
                <div class="pt-4 border-t border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-800">
                            Gambar Saat Ini ({{ $studentWork->galleries->count() }})
                        </label>
                        <span class="text-[11px] sm:text-xs text-gray-400">Centang kotak untuk menghapus gambar</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach ($studentWork->galleries as $gallery)
                            <div class="relative group bg-gray-50 border border-gray-200 rounded-xl overflow-hidden p-2 flex flex-col justify-between transition-all hover:border-gray-300">
                                <div class="w-full h-24 sm:h-28 overflow-hidden rounded-lg bg-gray-100 mb-2">
                                    @if ($gallery->media?->file_path)
                                        <img src="{{ Storage::url($gallery->media->file_path) }}" 
                                             alt="Gallery Item" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                                            Gambar hilang
                                        </div>
                                    @endif
                                </div>
                                <label class="flex items-center gap-2 px-1 py-1 cursor-pointer select-none text-xs text-rose-600 font-medium">
                                    <input type="checkbox" 
                                           name="remove_gallery_ids[]" 
                                           value="{{ $gallery->id }}" 
                                           @checked(in_array($gallery->id, old('remove_gallery_ids', [])))
                                           class="remove-gallery-checkbox rounded border-gray-300 text-rose-600 focus:ring-rose-500 w-4 h-4 cursor-pointer"
                                           onchange="toggleSubmitButtonState()">
                                    <span>Hapus Gambar</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('remove_gallery_ids')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.karya-siswa.index') }}" 
                   class="w-full sm:w-auto px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center transition-colors">
                    Batal
                </a>
                <button type="submit" id="submit-btn"
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-medium shadow-sm transition-colors">
                    <svg id="submit-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="submit-text">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Perubahan Dinamis Tombol CTA --}}
<script>
    function toggleSubmitButtonState() {
        const checkboxes = document.querySelectorAll('.remove-gallery-checkbox');
        const btn = document.getElementById('submit-btn');
        const btnText = document.getElementById('submit-text');
        const btnIcon = document.getElementById('submit-icon');

        const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);

        if (isAnyChecked) {
            btnText.textContent = 'Hapus';
            btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            btn.classList.add('bg-rose-600', 'hover:bg-rose-700');
            btnIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>';
        } else {
            btnText.textContent = 'Simpan Perubahan';
            btn.classList.remove('bg-rose-600', 'hover:bg-rose-700');
            btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            btnIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        }
    }

    document.addEventListener('DOMContentLoaded', toggleSubmitButtonState);
</script>
@endsection
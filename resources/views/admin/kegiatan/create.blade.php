@extends('layouts.admin.app')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-6 px-3 sm:px-6">
    <!-- Link Navigasi Kembali -->
    <div class="mb-3">
        <a href="{{ route('admin.kegiatan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Kegiatan
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Tambah Kegiatan</h1>
        <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Isi formulir di bawah ini untuk mengunggah dan Mempublikasikan kegiatan baru.</p>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.kegiatan.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Judul & Tanggal Kegiatan (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="Masukkan judul kegiatan..."
                           class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('title') border-red-500 @enderror">
                    @error('title') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="event_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}" required
                           class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('event_date') border-red-500 @enderror">
                    @error('event_date') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Deskripsi Kegiatan -->
            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="5" required
                          placeholder="Tuliskan deskripsi lengkap mengenai kegiatan ini..."
                          class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                @error('content') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Foto Utama -->
            <div>
                <label for="photo" class="block text-sm font-semibold text-gray-700 mb-1">Foto Utama Kegiatan</label>
                <p class="text-xs text-gray-500 mb-2">Opsional (Maksimal 2MB, format JPEG/PNG/WEBP)</p>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none @error('photo') border-red-500 @enderror">
                @error('photo') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Urutan Tampil -->
            <div>
                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil</label>
                <p class="text-xs text-gray-500 mb-2">Angka urutan posisi tampilan (misal: 0, 1, 2)</p>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full sm:w-48 text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('sort_order') border-red-500 @enderror">
                @error('sort_order') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-5 border-t border-gray-200 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
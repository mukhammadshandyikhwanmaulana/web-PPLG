@extends('layouts.admin.app')

@section('title', 'Edit Prestasi')

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-6 px-3 sm:px-6">
    <!-- Link Navigasi Kembali -->
    <div class="mb-3">
        <a href="{{ route('admin.prestasi.index') }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Prestasi
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Edit Prestasi</h1>
        <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Perbarui informasi data prestasi ini.</p>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form action="{{ route('admin.prestasi.update', $achievement) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Prestasi <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $achievement->title) }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contributor Name --}}
            <div>
                <label for="contributor_name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kontributor/Siswa</label>
                <input type="text" name="contributor_name" id="contributor_name" value="{{ old('contributor_name', $achievement->contributor_name) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('contributor_name') border-red-500 @enderror">
                @error('contributor_name')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Level --}}
                <div>
                    <label for="level" class="block text-sm font-semibold text-gray-700 mb-1.5">Tingkat / Level <span class="text-red-500">*</span></label>
                    <select name="level" id="level" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('level') border-red-500 @enderror" required>
                        <option value="">-- Pilih Level --</option>
                        @foreach(\App\Enums\AchievementLevel::cases() as $levelEnum)
                            <option value="{{ $levelEnum->value }}" {{ old('level', $achievement->level?->value) == $levelEnum->value ? 'selected' : '' }}>
                                {{ $levelEnum->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('level')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Achievement Date --}}
                <div>
                    <label for="achievement_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Prestasi <span class="text-red-500">*</span></label>
                    <input type="date" name="achievement_date" id="achievement_date" value="{{ old('achievement_date', $achievement->achievement_date?->format('Y-m-d')) }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('achievement_date') border-red-500 @enderror">
                    @error('achievement_date')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none @error('description') border-red-500 @enderror">{{ old('description', $achievement->description) }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Preview & Input --}}
            <div>
                <label for="document" class="block text-sm font-semibold text-gray-700 mb-1">Dokumen / Bukti</label>
                <p class="text-xs text-gray-500 mb-2">Kosongkan jika tidak ingin mengubah dokumen (Format JPEG, PNG, WebP, PDF — Maksimal 5MB)</p>

                @if($achievement->document)
                    <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Dokumen Saat Ini:</p>
                        @php
                            $filePath = $achievement->document->file_path;
                            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $isPdf = $extension === 'pdf';
                        @endphp

                        @if($isPdf)
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <a href="{{ Storage::url($filePath) }}" target="_blank" class="text-indigo-600 hover:underline font-medium break-all">
                                    Lihat Berkas PDF ({{ $achievement->document->file_name ?? basename($filePath) }})
                                </a>
                            </div>
                        @else
                            <div>
                                <img src="{{ Storage::url($filePath) }}" alt="Preview" class="w-32 h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                            </div>
                        @endif
                    </div>
                @endif

                <input type="file" name="document" id="document" accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none @error('document') border-red-500 @enderror">
                @error('document')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white @error('status') border-red-500 @enderror" required>
                    @foreach(\App\Enums\PublishStatus::cases() as $statusEnum)
                        <option value="{{ $statusEnum->value }}" {{ old('status', $achievement->status?->value) == $statusEnum->value ? 'selected' : '' }}>
                            {{ ucfirst($statusEnum->value) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="pt-5 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.prestasi.index') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 text-sm">
                    Perbarui Prestasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
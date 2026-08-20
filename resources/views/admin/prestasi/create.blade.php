@extends('layouts.admin.app')

@section('title', 'Tambah Prestasi')

@section('content')
    <div class="max-w-2xl mx-auto my-3 sm:my-6 px-4 py-5 sm:py-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-5 sm:mb-6">Tambah Prestasi</h1>

        <form method="POST" action="{{ route('admin.prestasi.store') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="4" 
                          class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Prestasi</label>
                    <input type="date" name="achievement_date" value="{{ old('achievement_date') }}"
                           class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('achievement_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <select name="level" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                        <option value="">-- Pilih Level --</option>
                        @foreach (\App\Enums\AchievementLevel::cases() as $level)
                            <option value="{{ $level->value }}" @selected(old('level') === $level->value)>
                                {{ $level->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('level') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kontributor</label>
                <input type="text" name="contributor_name" value="{{ old('contributor_name') }}"
                       class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('contributor_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen Bukti (JPG/PNG/WEBP/PDF, maks 5MB)</label>
                <input type="file" name="document" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('document') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                    <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status') === 'published')>Published</option>
                </select>
                @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-3">
                <a href="{{ route('admin.prestasi.index') }}" 
                   class="w-full sm:w-auto text-center px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 sm:border-0 rounded-md">Batal</a>
                <button type="submit" 
                        class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
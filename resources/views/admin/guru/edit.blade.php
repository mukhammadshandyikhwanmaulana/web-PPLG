@extends('layouts.admin.app')

@section('title', 'Edit Akun Guru')

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-6 px-3 sm:px-6">
    <!-- Link Navigasi Kembali -->
    <div class="mb-3">
        <a href="{{ route('admin.guru.index') }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Guru
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Edit Akun Guru</h1>
        <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Perbarui informasi data akun dan profil guru ini.</p>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form action="{{ route('admin.guru.update', $guru) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Subheader Akun -->
            <div class="pb-2 border-b border-gray-100">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kredensial Akun</h2>
            </div>

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $guru->name) }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $guru->email) }}" required class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru <span class="text-gray-400 text-xs font-normal">(opsional)</span></label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak diubah..." class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru..." class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>
            </div>

            {{-- Akun Is Active --}}
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $guru->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-gray-700">Akun dapat login ke sistem</label>
            </div>

            <!-- Subheader Profil Publik -->
            <div class="pt-4 pb-2 border-b border-gray-100">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Profil Publik</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Position --}}
                <div>
                    <label for="position" class="block text-sm font-semibold text-gray-700 mb-1.5">Jabatan / Peran</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $guru->staffMember?->position) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('position') border-red-500 @enderror">
                    @error('position')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Expertise --}}
                <div>
                    <label for="expertise" class="block text-sm font-semibold text-gray-700 mb-1.5">Keahlian / Bidang</label>
                    <input type="text" name="expertise" id="expertise" value="{{ old('expertise', $guru->staffMember?->expertise) }}" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('expertise') border-red-500 @enderror">
                    @error('expertise')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Foto Preview & Input --}}
            <div>
                <label for="photo" class="block text-sm font-semibold text-gray-700 mb-1">Ganti Foto Profil</label>
                <p class="text-xs text-gray-500 mb-2">Kosongkan jika tidak ingin mengubah foto (Format JPEG, PNG, WebP — Maksimal 2MB)</p>

                @if($guru->staffMember?->photo)
                    <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Foto Saat Ini:</p>
                        <img src="{{ Storage::url($guru->staffMember->photo->file_path) }}" alt="{{ $guru->name }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200 shadow-sm">
                    </div>
                @endif

                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50 focus:outline-none @error('photo') border-red-500 @enderror">
                @error('photo')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1.5">Urutan Tampil</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $guru->staffMember?->sort_order ?? 0) }}" min="0" class="w-full text-sm border border-gray-300 rounded-lg px-3.5 py-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition @error('sort_order') border-red-500 @enderror">
                @error('sort_order')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Staff Is Active --}}
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="staff_is_active" id="staff_is_active" value="1" {{ old('staff_is_active', $guru->staffMember?->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="staff_is_active" class="text-sm font-medium text-gray-700">Tampilkan profil di halaman website publik</label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-5 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.guru.index') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition duration-150 text-sm">
                    Perbarui Akun Guru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
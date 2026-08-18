@extends('layouts.admin.app')
@section('title', 'Edit Akun Guru')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Edit Akun Guru</h1>
    <form method="POST" action="{{ route('admin.guru.update', $guru) }}" enctype="multipart/form-data" class="bg-white rounded-lg border p-6 max-w-lg space-y-4">
        @csrf
        @method('PUT')

        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide pt-2">Akun</h2>

        <div>
            <label for="name" class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name', $guru->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $guru->email) }}" required class="w-full border rounded px-3 py-2 text-sm">
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password Baru <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 text-sm">
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $guru->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
            <label for="is_active" class="text-sm">Akun dapat login</label>
        </div>

        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide pt-4 border-t">Profil Publik</h2>

        @if ($guru->staffMember?->photo)
            <div>
                <span class="block text-sm font-medium mb-1">Foto Saat Ini</span>
                <img src="{{ Storage::url($guru->staffMember->photo->file_path) }}" alt="{{ $guru->name }}" class="w-16 h-16 rounded-full object-cover">
            </div>
        @endif

        <div>
            <label for="position" class="block text-sm font-medium mb-1">Jabatan</label>
            <input type="text" name="position" id="position" value="{{ old('position', $guru->staffMember?->position) }}" class="w-full border rounded px-3 py-2 text-sm">
            @error('position') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="expertise" class="block text-sm font-medium mb-1">Keahlian</label>
            <input type="text" name="expertise" id="expertise" value="{{ old('expertise', $guru->staffMember?->expertise) }}" class="w-full border rounded px-3 py-2 text-sm">
            @error('expertise') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="photo" class="block text-sm font-medium mb-1">Ganti Foto</label>
            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="w-full border rounded px-3 py-2 text-sm">
            @error('photo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium mb-1">Urutan Tampil</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $guru->staffMember?->sort_order ?? 0) }}" min="0" class="w-full border rounded px-3 py-2 text-sm">
            @error('sort_order') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="staff_is_active" id="staff_is_active" value="1" {{ old('staff_is_active', $guru->staffMember?->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
            <label for="staff_is_active" class="text-sm">Tampilkan di halaman publik</label>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Perbarui</button>
            <a href="{{ route('admin.guru.index') }}" class="text-sm px-4 py-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
@endsection
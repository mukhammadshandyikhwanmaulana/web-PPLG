@extends('layouts.admin.app')
@section('title', 'Tambah Akun Guru')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Tambah Akun Guru</h1>
    <form method="POST" action="{{ route('admin.guru.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg border p-6 max-w-lg space-y-4">
        @csrf

        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide pt-2">Akun</h2>

        <div>
            <label for="name" class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2 text-sm">
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2 text-sm">
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" id="password" required class="w-full border rounded px-3 py-2 text-sm">
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-gray-300">
            <label for="is_active" class="text-sm">Akun dapat login</label>
        </div>

        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide pt-4 border-t">Profil Publik</h2>

        <div>
            <label for="position" class="block text-sm font-medium mb-1">Jabatan</label>
            <input type="text" name="position" id="position" value="{{ old('position') }}" class="w-full border rounded px-3 py-2 text-sm">
            @error('position') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="expertise" class="block text-sm font-medium mb-1">Keahlian</label>
            <input type="text" name="expertise" id="expertise" value="{{ old('expertise') }}" class="w-full border rounded px-3 py-2 text-sm">
            @error('expertise') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="photo" class="block text-sm font-medium mb-1">Foto</label>
            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="w-full border rounded px-3 py-2 text-sm">
            @error('photo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium mb-1">Urutan Tampil</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full border rounded px-3 py-2 text-sm">
            @error('sort_order') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="staff_is_active" id="staff_is_active" value="1" checked class="rounded border-gray-300">
            <label for="staff_is_active" class="text-sm">Tampilkan di halaman publik</label>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Simpan</button>
            <a href="{{ route('admin.guru.index') }}" class="text-sm px-4 py-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
@endsection
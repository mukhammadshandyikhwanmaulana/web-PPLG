@extends('layouts.admin.app')
@section('title', 'Edit Akun Guru')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Edit Akun Guru</h1>
    <form method="POST" action="{{ route('admin.guru.update', $guru) }}" class="bg-white rounded-lg border p-6 max-w-lg space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name', $guru->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $guru->email) }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password Baru <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $guru->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
            <label for="is_active" class="text-sm">Akun aktif</label>
        </div>
        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Perbarui</button>
            <a href="{{ route('admin.guru.index') }}" class="text-sm px-4 py-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
@endsection
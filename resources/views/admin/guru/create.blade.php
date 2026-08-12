@extends('layouts.admin.app')
@section('title', 'Tambah Akun Guru')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Tambah Akun Guru</h1>
    <form method="POST" action="{{ route('admin.guru.store') }}" class="bg-white rounded-lg border p-6 max-w-lg space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" id="password" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-gray-300">
            <label for="is_active" class="text-sm">Akun aktif</label>
        </div>
        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Simpan</button>
            <a href="{{ route('admin.guru.index') }}" class="text-sm px-4 py-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
@endsection
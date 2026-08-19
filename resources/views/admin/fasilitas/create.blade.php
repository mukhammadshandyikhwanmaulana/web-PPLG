@extends('layouts.admin.app')
@section('title', 'Tambah Fasilitas')
@section('content')
    <h1 class="text-xl font-semibold mb-6">Tambah Fasilitas</h1>
    <form method="POST" action="{{ route('admin.fasilitas.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg border p-6 max-w-lg space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2 text-sm">
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="description" class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded px-3 py-2 text-sm">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
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
        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Simpan</button>
            <a href="{{ route('admin.fasilitas.index') }}" class="text-sm px-4 py-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
@endsection
@extends('layouts.admin.app')
@section('title', 'Manajemen Fasilitas')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Manajemen Fasilitas</h1>
        <a href="{{ route('admin.fasilitas.create') }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">+ Tambah Fasilitas</a>
    </div>

    <form method="GET" action="{{ route('admin.fasilitas.index') }}" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama fasilitas..." class="border rounded px-3 py-2 text-sm w-full max-w-sm">
    </form>

    <div class="bg-white rounded-lg border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-2">Foto</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Urutan</th>
                    <th class="px-4 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($facilities as $item)
                    <tr>
                        <td class="px-4 py-2">
                            @if ($item->photo)
                                <img src="{{ Storage::url($item->photo->file_path) }}" alt="{{ $item->name }}" class="w-10 h-10 rounded object-cover">
                            @else
                                <span class="inline-flex w-10 h-10 rounded bg-gray-100 items-center justify-center text-gray-400 text-xs">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $item->sort_order }}</td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('admin.fasilitas.edit', $item) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.fasilitas.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus fasilitas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada data Fasilitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $facilities->links() }}</div>
@endsection
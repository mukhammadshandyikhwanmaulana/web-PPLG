@extends('layouts.admin.app')
@section('title', 'Manajemen Akun Guru')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Manajemen Akun Guru</h1>
        <a href="{{ route('admin.guru.create') }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-800">+ Tambah Guru</a>
    </div>

    <div class="bg-white rounded-lg border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($guru as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2">{{ $item->email }}</td>
                        <td class="px-4 py-2">
                            @if ($item->is_active)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('admin.guru.edit', $item) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.guru.destroy', $item) }}" class="inline" onsubmit="return confirm('Nonaktifkan akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Nonaktifkan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada akun Guru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $guru->links() }}</div>
@endsection
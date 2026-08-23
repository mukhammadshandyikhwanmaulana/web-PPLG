@extends('layouts.admin.app')

@section('title', 'Manajemen Kegiatan')

@section('content')
<div class="w-full max-w-7xl mx-auto py-4 sm:py-6 px-3 sm:px-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Kegiatan</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Kelola dan publikasikan seluruh dokumentasi kegiatan sekolah.</p>
        </div>
        <div>
            <a href="{{ route('admin.kegiatan.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kegiatan
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-3.5 sm:p-4 rounded-xl shadow-sm border border-gray-200 mb-5">
        <form method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul kegiatan..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
            </div>

            <div class="w-full md:w-48">
                <select name="status" class="w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-white">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\PublishStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ ucfirst($status->value) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.kegiatan.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 underline text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TAMPILAN HP / MOBILE -->
    <div class="block md:hidden space-y-3 mb-5">
        @forelse ($activities as $item)
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    @if ($item->cover)
                        <img src="{{ Storage::url($item->cover->file_path) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 shrink-0">
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400 font-medium shrink-0">
                            N/A
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            @if($item->status->value === 'published')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Draft
                                </span>
                            @endif
                            <span class="text-xs text-gray-400 font-medium">Galeri: {{ $item->galleries->count() }}/8</span>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug">{{ $item->title }}</h2>
                        <p class="text-xs text-gray-500 mt-1">📅 {{ $item->event_date->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.kegiatan.edit', $item) }}"
                       class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-medium rounded-lg transition">
                        Edit
                    </a>
                    <form action="{{ route('admin.kegiatan.destroy', $item) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 text-xs font-medium rounded-lg transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500 text-sm">
                Belum ada data kegiatan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                    <th class="py-3.5 px-4 w-20">Cover</th>
                    <th class="py-3.5 px-4">Judul</th>
                    <th class="py-3.5 px-4 w-36">Tanggal</th>
                    <th class="py-3.5 px-4 w-24">Galeri</th>
                    <th class="py-3.5 px-4 w-32">Status</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse ($activities as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3 px-4">
                            @if ($item->cover)
                                <img src="{{ Storage::url($item->cover->file_path) }}" alt="{{ $item->title }}" class="w-14 h-10 object-cover rounded-md border border-gray-200 shadow-sm">
                            @else
                                <div class="w-14 h-10 bg-gray-100 rounded-md border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400 font-medium">
                                    N/A
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-gray-900">
                            {{ $item->title }}
                        </td>
                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                            {{ $item->event_date->format('d M Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $item->galleries->count() }}/8
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($item->status->value === 'published')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.kegiatan.edit', $item) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.kegiatan.destroy', $item) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center text-gray-500">
                            Belum ada data kegiatan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($activities->hasPages())
            <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

    @if ($activities->hasPages())
        <div class="block md:hidden mt-3">
            {{ $activities->links() }}
        </div>
    @endif
</div>
@endsection
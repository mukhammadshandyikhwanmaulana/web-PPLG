@extends('layouts.admin.app')

@section('title', 'Manajemen Fasilitas')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Fasilitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola daftar fasilitas sekolah, urutan tampilan, dan foto pendukung.</p>
        </div>
        <div>
            <a href="{{ route('admin.fasilitas.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Fasilitas
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form method="GET" action="{{ route('admin.fasilitas.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama fasilitas..."
                       class="w-full pl-10 pr-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition">
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-xs transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>

                @if(request('search'))
                    <a href="{{ route('admin.fasilitas.index') }}" class="inline-flex items-center justify-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TAMPILAN HP / MOBILE -->
    <div class="block md:hidden space-y-3">
        @forelse ($facilities as $item)
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    @if ($item->photo)
                        <img src="{{ Storage::url($item->photo->file_path) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shrink-0">
                    @else
                        <div class="w-16 h-16 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400 font-medium shrink-0">
                            No Photo
                        </div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Urutan #{{ $item->sort_order }}
                            </span>
                        </div>
                        <h2 class="text-sm font-semibold text-slate-900 line-clamp-2 leading-snug">{{ $item->name }}</h2>
                        @if($item->description)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $item->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.fasilitas.edit', $item) }}"
                       class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-medium rounded-lg transition">
                        Edit
                    </a>
                    <form action="{{ route('admin.fasilitas.destroy', $item) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-medium rounded-lg transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Belum ada data fasilitas yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-20">Foto</th>
                    <th class="py-3.5 px-4">Nama Fasilitas</th>
                    <th class="py-3.5 px-4 w-28 text-center">Urutan</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($facilities as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4">
                            @if ($item->photo)
                                <img src="{{ Storage::url($item->photo->file_path) }}" alt="{{ $item->name }}" class="w-14 h-10 object-cover rounded-xl border border-slate-200 shadow-xs">
                            @else
                                <div class="w-14 h-10 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400 font-medium">
                                    No Photo
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $item->name }}
                            @if($item->description)
                                <div class="text-xs text-slate-500 font-normal line-clamp-1 mt-0.5">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                #{{ $item->sort_order }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.fasilitas.edit', $item) }}"
                                   class="text-blue-600 hover:text-blue-900 font-medium transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.fasilitas.destroy', $item) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 px-4 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Belum ada data fasilitas yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($facilities->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>

    @if ($facilities->hasPages())
        <div class="block md:hidden mt-3">
            {{ $facilities->links() }}
        </div>
    @endif
</div>
@endsection
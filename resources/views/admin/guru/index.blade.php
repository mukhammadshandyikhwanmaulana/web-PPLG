@extends('layouts.admin.app')

@section('title', 'Manajemen Akun Guru')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Guru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola akun login dan profil publik seluruh tenaga pengajar.</p>
        </div>
        <div>
            <a href="{{ route('admin.guru.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Guru
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form method="GET" action="{{ route('admin.guru.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau jabatan..."
                       class="w-full pl-10 pr-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition">
            </div>

            <div class="w-full md:w-48">
                <select name="status" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">
                    <option value="">Semua Status Akun</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-xs transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center justify-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150">
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
        @forelse ($guru as $item)
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    @if ($item->staffMember?->photo)
                        <img src="{{ Storage::url($item->staffMember->photo->file_path) }}" alt="{{ $item->name }}" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shrink-0">
                    @else
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl border border-slate-200 flex items-center justify-center font-semibold text-sm shrink-0">
                            {{ strtoupper(substr($item->name, 0, 2)) }}
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            @if($item->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Akun Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Nonaktif
                                </span>
                            @endif

                            @if($item->staffMember?->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    Profil Tampil
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Profil Sembunyi
                                </span>
                            @endif
                        </div>
                        <h2 class="text-sm font-semibold text-slate-900 line-clamp-1 leading-snug">{{ $item->name }}</h2>
                        <p class="text-xs text-slate-500 font-medium truncate">{{ $item->staffMember?->position ?? '—' }}</p>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">{{ $item->email }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs text-slate-500">
                    <span>Urutan: #{{ $item->staffMember?->sort_order ?? 0 }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.guru.edit', $item) }}"
                           class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-medium rounded-lg transition">
                            Edit
                        </a>
                        <form action="{{ route('admin.guru.destroy', $item) }}" method="POST" class="inline"
                              onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan akun guru ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-medium rounded-lg transition">
                                Nonaktifkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Belum ada data guru yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-16">Foto</th>
                    <th class="py-3.5 px-4">Guru / Staf</th>
                    <th class="py-3.5 px-4">Jabatan & Keahlian</th>
                    <th class="py-3.5 px-4">Email</th>
                    <th class="py-3.5 px-4 w-28">Status Akun</th>
                    <th class="py-3.5 px-4 w-28">Profil Publik</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($guru as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4">
                            @if ($item->staffMember?->photo)
                                <img src="{{ Storage::url($item->staffMember->photo->file_path) }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-xs">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl border border-slate-200 flex items-center justify-center font-semibold text-xs shadow-xs">
                                    {{ strtoupper(substr($item->name, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            <div>{{ $item->name }}</div>
                            <div class="text-xs text-slate-400 font-normal">Urutan: #{{ $item->staffMember?->sort_order ?? 0 }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-slate-900 font-medium">{{ $item->staffMember?->position ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $item->staffMember?->expertise ?? 'Belum diatur' }}</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                            {{ $item->email }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if ($item->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if ($item->staffMember?->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    Tampil
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    Sembunyi
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.guru.edit', $item) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.guru.destroy', $item) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan akun guru ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium transition">
                                        Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Belum ada data guru yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($guru->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                {{ $guru->links() }}
            </div>
        @endif
    </div>

    <!-- Pagination versi Mobile -->
    @if ($guru->hasPages())
        <div class="mt-3 block md:hidden">
            {{ $guru->links() }}
        </div>
    @endif
</div>
@endsection
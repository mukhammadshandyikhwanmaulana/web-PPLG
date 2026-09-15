@extends('layouts.admin.app')

@section('title', 'Manajemen Akun Guru')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4"
     x-data="{
         previewOpen: false,
         previewPhoto: '',
         previewTitle: 'Preview Foto Guru',
         openPreview(photo, title) {
             if (!photo) return;
             this.previewPhoto = photo;
             this.previewTitle = title || 'Preview Foto Guru';
             this.previewOpen = true;
         }
     }"
     @keydown.escape.window="previewOpen = false">

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
        <form id="search-form" method="GET" action="{{ route('admin.guru.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            {{-- Input Search --}}
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau jabatan..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.guru.index', request()->except('search')) }}" 
                       title="Hapus kata kunci pencarian"
                       class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Dropdown Status Akun --}}
            <div class="w-full md:w-48">
                <select name="status" onchange="document.getElementById('search-form').submit()" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Status Akun</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>

            {{-- Tombol Reset --}}
            @php $hasFilter = request()->anyFilled(['search', 'status']); @endphp

            @if($hasFilter)
                <a href="{{ route('admin.guru.index') }}" 
                   class="flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN HP / MOBILE -->
    <div class="block md:hidden space-y-3">
        @forelse ($guru as $item)
            @php
                $photoObj = $item->staffMember?->photo;
                $photoPath = $photoObj?->path ?? $photoObj?->file_path;
                $diskName = $photoObj?->disk ?? 'public';
                $staffPhotoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::disk($diskName)->url(ltrim($photoPath, '/')) : null;
                
                $photoUrl = $item->avatar_url ?? $staffPhotoUrl;

                $rawPosition = $item->staffMember?->position;
                $displayPosition = is_array($rawPosition) ? implode(', ', $rawPosition) : ($rawPosition ?? '—');
            @endphp
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    @if ($photoUrl)
                        <div class="relative group cursor-pointer shrink-0 rounded-xl overflow-hidden border border-slate-200"
                             @click="openPreview('{{ $photoUrl }}', '{{ addslashes($item->name) }}')">
                            <img src="{{ $photoUrl }}" alt="{{ $item->name }}" class="w-14 h-14 object-cover bg-slate-50">
                            <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl border border-slate-200 flex items-center justify-center font-semibold text-sm shrink-0">
                            {{ strtoupper(substr($item->name, 0, 2)) }}
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="text-sm font-semibold text-slate-900 line-clamp-1 leading-snug">{{ $item->name }}</h2>
                            <div class="shrink-0 text-[11px] font-semibold pt-0.5">
                                @if($item->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5">{{ $displayPosition }}</p>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">{{ $item->email }}</p>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.guru.edit', $item) }}"
                       class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                        Edit
                    </a>
                    <form action="{{ route('admin.guru.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                          onsubmit="return confirm('Apakah Anda yakin ingin {{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun guru ini?')">
                        @csrf
                        @method('DELETE')
                        @if ($item->is_active)
                            <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                                Nonaktifkan
                            </button>
                        @else
                            <button type="submit" class="inline-flex items-center text-xs font-semibold text-slate-700 hover:text-slate-900 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                                Aktifkan
                            </button>
                        @endif
                    </form>
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
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-16">Foto</th>
                    <th class="py-3.5 px-4">Guru / Staf</th>
                    <th class="py-3.5 px-4">Jabatan & Keahlian</th>
                    <th class="py-3.5 px-4">Email</th>
                    <th class="py-3.5 px-4 w-32">Status Akun</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($guru as $item)
                    @php
                        $photoObj = $item->staffMember?->photo;
                        $photoPath = $photoObj?->path ?? $photoObj?->file_path;
                        $diskName = $photoObj?->disk ?? 'public';
                        $staffPhotoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::disk($diskName)->url(ltrim($photoPath, '/')) : null;

                        $photoUrl = $item->avatar_url ?? $staffPhotoUrl;

                        $rawPosition = $item->staffMember?->position;
                        $displayPosition = is_array($rawPosition) ? implode(', ', $rawPosition) : ($rawPosition ?? '—');
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4">
                            @if ($photoUrl)
                                <div class="relative group cursor-pointer w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-50 p-0.5"
                                     @click="openPreview('{{ $photoUrl }}', '{{ addslashes($item->name) }}')">
                                    <img src="{{ $photoUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                        <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl border border-slate-200 flex items-center justify-center font-semibold text-xs shadow-xs">
                                    {{ strtoupper(substr($item->name, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $item->name }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-slate-900 font-medium">{{ $displayPosition }}</div>
                            <div class="text-xs text-slate-500">{{ $item->staffMember?->expertise ?? 'Belum diatur' }}</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                            {{ $item->email }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if ($item->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.guru.edit', $item) }}"
                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                                    Edit
                                </a>
                                <form action="{{ route('admin.guru.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin {{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun guru ini?')">
                                    @csrf
                                    @method('DELETE')
                                    @if ($item->is_active)
                                        <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center text-xs font-semibold text-slate-700 hover:text-slate-900 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center text-slate-500">
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

    <!-- MODAL PREVIEW FOTO (LIGHTBOX) -->
    <div x-show="previewOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs"
         x-cloak>
        
        <div class="relative max-w-lg w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col"
             @click.away="previewOpen = false">
            
            <div class="flex items-center justify-between p-4 border-b border-slate-800 text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold" x-text="previewTitle"></span>
                </div>
                <button type="button" @click="previewOpen = false" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex items-center justify-center p-6 bg-white/95">
                <img :src="previewPhoto" class="max-h-[50vh] max-w-full object-contain rounded-lg">
            </div>
        </div>
    </div>
</div>

<script>
    let searchTimer;

    function handleAutoSearch() {
        clearTimeout(searchTimer);

        const input = document.getElementById('search-input');
        const urlParams = new URLSearchParams(window.location.search);
        const hasSearchParam = urlParams.has('search') && urlParams.get('search') !== '';

        if (input.value.trim() === '' && !hasSearchParam) {
            return;
        }

        searchTimer = setTimeout(() => {
            sessionStorage.setItem('guru_search_focus', 'true');
            document.getElementById('search-form').submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        
        if (searchInput && sessionStorage.getItem('guru_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('guru_search_focus');
        }
    });
</script>
@endsection
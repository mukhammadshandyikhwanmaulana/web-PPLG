@extends('layouts.admin.app')

@section('title', 'Manajemen Mitra')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4"
     x-data="{
         previewOpen: false,
         previewPhoto: '',
         previewTitle: 'Preview Logo Mitra',
         openPreview(photo, title) {
             if (!photo) return;
             this.previewPhoto = photo;
             this.previewTitle = title || 'Preview Logo Mitra';
             this.previewOpen = true;
         }
     }"
     @keydown.escape.window="previewOpen = false">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Mitra Industri</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data mitra industri yang akan ditampilkan ke publik.</p>
        </div>
        <div>
            <a href="{{ route('admin.mitra.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Mitra
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form id="search-form" method="GET" action="{{ route('admin.mitra.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari nama mitra..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.mitra.index', request()->except('search')) }}" 
                       title="Hapus kata kunci pencarian"
                       class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>

            <div class="w-full md:w-48">
                <select name="status" onchange="document.getElementById('search-form').submit()" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\PublishStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ method_exists($status, 'label') ? $status->label() : ucfirst($status->value) }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php 
                $hasFilter = request()->anyFilled(['search', 'status']); 
            @endphp

            @if($hasFilter)
                <a href="{{ route('admin.mitra.index') }}" 
                   class="flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150 shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN HP / MOBILE -->
    <div class="block md:hidden space-y-3">
        @forelse ($partners as $item)
            @php
                $isPublished = ($item->status === \App\Enums\PublishStatus::Published) || ($item->status?->value === \App\Enums\PublishStatus::Published->value) || ($item->status === 'published');
                
                $logoUrl = $item->logo_url;
                if (!$logoUrl && $item->logo_path) {
                    $logoUrl = \Illuminate\Support\Facades\Storage::disk($item->disk ?? 'public')->url($item->logo_path);
                } elseif (!$logoUrl && isset($item->media) && $item->media?->path) {
                    $logoUrl = \Illuminate\Support\Facades\Storage::disk($item->media->disk ?? 'public')->url($item->media->path);
                }
            @endphp
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    @if ($logoUrl)
                        <div class="relative group cursor-pointer shrink-0 rounded-xl overflow-hidden border border-slate-200"
                             @click="openPreview(@js($logoUrl), @js($item->name))">
                            <img src="{{ $logoUrl }}" alt="{{ $item->name }}" class="w-16 h-16 object-contain bg-slate-50 p-1">
                            <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="w-16 h-16 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400 font-medium shrink-0">
                            No Logo
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h2 class="text-sm font-semibold text-slate-900 line-clamp-2 leading-snug">{{ $item->name }}</h2>

                            @if($isPublished)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Published</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">Draft</span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-500 mt-1">
                            @if ($item->website_url)
                                <a href="{{ $item->website_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline inline-flex items-center gap-1 truncate max-w-full">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span class="truncate">{{ $item->website_url }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 005.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    Tanpa Website
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <div class="text-xs text-slate-500 font-medium">
                        Urutan: <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $item->sort_order ?? '-' }}</span>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('admin.mitra.edit', $item) }}"
                           class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                            Edit
                        </a>
                        <form action="{{ route('admin.mitra.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition leading-none p-0 border-0 bg-transparent cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Belum ada data mitra yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[750px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 w-20">Logo</th>
                    <th class="py-3.5 px-4">Nama Mitra</th>
                    <th class="py-3.5 px-4">Website</th>
                    <th class="py-3.5 px-4 w-24 text-center">Urutan</th>
                    <th class="py-3.5 px-4 w-28 text-center">Status</th>
                    <th class="py-3.5 px-4 w-36 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($partners as $item)
                    @php
                        $isPublished = ($item->status === \App\Enums\PublishStatus::Published) || ($item->status?->value === \App\Enums\PublishStatus::Published->value) || ($item->status === 'published');
                        
                        $logoUrl = $item->logo_url;
                        if (!$logoUrl && $item->logo_path) {
                            $logoUrl = \Illuminate\Support\Facades\Storage::disk($item->disk ?? 'public')->url($item->logo_path);
                        } elseif (!$logoUrl && isset($item->media) && $item->media?->path) {
                            $logoUrl = \Illuminate\Support\Facades\Storage::disk($item->media->disk ?? 'public')->url($item->media->path);
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ method_exists($partners, 'firstItem') && $partners->firstItem() ? $partners->firstItem() + $loop->index : $loop->iteration }}
                        </td>
                        <td class="py-3 px-4">
                            @if ($logoUrl)
                                <div class="relative group cursor-pointer w-14 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-50 p-0.5"
                                     @click="openPreview(@js($logoUrl), @js($item->name))">
                                    <img src="{{ $logoUrl }}" alt="{{ $item->name }}" class="w-full h-full object-contain">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                        <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div class="w-14 h-10 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400 font-medium">
                                    No Logo
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $item->name }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            @if ($item->website_url)
                                <a href="{{ $item->website_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline inline-flex items-center gap-1 truncate max-w-xs">
                                    <span class="truncate">{{ $item->website_url }}</span>
                                    <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-1.5 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600 font-mono" title="Urutan Tampil">
                                {{ $item->sort_order ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($isPublished)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">Draft</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.mitra.edit', $item) }}"
                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                                    Edit
                                </a>
                                <form action="{{ route('admin.mitra.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition leading-none p-0 border-0 bg-transparent cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Belum ada data mitra yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (method_exists($partners, 'hasPages') && $partners->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                {{ $partners->links() }}
            </div>
        @endif
    </div>

    @if (method_exists($partners, 'hasPages') && $partners->hasPages())
        <div class="mt-3 block md:hidden">
            {{ $partners->links() }}
        </div>
    @endif

    <!-- MODAL PREVIEW LOGO (LIGHTBOX) -->
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
            
            <div class="flex items-center justify-between p-4 border-b border-slate-800 text-white shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold truncate" x-text="previewTitle"></span>
                </div>
                <button type="button" @click="previewOpen = false" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800 shrink-0 cursor-pointer">
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
            sessionStorage.setItem('mitra_search_focus', 'true');
            document.getElementById('search-form').submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        
        if (searchInput && sessionStorage.getItem('mitra_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('mitra_search_focus');
        }
    });
</script>
@endsection
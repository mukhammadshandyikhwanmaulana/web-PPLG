@extends('layouts.admin.app')

@section('title', 'Manajemen Kegiatan')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4"
     x-data="{
         previewOpen: false,
         previewPhotos: [],
         currentIndex: 0,
         previewTitle: 'Preview Foto',
         openPreview(photos, title = 'Preview Foto Galeri') {
             if (!photos || photos.length === 0) return;
             this.previewPhotos = Array.isArray(photos) ? photos : [photos];
             this.previewTitle = title;
             this.currentIndex = 0;
             this.previewOpen = true;
         },
         nextPhoto() {
             if (this.currentIndex < this.previewPhotos.length - 1) {
                 this.currentIndex++;
             } else {
                 this.currentIndex = 0;
             }
         },
         prevPhoto() {
             if (this.currentIndex > 0) {
                 this.currentIndex--;
             } else {
                 this.currentIndex = this.previewPhotos.length - 1;
             }
         }
     }"
     @keydown.escape.window="previewOpen = false">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kegiatan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola dan publikasikan seluruh dokumentasi kegiatan sekolah.</p>
        </div>
        <div>
            <a href="{{ route('admin.kegiatan.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kegiatan
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form id="search-form" method="GET" action="{{ route('admin.kegiatan.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari judul kegiatan..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.kegiatan.index', request()->except('search')) }}" 
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

            @php $hasFilter = request()->anyFilled(['search', 'status']); @endphp
            @if($hasFilter)
                <a href="{{ route('admin.kegiatan.index') }}" 
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
        @forelse ($activities as $item)
            @php
                $coverUrl = $item->cover_url ?? ($item->cover?->path ? \Illuminate\Support\Facades\Storage::disk($item->cover->disk ?? 'public')->url(ltrim($item->cover->path, '/')) : null);
                
                $galleryPhotos = $item->galleries ? $item->galleries->map(function($g) {
                    $m = $g->media ?? $g;
                    return $m->url ?? ($m->path ? \Illuminate\Support\Facades\Storage::disk($m->disk ?? 'public')->url(ltrim($m->path, '/')) : null);
                })->filter()->values() : collect([]);

                $isPublished = ($item->status === \App\Enums\PublishStatus::Published) || ($item->status?->value === \App\Enums\PublishStatus::Published->value);
                
                $cleanContent = $item->content ? trim(strip_tags($item->content)) : null;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    @if ($coverUrl)
                        <div class="relative group cursor-pointer shrink-0 rounded-xl overflow-hidden border border-slate-200" 
                             @click="openPreview('{{ $coverUrl }}', 'Preview Foto Cover Kegiatan')">
                            <img src="{{ $coverUrl }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-slate-950/40 flex flex-col items-center justify-center text-white text-[9px] font-bold gap-0.5 transition opacity-80 group-hover:opacity-100">
                                <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span class="leading-none">Cover</span>
                            </div>
                        </div>
                    @else
                        <div class="w-16 h-16 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400 font-medium shrink-0">
                            No Cover
                        </div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h2 class="text-sm font-semibold text-slate-900 line-clamp-2 leading-snug">{{ $item->title }}</h2>

                            @if($isPublished)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Published</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">Draft</span>
                            @endif
                        </div>

                        {{-- DESKRIPSI KEGIATAN DI MOBILE --}}
                        @if($cleanContent)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $cleanContent }}
                            </p>
                        @else
                            <p class="text-xs text-slate-400 italic mt-1">
                                Belum ada deskripsi.
                            </p>
                        @endif

                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ $item->event_date ? (is_string($item->event_date) ? \Carbon\Carbon::parse($item->event_date)->format('d M Y') : $item->event_date->format('d M Y')) : '-' }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <div>
                        @if(count($galleryPhotos) > 0)
                            <button type="button" 
                                    @click='openPreview(@json($galleryPhotos), "Preview Galeri Dokumentasi Kegiatan")'
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition border border-slate-200 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Galeri ({{ count($galleryPhotos) }})</span>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('admin.kegiatan.edit', $item) }}"
                           class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                            Edit
                        </a>
                        <form action="{{ route('admin.kegiatan.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Belum ada data kegiatan yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[850px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 w-20">Cover</th>
                    <th class="py-3.5 px-4">Judul & Deskripsi Kegiatan</th>
                    <th class="py-3.5 px-4 w-36">Tanggal</th>
                    <th class="py-3.5 px-4 w-36 text-center">Galeri</th>
                    <th class="py-3.5 px-4 w-28 text-center">Status</th>
                    <th class="py-3.5 px-4 w-36 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($activities as $item)
                    @php
                        $coverUrl = $item->cover_url ?? ($item->cover?->path ? \Illuminate\Support\Facades\Storage::disk($item->cover->disk ?? 'public')->url(ltrim($item->cover->path, '/')) : null);
                        
                        $galleryPhotos = $item->galleries ? $item->galleries->map(function($g) {
                            $m = $g->media ?? $g;
                            return $m->url ?? ($m->path ? \Illuminate\Support\Facades\Storage::disk($m->disk ?? 'public')->url(ltrim($m->path, '/')) : null);
                        })->filter()->values() : collect([]);

                        $isPublished = ($item->status === \App\Enums\PublishStatus::Published) || ($item->status?->value === \App\Enums\PublishStatus::Published->value);
                        
                        $cleanContent = $item->content ? trim(strip_tags($item->content)) : null;
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ method_exists($activities, 'firstItem') && $activities->firstItem() ? $activities->firstItem() + $loop->index : $loop->iteration }}
                        </td>
                        <td class="py-3 px-4">
                            @if ($coverUrl)
                                <div class="relative group cursor-pointer w-14 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-xs" 
                                     @click="openPreview('{{ $coverUrl }}', 'Preview Foto Cover Kegiatan')">
                                    <img src="{{ $coverUrl }}" alt="{{ $item->title }}" class="w-14 h-10 object-cover transition-transform duration-300 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                        <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div class="w-14 h-10 bg-slate-100 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400 font-medium">
                                    No Cover
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 max-w-md">
                            <div class="font-semibold text-slate-900">{{ $item->title }}</div>

                            {{-- DESKRIPSI KEGIATAN DI DESKTOP --}}
                            @if($cleanContent)
                                <div class="text-xs text-slate-500 font-normal line-clamp-2 mt-1 leading-relaxed" title="{{ $cleanContent }}">
                                    {{ $cleanContent }}
                                </div>
                            @else
                                <div class="text-xs text-slate-400 italic font-normal mt-0.5">
                                    Belum ada deskripsi.
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                            {{ $item->event_date ? (is_string($item->event_date) ? \Carbon\Carbon::parse($item->event_date)->format('d M Y') : $item->event_date->format('d M Y')) : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if(count($galleryPhotos) > 0)
                                <button type="button" 
                                        @click='openPreview(@json($galleryPhotos), "Preview Galeri Dokumentasi Kegiatan")'
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 transition cursor-pointer group">
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Lihat Galeri ({{ count($galleryPhotos) }})</span>
                                </button>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($isPublished)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.kegiatan.edit', $item) }}"
                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                                    Edit
                                </a>
                                <form action="{{ route('admin.kegiatan.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Belum ada data kegiatan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (method_exists($activities, 'hasPages') && $activities->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

    @if (method_exists($activities, 'hasPages') && $activities->hasPages())
        <div class="block md:hidden mt-3">
            {{ $activities->links() }}
        </div>
    @endif

    <!-- MODAL PREVIEW GALERI FOTO (LIGHTBOX) -->
    <div x-show="previewOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs"
         x-cloak>
        
        <div class="relative max-w-4xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col max-h-[90vh]"
             @click.away="previewOpen = false">
            
            <div class="flex items-center justify-between p-4 border-b border-slate-800 text-white shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold truncate" x-text="previewTitle"></span>
                    <span class="text-xs text-slate-400 font-mono shrink-0" x-text="`(${currentIndex + 1}/${previewPhotos.length})`"></span>
                </div>
                <button type="button" @click="previewOpen = false" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800 shrink-0 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="relative flex-1 flex items-center justify-center p-4 min-h-[300px] bg-slate-950/50">
                <template x-if="previewPhotos.length > 0">
                    <img :src="previewPhotos[currentIndex]" class="max-h-[60vh] max-w-full object-contain rounded-lg shadow-md">
                </template>

                <template x-if="previewPhotos.length > 1">
                    <div class="absolute inset-x-4 flex items-center justify-between pointer-events-none">
                        <button type="button" @click="prevPhoto()" class="pointer-events-auto bg-slate-900/80 hover:bg-indigo-600 text-white p-2.5 rounded-full backdrop-blur-xs transition shadow-lg border border-slate-700 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button type="button" @click="nextPhoto()" class="pointer-events-auto bg-slate-900/80 hover:bg-indigo-600 text-white p-2.5 rounded-full backdrop-blur-xs transition shadow-lg border border-slate-700 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <template x-if="previewPhotos.length > 1">
                <div class="p-3 bg-slate-900 border-t border-slate-800 overflow-x-auto flex items-center justify-center gap-2 shrink-0">
                    <template x-for="(photo, index) in previewPhotos" :key="index">
                        <button type="button" 
                                @click="currentIndex = index"
                                :class="currentIndex === index ? 'ring-2 ring-indigo-500 border-transparent scale-105' : 'opacity-50 hover:opacity-100 border-slate-700'"
                                class="relative w-12 h-12 rounded-lg overflow-hidden border transition transform shrink-0 cursor-pointer">
                            <img :src="photo" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </template>
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
            sessionStorage.setItem('kegiatan_search_focus', 'true');
            document.getElementById('search-form').submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        
        if (searchInput && sessionStorage.getItem('kegiatan_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('kegiatan_search_focus');
        }
    });
</script>
@endsection
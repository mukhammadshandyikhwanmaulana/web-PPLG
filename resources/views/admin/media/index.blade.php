@extends('layouts.admin.app')

@section('title', 'Galeri Media')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4" 
     x-data="{ openModal: false, activeAlbum: null, selectedMediaIds: [] }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Galeri Media</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola dan tinjau seluruh berkas media/gambar yang diunggah ke dalam sistem.</p>
        </div>
        <div>
            <a href="{{ route('admin.media.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Media
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form id="search-form" method="GET" action="{{ route('admin.media.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari nama file / deskripsi gambar..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.media.index', request()->except('search')) }}" 
                       title="Hapus kata kunci pencarian"
                       class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>

            <div class="w-full md:w-64 shrink-0">
                <select name="category" onchange="document.getElementById('search-form').submit()" 
                        class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Kelompok Media</option>
                    <option value="facilities" @selected(request('category') === 'facilities')>Fasilitas</option>
                    <option value="achievements" @selected(request('category') === 'achievements')>Prestasi</option>
                    <option value="student-works" @selected(request('category') === 'student-works')>Karya Siswa</option>
                    <option value="activities" @selected(request('category') === 'activities')>Kegiatan</option>
                    <option value="partners" @selected(request('category') === 'partners')>Mitra</option>
                    <option value="staff" @selected(request('category') === 'staff')>Guru</option>
                </select>
            </div>

            @php $hasFilter = request()->anyFilled(['search', 'category']); @endphp
            @if($hasFilter)
                <a href="{{ route('admin.media.index') }}" 
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
        @forelse ($groupedItems as $item)
            @php
                $cover = $item->cover;
                $isPdf = \Illuminate\Support\Str::contains($cover->mime_type ?? '', 'pdf') || \Illuminate\Support\Str::endsWith(strtolower($cover->original_name ?? ''), '.pdf');
                $coverDisk = $cover->disk ?? 'public';
                $fileUrl = $cover->file_url ?? ($cover->path ? \Illuminate\Support\Facades\Storage::disk($coverDisk)->url($cover->path) : null);

                $badgeName = match(true) {
                    $item->folder === 'achievements'  => 'Prestasi',
                    $item->folder === 'activities'    => 'Kegiatan',
                    $item->folder === 'student-works' => 'Karya Siswa',
                    $item->folder === 'facilities'    => 'Fasilitas',
                    in_array($item->folder, ['partners', 'partner']) => 'Mitra',
                    $item->folder === 'staff'         => 'Guru',
                    $item->folder === 'media'         => 'Galeri',
                    default                           => 'Umum'
                };

                if (isset($item->all_media)) {
                    foreach ($item->all_media as &$mItem) {
                        $mDisk = $mItem->disk ?? 'public';
                        $mItem->file_url = $mItem->file_url ?? ($mItem->path ? \Illuminate\Support\Facades\Storage::disk($mDisk)->url($mItem->path) : null);
                    }
                }
            @endphp

            <div class="bg-white rounded-2xl border border-slate-300 p-3 shadow-xs flex flex-col gap-2.5">
                <div class="flex items-center gap-3">
                    <div class="relative w-20 h-20 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 shrink-0 cursor-pointer"
                         @click="activeAlbum = {{ json_encode($item) }}; selectedMediaIds = []; openModal = true">
                        @if($isPdf)
                            <div class="w-full h-full flex flex-col items-center justify-center text-rose-600 font-bold">
                                <span class="text-[10px] font-black uppercase">PDF</span>
                            </div>
                        @else
                            <img src="{{ $fileUrl ?? 'https://placehold.co/600x400/e2e8f0/64748b?text=Rusak' }}" 
                                 alt="{{ $cover->alt_text ?? $item->title }}" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Rusak';"
                                 class="w-full h-full object-cover">
                        @endif

                        @if($item->total_count > 1)
                            <span class="absolute bottom-1 right-1 bg-indigo-600 text-white text-[8px] font-bold px-1.5 py-0.5 rounded shadow-xs">
                                📷 {{ $item->total_count }}
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="bg-slate-100 text-slate-700 border border-slate-200 text-[9px] font-semibold px-2 py-0.5 rounded-md">
                                {{ $badgeName }}
                            </span>
                        </div>
                        <h2 class="text-xs font-semibold text-slate-900 truncate" title="{{ $item->title }}">
                            {{ $item->title }}
                        </h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ isset($cover->created_at) && $cover->created_at ? (is_string($cover->created_at) ? \Carbon\Carbon::parse($cover->created_at)->format('d M Y') : $cover->created_at->format('d M Y')) : '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-5 pt-2 border-t border-slate-100">
                    <button type="button" 
                            @click="activeAlbum = {{ json_encode($item) }}; selectedMediaIds = []; openModal = true"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition cursor-pointer">
                        Lihat
                    </button>

                    <form action="{{ route('admin.media.destroy-group') }}" method="POST" class="inline-flex items-center m-0 p-0"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh berkas dalam album ini?')">
                        @csrf
                        @method('DELETE')
                        @foreach($item->all_media as $m)
                            <input type="hidden" name="media_ids[]" value="{{ $m->id }}">
                        @endforeach
                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer border-0 bg-transparent p-0">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                Belum ada berkas media yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        @forelse ($groupedItems as $item)
            @php
                $cover = $item->cover;
                $isPdf = \Illuminate\Support\Str::contains($cover->mime_type ?? '', 'pdf') || \Illuminate\Support\Str::endsWith(strtolower($cover->original_name ?? ''), '.pdf');
                $coverDisk = $cover->disk ?? 'public';
                $fileUrl = $cover->file_url ?? ($cover->path ? \Illuminate\Support\Facades\Storage::disk($coverDisk)->url($cover->path) : null);

                $badgeName = match(true) {
                    $item->folder === 'achievements'  => 'Prestasi',
                    $item->folder === 'activities'    => 'Kegiatan',
                    $item->folder === 'student-works' => 'Karya Siswa',
                    $item->folder === 'facilities'    => 'Fasilitas',
                    in_array($item->folder, ['partners', 'partner']) => 'Mitra',
                    $item->folder === 'staff'         => 'Guru',
                    $item->folder === 'media'         => 'Galeri',
                    default                           => 'Umum'
                };

                if (isset($item->all_media)) {
                    foreach ($item->all_media as &$mItem) {
                        $mDisk = $mItem->disk ?? 'public';
                        $mItem->file_url = $mItem->file_url ?? ($mItem->path ? \Illuminate\Support\Facades\Storage::disk($mDisk)->url($mItem->path) : null);
                    }
                }
            @endphp

            <div class="bg-white rounded-2xl border border-slate-300 overflow-hidden shadow-xs flex flex-col justify-between hover:border-indigo-400 hover:shadow-md transition duration-200 group">
                
                <div class="relative w-full h-32 sm:h-36 bg-slate-100 flex items-center justify-center overflow-hidden border-b border-slate-100 cursor-pointer"
                     @click="activeAlbum = {{ json_encode($item) }}; selectedMediaIds = []; openModal = true">
                    @if($isPdf)
                        <div class="flex flex-col items-center justify-center text-rose-600 font-bold">
                            <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-wider mt-1">PDF</span>
                        </div>
                    @else
                        <img src="{{ $fileUrl ?? 'https://placehold.co/600x400/e2e8f0/64748b?text=Gambar+Rusak' }}" 
                             alt="{{ $cover->alt_text ?? $item->title }}" 
                             onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Gambar+Rusak';"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @endif

                    <div class="absolute top-2 right-2 flex items-center gap-1 z-10">
                        <span class="bg-slate-900/80 backdrop-blur-xs text-white text-[9px] font-semibold px-2 py-0.5 rounded-md shadow-xs">
                            {{ $badgeName }}
                        </span>
                        @if($item->total_count > 1)
                            <span class="bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-xs flex items-center gap-0.5">
                                📷 {{ $item->total_count }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-2.5 sm:p-3 flex-1 flex flex-col justify-between space-y-2">
                    <div>
                        <h2 class="text-xs font-semibold text-slate-900 truncate" title="{{ $item->title }}">
                            {{ $item->title }}
                        </h2>

                        <p class="text-[10px] text-slate-400 mt-1">
                            {{ isset($cover->created_at) && $cover->created_at ? (is_string($cover->created_at) ? \Carbon\Carbon::parse($cover->created_at)->format('d M Y') : $cover->created_at->format('d M Y')) : '—' }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <button type="button" 
                                @click="activeAlbum = {{ json_encode($item) }}; selectedMediaIds = []; openModal = true"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none cursor-pointer">
                            Lihat
                        </button>

                        <form action="{{ route('admin.media.destroy-group') }}" method="POST" class="inline-flex items-center m-0 p-0"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh berkas dalam album ini?')">
                            @csrf
                            @method('DELETE')
                            @foreach($item->all_media as $m)
                                <input type="hidden" name="media_ids[]" value="{{ $m->id }}">
                            @endforeach
                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-300 p-12 text-center text-slate-500 text-sm shadow-xs">
                Belum ada berkas media yang ditemukan.
            </div>
        @endforelse
    </div>

    @if (isset($media) && method_exists($media, 'hasPages') && $media->hasPages())
        <div class="bg-white px-4 py-3 rounded-2xl border border-slate-300 shadow-xs">
            {{ $media->links() }}
        </div>
    @endif

    <!-- MODAL DETAIL ALBUM / PEMILIHAN MEDIA -->
    <div x-show="openModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden border border-slate-200"
             @click.away="openModal = false">
            
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-slate-900" x-text="activeAlbum?.title"></h3>
                    <p class="text-xs text-slate-500 mt-0.5">Klik gambar untuk menandai foto yang ingin dihapus.</p>
                </div>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-200/60 transition cursor-pointer shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 overflow-y-auto flex-1 grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                <template x-for="(m, idx) in activeAlbum?.all_media" :key="m.id">
                    <div class="relative bg-white border rounded-xl overflow-hidden flex flex-col justify-between shadow-xs transition duration-200 cursor-pointer select-none group"
                         :class="selectedMediaIds.includes(m.id) ? 'border-rose-500 ring-2 ring-rose-500/50 bg-rose-50/10' : 'border-slate-300 hover:border-indigo-400'"
                         @click="
                             if (selectedMediaIds.includes(m.id)) {
                                 selectedMediaIds = selectedMediaIds.filter(id => id !== m.id);
                             } else {
                                 selectedMediaIds.push(m.id);
                             }
                         ">
                        
                        <div class="h-32 sm:h-36 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            <img :src="m.file_url" 
                                 :alt="m.original_name" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Gambar+Rusak';"
                                 class="w-full h-full object-cover transition-transform duration-300">
                            
                            <span x-show="idx === 0" class="absolute top-2 left-2 bg-indigo-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-md shadow-xs z-10">Sampul</span>

                            <div x-show="selectedMediaIds.includes(m.id)"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="absolute inset-0 bg-rose-950/60 backdrop-blur-[1px] flex flex-col items-center justify-center gap-1.5 z-20">
                                
                                <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>

                                <span class="text-[10px] font-bold text-white uppercase tracking-wider">Akan Dihapus</span>
                            </div>

                            <div x-show="!selectedMediaIds.includes(m.id)"
                                 class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition duration-150 flex items-center justify-center">
                                <span class="bg-white/90 backdrop-blur-xs text-slate-700 text-[10px] font-semibold px-2 py-1 rounded-md shadow-xs">
                                    Klik untuk pilih
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-2.5 text-xs flex flex-col gap-1.5 bg-white border-t border-slate-100">
                            <span class="truncate font-sans font-medium text-[11px] text-slate-800" x-text="m.original_name" :title="m.original_name"></span>
                            
                            <div class="flex items-center justify-between pt-1 border-t border-slate-100" @click.stop>
                                <a :href="'/admin/media/' + m.id + '/edit'" 
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>

                                <span x-show="selectedMediaIds.includes(m.id)" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                                    Dipilih
                                </span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-3 shrink-0">
                <button type="button" 
                        @click="openModal = false" 
                        class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center cursor-pointer shrink-0">
                    Batal
                </button>

                <form action="{{ route('admin.media.destroy-group') }}" method="POST" class="m-0 p-0"
                      onsubmit="
                          if (selectedMediaIds.length > 0) {
                              return confirm(`Apakah Anda yakin ingin menghapus ${selectedMediaIds.length} foto yang dipilih?`);
                          }
                      ">
                    @csrf
                    @method('DELETE')

                    <template x-for="id in selectedMediaIds" :key="id">
                        <input type="hidden" name="media_ids[]" :value="id">
                    </template>

                    <button type="submit" 
                            @click="if (selectedMediaIds.length === 0) { openModal = false; $event.preventDefault(); }"
                            :class="selectedMediaIds.length > 0 ? 'bg-rose-600 hover:bg-rose-700 focus:ring-2 focus:ring-rose-500/30' : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/30'"
                            class="inline-flex items-center justify-center gap-2 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                        
                        <template x-if="selectedMediaIds.length > 0">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </template>

                        <template x-if="selectedMediaIds.length === 0">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>

                        <span x-text="selectedMediaIds.length > 0 ? `Hapus (${selectedMediaIds.length}) & Simpan` : 'Simpan'"></span>
                    </button>
                </form>
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
            sessionStorage.setItem('media_search_focus', 'true');
            document.getElementById('search-form').submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        
        if (searchInput && sessionStorage.getItem('media_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('media_search_focus');
        }
    });
</script>
@endsection
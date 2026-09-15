@extends('layouts.admin.app')

@section('title', 'Prestasi')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4" 
     x-data="{ 
         previewModal: false, 
         previewUrl: '', 
         previewType: '', 
         previewTitle: '',
         previewAlt: '',
         openPreview(url, file_name, ext, alt_text, mime_type) {
             this.previewUrl = url;
             this.previewTitle = file_name;
             this.previewAlt = alt_text || file_name;
             this.previewType = (mime_type === 'application/pdf' || (ext || '').toLowerCase() === 'pdf') ? 'pdf' : 'image';
             this.previewModal = true;
         }
     }"
     @keydown.escape.window="previewModal = false">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Prestasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data pencapaian dan prestasi siswa/sekolah.</p>
        </div>
        <div>
            <a href="{{ route('admin.prestasi.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Prestasi
            </a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-300">
        <form id="search-form" method="GET" action="{{ route('admin.prestasi.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            {{-- Input Pencarian Teks --}}
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari judul / kontributor / deskripsi..."
                       autocomplete="off"
                       oninput="handleAutoSearch()"
                       class="w-full pl-10 {{ request('search') ? 'pr-10' : 'pr-3.5' }} py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">

                @if(request('search'))
                    <a href="{{ route('admin.prestasi.index', request()->except('search')) }}" 
                       title="Hapus kata kunci pencarian"
                       class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Filter Level --}}
            <div class="w-full md:w-48">
                <select name="level" onchange="document.getElementById('search-form').submit()" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer font-medium text-slate-700">
                    <option value="">Semua Level</option>
                    @foreach (\App\Enums\AchievementLevel::cases() as $level)
                        <option value="{{ $level->value }}" @selected(request('level') === $level->value)>
                            {{ $level->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
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

            {{-- Tombol Reset --}}
            @php 
                $hasFilter = request()->anyFilled(['search', 'level', 'status']); 
            @endphp

            @if($hasFilter)
                <a href="{{ route('admin.prestasi.index') }}" 
                   class="flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE -->
    <div class="block md:hidden space-y-3">
        @forelse ($achievements as $achievement)
            @php
                $mediaDoc = $achievement->document;
                $docPath = $mediaDoc?->path ?? $mediaDoc?->file_path;
                $diskName = $mediaDoc?->disk ?? 'public';
                $docUrl = $docPath ? \Illuminate\Support\Facades\Storage::disk($diskName)->url(ltrim($docPath, '/')) : '';
                $docExt = $docPath ? pathinfo($docPath, PATHINFO_EXTENSION) : '';
                $docMime = $mediaDoc?->mime_type ?? '';
                $docAlt = $mediaDoc?->alt_text ?? $achievement->title;
                $isPublished = ($achievement->status === \App\Enums\PublishStatus::Published) || ($achievement->status?->value === \App\Enums\PublishStatus::Published->value);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="text-sm font-semibold text-slate-900 leading-snug break-words">{{ $achievement->title }}</h2>
                    @if($isPublished)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Published</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">Draft</span>
                    @endif
                </div>

                @if($achievement->description)
                    <p class="text-xs text-slate-600 line-clamp-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60 leading-relaxed">
                        {{ strip_tags($achievement->description) }}
                    </p>
                @endif

                <div class="text-xs text-slate-600 space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kontributor:</span>
                        <span class="text-slate-800 font-medium text-right">{{ $achievement->contributor_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Level:</span>
                        <span class="text-slate-800 font-medium">{{ $achievement->level instanceof \App\Enums\AchievementLevel ? $achievement->level->label() : ($achievement->level ?? '—') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tanggal:</span>
                        <span class="text-slate-800 font-medium">{{ $achievement->achievement_date?->format('d M Y') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1.5 border-t border-slate-200/60">
                        <span class="text-slate-400">Dokumen Bukti:</span>
                        @if($docUrl)
                            <button type="button" 
                                    @click="openPreview(@js($docUrl), @js($achievement->title), @js($docExt), @js($docAlt), @js($docMime))"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold text-xs transition active:scale-95 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Berkas</span>
                            </button>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.prestasi.edit', $achievement) }}"
                       class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                        Edit
                    </a>
                    <form action="{{ route('admin.prestasi.destroy', $achievement) }}" method="POST" class="inline-flex items-center m-0 p-0"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                Belum ada data prestasi yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[950px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4">Judul</th>
                    <th class="py-3.5 px-4">Deskripsi</th>
                    <th class="py-3.5 px-4">Kontributor</th>
                    <th class="py-3.5 px-4 w-32">Tanggal</th>
                    <th class="py-3.5 px-4 w-28">Level</th>
                    <th class="py-3.5 px-4 w-28 text-center">Status</th>
                    <th class="py-3.5 px-4 w-28 text-center">Dokumen</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($achievements as $achievement)
                    @php
                        $mediaDoc = $achievement->document;
                        $docPath = $mediaDoc?->path ?? $mediaDoc?->file_path;
                        $diskName = $mediaDoc?->disk ?? 'public';
                        $docUrl = $docPath ? \Illuminate\Support\Facades\Storage::disk($diskName)->url(ltrim($docPath, '/')) : '';
                        $docExt = $docPath ? pathinfo($docPath, PATHINFO_EXTENSION) : '';
                        $docMime = $mediaDoc?->mime_type ?? '';
                        $docAlt = $mediaDoc?->alt_text ?? $achievement->title;
                        $isPublished = ($achievement->status === \App\Enums\PublishStatus::Published) || ($achievement->status?->value === \App\Enums\PublishStatus::Published->value);
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4 font-medium text-slate-900 max-w-xs">
                            <div class="line-clamp-2" title="{{ $achievement->title }}">{{ $achievement->title }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-600 max-w-xs">
                            <div class="line-clamp-2 text-xs leading-relaxed" title="{{ strip_tags($achievement->description ?? '') }}">
                                {{ strip_tags($achievement->description ?? '—') }}
                            </div>
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            {{ $achievement->contributor_name ?? '—' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                            {{ $achievement->achievement_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                            {{ $achievement->level instanceof \App\Enums\AchievementLevel ? $achievement->level->label() : ($achievement->level ?? '—') }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($isPublished)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">Draft</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap text-slate-600">
                            @if($docUrl)
                                <button type="button" 
                                        @click="openPreview(@js($docUrl), @js($achievement->title), @js($docExt), @js($docAlt), @js($docMime))"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs border border-indigo-200 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Lihat</span>
                                </button>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.prestasi.edit', $achievement) }}"
                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                                    Edit
                                </a>
                                <form action="{{ route('admin.prestasi.destroy', $achievement) }}" method="POST" class="inline-flex items-center m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
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
                        <td colspan="8" class="py-12 px-4 text-center text-slate-500">
                            Belum ada data prestasi yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($achievements->hasPages())
        <div class="px-4 py-3 bg-white border border-slate-300 rounded-2xl shadow-xs">
            {{ $achievements->links() }}
        </div>
    @endif

    <!-- MODAL PREVIEW DOKUMEN / MEDIA -->
    <div x-show="previewModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-xs"
         x-cloak>
        
        <div class="relative max-w-lg w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col max-h-[90vh]"
             @click.away="previewModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-800 text-white shrink-0">
                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                    <div class="p-1.5 bg-indigo-500/20 text-indigo-400 rounded-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold truncate text-slate-100" x-text="previewTitle"></h3>
                        <p class="text-[11px] text-slate-400">Preview Dokumen Bukti</p>
                    </div>
                </div>
                <button type="button" @click="previewModal = false" class="text-slate-400 hover:text-white transition p-1.5 rounded-xl hover:bg-slate-800 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body Container -->
            <div class="p-5 flex-1 flex flex-col items-center justify-center min-h-[260px] bg-slate-950/60 overflow-y-auto">
                
                <!-- JIKA BERKAS ADALAH PDF -->
                <template x-if="previewType === 'pdf'">
                    <div class="w-full flex flex-col items-center justify-center text-center p-6 bg-slate-900/90 rounded-2xl border border-slate-800/80 space-y-4">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-rose-500/10 border border-rose-500/30 text-rose-500 rounded-3xl flex flex-col items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-wider mt-1 bg-rose-600 text-white px-2 py-0.5 rounded-full">PDF</span>
                        </div>

                        <div class="space-y-1 max-w-full px-2">
                            <p class="text-xs font-semibold text-slate-200" x-text="previewAlt"></p>
                        </div>

                        <a :href="previewUrl" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center justify-center gap-2 w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-3 rounded-xl text-xs transition shadow-md">
                            <span>Buka Dokumen PDF ↗</span>
                        </a>
                    </div>
                </template>

                <!-- JIKA BERKAS ADALAH GAMBAR -->
                <template x-if="previewType === 'image'">
                    <div class="flex flex-col items-center justify-center w-full space-y-2">
                        <img :src="previewUrl" :alt="previewAlt" class="max-h-[50vh] max-w-full object-contain rounded-xl shadow-md border border-slate-800">
                        <p class="text-xs text-slate-400 text-center italic mt-2" x-text="previewAlt"></p>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-900 border-t border-slate-800 flex items-center justify-end shrink-0">
                <button type="button" @click="previewModal = false" 
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Kembali
                </button>
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
            sessionStorage.setItem('prestasi_search_focus', 'true');
            document.getElementById('search-form').submit();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        
        if (searchInput && sessionStorage.getItem('prestasi_search_focus') === 'true') {
            searchInput.focus();
            const textLen = searchInput.value.length;
            searchInput.setSelectionRange(textLen, textLen);
            sessionStorage.removeItem('prestasi_search_focus');
        }
    });
</script>
@endsection
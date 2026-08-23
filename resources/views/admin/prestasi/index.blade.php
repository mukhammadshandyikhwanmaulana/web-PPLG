@extends('layouts.admin.app')

@section('title', 'Prestasi')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4" 
     x-data="{ 
         previewModal: false, 
         previewUrl: '', 
         previewType: '', 
         previewTitle: '',
         openPreview(url, file_name) {
             this.previewUrl = url;
             this.previewTitle = file_name;
             this.previewType = url.split('.').pop().toLowerCase() === 'pdf' ? 'pdf' : 'image';
             this.previewModal = true;
         }
     }">

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
        <form method="GET" action="{{ route('admin.prestasi.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul / kontributor..."
                       class="w-full pl-10 pr-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white">
            </div>

            <div class="w-full md:w-48">
                <select name="level" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer">
                    <option value="">Semua Level</option>
                    @foreach (\App\Enums\AchievementLevel::cases() as $level)
                        <option value="{{ $level->value }}" @selected(request('level') === $level->value)>
                            {{ $level->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="status" class="w-full py-2.5 px-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition bg-white cursor-pointer">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\PublishStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ ucfirst($status->value) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-xs transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>

                @if(request()->anyFilled(['search', 'level', 'status']))
                    <a href="{{ route('admin.prestasi.index') }}" class="inline-flex items-center justify-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2.5 rounded-xl transition duration-150">
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
        @forelse ($achievements as $achievement)
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="text-sm font-semibold text-slate-900 leading-snug break-words">{{ $achievement->title }}</h2>
                    @if($achievement->status === \App\Enums\PublishStatus::Published)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                            Draft
                        </span>
                    @endif
                </div>

                <div class="text-xs text-slate-600 space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kontributor:</span>
                        <span class="text-slate-800 font-medium text-right">{{ $achievement->contributor_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Level:</span>
                        <span class="text-slate-800 font-medium">{{ $achievement->level?->label() ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tanggal:</span>
                        <span class="text-slate-800 font-medium">{{ $achievement->achievement_date?->format('d M Y') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1.5 border-t border-slate-200/60">
                        <span class="text-slate-400">Dokumen Bukti:</span>
                        @if($achievement->document)
                            <button type="button" 
                                    @click="openPreview('{{ Storage::url($achievement->document->file_path) }}', '{{ addslashes($achievement->title) }}')"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-medium text-xs transition active:scale-95">
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

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.prestasi.edit', $achievement) }}"
                       class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-medium rounded-lg transition">
                        Edit
                    </a>
                    <form action="{{ route('admin.prestasi.destroy', $achievement) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
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
                Belum ada data prestasi yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4">Judul</th>
                    <th class="py-3.5 px-4">Kontributor</th>
                    <th class="py-3.5 px-4 w-36">Tanggal</th>
                    <th class="py-3.5 px-4 w-32">Level</th>
                    <th class="py-3.5 px-4 w-32">Status</th>
                    <th class="py-3.5 px-4 w-32">Dokumen</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($achievements as $achievement)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $achievement->title }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            {{ $achievement->contributor_name ?? '—' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                            {{ $achievement->achievement_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                            {{ $achievement->level?->label() ?? '—' }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($achievement->status === \App\Enums\PublishStatus::Published)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            @if($achievement->document)
                                <button type="button" 
                                        @click="openPreview('{{ Storage::url($achievement->document->file_path) }}', '{{ addslashes($achievement->title) }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-medium text-xs border border-indigo-200 transition">
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
                                   class="text-indigo-600 hover:text-indigo-900 font-medium transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.prestasi.destroy', $achievement) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
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
                        <td colspan="7" class="py-12 px-4 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Belum ada data prestasi yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($achievements->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                {{ $achievements->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL PREVIEW DOKUMEN -->
    <div x-show="previewModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" 
         style="display: none;">
        
        <div @click.away="previewModal = false" 
             class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="min-w-0 pr-2">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-900 truncate" x-text="previewTitle"></h3>
                    <p class="text-[11px] sm:text-xs text-slate-500">Preview Dokumen Bukti</p>
                </div>
                <button @click="previewModal = false" 
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-3 sm:p-4 overflow-y-auto flex-1 bg-slate-100 flex justify-center items-center">
                <template x-if="previewType === 'image'">
                    <img :src="previewUrl" class="max-h-[65vh] w-auto object-contain rounded-xl shadow-xs border border-slate-200">
                </template>
                <template x-if="previewType === 'pdf'">
                    <div class="w-full h-[65vh] flex flex-col">
                        <iframe :src="previewUrl" class="w-full h-full rounded-xl border border-slate-300"></iframe>
                    </div>
                </template>
            </div>

            <div class="px-4 py-3 border-t border-slate-200 bg-white flex items-center justify-between shrink-0">
                <a :href="previewUrl" target="_blank" class="text-xs text-indigo-600 hover:underline font-medium">
                    Buka di Tab Baru ↗
                </a>
                <button type="button" 
                        @click="previewModal = false" 
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs sm:text-sm font-medium transition active:scale-95 shadow-xs">
                    Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
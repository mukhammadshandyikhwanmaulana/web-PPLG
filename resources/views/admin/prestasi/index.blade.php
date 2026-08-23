@extends('layouts.admin.app')

@section('title', 'Prestasi')

@section('content')
<div class="max-w-7xl mx-auto py-3 sm:py-6 px-3 sm:px-6 lg:px-8" 
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

    {{-- Header: Judul & Tombol Tambah --}}
    <div class="flex items-center justify-between gap-3 mb-4 sm:mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Prestasi</h1>
            <p class="text-xs text-gray-500 hidden sm:block mt-0.5">Kelola data pencapaian dan prestasi siswa/sekolah.</p>
        </div>
        <a href="{{ route('admin.prestasi.create') }}"
           class="bg-indigo-600 text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm hover:bg-indigo-700 font-medium shrink-0 shadow-sm transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah<span class="hidden sm:inline"> Prestasi</span></span>
        </a>
    </div>

    {{-- Form Search & Filter Responsif --}}
    <form method="GET" action="{{ route('admin.prestasi.index') }}" class="mb-4 sm:mb-6 space-y-2 sm:space-y-0 sm:flex sm:items-center sm:gap-2">
        {{-- Input Search + Tombol Cari & Reset --}}
        <div class="flex items-center gap-2 flex-1">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul / kontributor..."
                       class="pl-9 border border-gray-300 rounded-lg text-xs sm:text-sm px-3 py-2 w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all">
            </div>

            {{-- Tombol CTA Cari --}}
            <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-medium transition shadow-sm flex items-center gap-1 shrink-0">
                <span>Cari</span>
            </button>

            {{-- Tombol Reset (Muncul jika ada filter/pencarian aktif) --}}
            @if(request()->anyFilled(['search', 'level', 'status']))
                <a href="{{ route('admin.prestasi.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-600 px-3 py-2 rounded-lg text-xs sm:text-sm font-medium transition shrink-0 flex items-center gap-1 border border-gray-300"
                   title="Reset Filter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="hidden sm:inline">Reset</span>
                </a>
            @endif
        </div>

        {{-- Dropdown Filter Grid --}}
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2">
            <select name="level" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg text-xs sm:text-sm px-2.5 py-2 w-full sm:w-auto focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer shadow-sm">
                <option value="">Semua Level</option>
                @foreach (\App\Enums\AchievementLevel::cases() as $level)
                    <option value="{{ $level->value }}" @selected(request('level') === $level->value)>
                        {{ $level->label() }}
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg text-xs sm:text-sm px-2.5 py-2 w-full sm:w-auto focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer shadow-sm">
                <option value="">Semua Status</option>
                @foreach (\App\Enums\PublishStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- TAMPILAN MOBILE & TABLET KECIL --}}
    <div class="block md:hidden space-y-3">
        @forelse ($achievements as $achievement)
            <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-gray-200 shadow-sm space-y-2.5 hover:border-gray-300 transition-all">
                <div class="flex justify-between items-start gap-2">
                    <h2 class="font-semibold text-gray-900 text-sm leading-snug break-words">{{ $achievement->title }}</h2>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold shrink-0 uppercase tracking-wider
                        {{ $achievement->status === \App\Enums\PublishStatus::Published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        {{ $achievement->status->value }}
                    </span>
                </div>

                <div class="text-xs text-gray-600 space-y-1 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Kontributor:</span>
                        <span class="text-gray-800 font-medium text-right">{{ $achievement->contributor_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Level:</span>
                        <span class="text-gray-800 font-medium">{{ $achievement->level?->label() ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tanggal:</span>
                        <span class="text-gray-800 font-medium">{{ $achievement->achievement_date?->format('d M Y') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1 border-t border-gray-200/60 mt-1">
                        <span class="text-gray-400">Dokumen Bukti:</span>
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
                            <span class="text-gray-400">—</span>
                        @endif
                    </div>
                </div>

                <div class="pt-1 flex justify-end gap-4 text-xs font-semibold">
                    <a href="{{ route('admin.prestasi.edit', $achievement) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                    <form method="POST" action="{{ route('admin.prestasi.destroy', $achievement) }}" class="inline" onsubmit="return confirm('Hapus prestasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-800">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 text-center text-gray-400 text-sm border border-gray-200 rounded-xl">
                Belum ada data prestasi.
            </div>
        @endforelse
    </div>

    {{-- TAMPILAN DESKTOP & TABLET BESAR --}}
    <div class="hidden md:block bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-50/75 text-gray-500 font-medium text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Judul</th>
                        <th class="px-4 py-3.5">Kontributor</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">Level</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Dokumen</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($achievements as $achievement)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-medium text-gray-900">{{ $achievement->title }}</td>
                            <td class="px-4 py-3.5 text-gray-600">{{ $achievement->contributor_name ?? '—' }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                {{ $achievement->achievement_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">{{ $achievement->level?->label() ?? '—' }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                                    {{ $achievement->status === \App\Enums\PublishStatus::Published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $achievement->status->value }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
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
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right space-x-3 font-medium">
                                <a href="{{ route('admin.prestasi.edit', $achievement) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.prestasi.destroy', $achievement) }}" class="inline" onsubmit="return confirm('Hapus prestasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data prestasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Responsif --}}
    <div class="mt-4 sm:mt-6">
        {{ $achievements->links() }}
    </div>

    {{-- MODAL PREVIEW DOKUMEN --}}
    <div x-show="previewModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" 
         style="display: none;">
        
        <div @click.away="previewModal = false" 
             class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50 shrink-0">
                <div class="min-w-0 pr-2">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate" x-text="previewTitle"></h3>
                    <p class="text-[11px] sm:text-xs text-gray-500">Preview Dokumen Bukti</p>
                </div>
                <button @click="previewModal = false" 
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-3 sm:p-4 overflow-y-auto flex-1 bg-gray-100 flex justify-center items-center">
                <template x-if="previewType === 'image'">
                    <img :src="previewUrl" class="max-h-[65vh] w-auto object-contain rounded-lg shadow-sm border border-gray-200">
                </template>
                <template x-if="previewType === 'pdf'">
                    <div class="w-full h-[65vh] flex flex-col">
                        <iframe :src="previewUrl" class="w-full h-full rounded-lg border border-gray-300"></iframe>
                    </div>
                </template>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 bg-white flex items-center justify-between shrink-0">
                <a :href="previewUrl" target="_blank" class="text-xs text-indigo-600 hover:underline font-medium">
                    Buka di Tab Baru ↗
                </a>
                <button type="button" 
                        @click="previewModal = false" 
                        class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-xs sm:text-sm font-medium transition active:scale-95 shadow-sm">
                    Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
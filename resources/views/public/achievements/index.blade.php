@extends('layouts.public')

@section('title', 'Prestasi Siswa - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS KONSISTEN) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-24 sm:pb-32 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- JUDUL UTAMA --}}
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Prestasi Siswa
            </h1>

        </div>
    </section>

    <!-- ================= 2. KONTEN UTAMA PRESTASI ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if($achievements->isNotEmpty())

                <!-- Grid Prestasi -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4 items-stretch">

                    @foreach($achievements as $achievement)

                        @php
                            $docUrl = $achievement->document_url;
                            $mimeType = $achievement->document?->mime_type ?? '';

                            $isPdf = str_contains($mimeType, 'pdf')
                                || str_ends_with(strtolower($docUrl ?? ''), '.pdf');

                            $levelText = is_object($achievement->level)
                                ? (method_exists($achievement->level, 'label')
                                    ? $achievement->level->label()
                                    : ($achievement->level->value ?? ''))
                                : (string) $achievement->level;
                        @endphp

                        <!-- KARTU PRESTASI (MENGHUBUNGKAN LANGSUNG KE SHOW) -->
                        <a href="{{ route('public.achievements.show', $achievement->slug ?? $achievement->id) }}" 
                           class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between group h-full">

                            <div>
                                <!-- Thumbnail Foto (3:4 Ratio) -->
                                <div class="w-full aspect-[3/4] bg-slate-100 border-b border-slate-100 flex items-center justify-center overflow-hidden relative select-none">

                                    @if($docUrl && !$isPdf)
                                        <img src="{{ $docUrl }}"
                                             alt="{{ $achievement->title }}"
                                             class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <div class="hidden flex-col items-center justify-center text-slate-400 gap-1 bg-slate-100 w-full h-full p-2 text-center">
                                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[10px] font-semibold">Tanpa Foto</span>
                                        </div>
                                    @elseif($docUrl && $isPdf)
                                        <div class="flex flex-col items-center justify-center text-rose-500 gap-1 bg-rose-50/60 w-full h-full p-2 text-center">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[10px] font-semibold">PDF Dokumentasi</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-center text-slate-400 gap-1 bg-slate-100 w-full h-full p-2 text-center">
                                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[10px] font-semibold">Tanpa Foto</span>
                                        </div>
                                    @endif

                                </div>

                                <!-- Detail Ringkas -->
                                <div class="p-3 space-y-1.5">
                                    @if(!empty($levelText))
                                        <span class="inline-block px-2 py-0.5 text-[9px] font-bold text-orange-600 bg-orange-50 border border-orange-100 rounded">
                                            {{ ucfirst($levelText) }}
                                        </span>
                                    @endif

                                    <h3 class="font-sans font-semibold text-slate-900 text-xs leading-snug line-clamp-2 group-hover:text-orange-600 transition-colors">
                                        {{ $achievement->title }}
                                    </h3>

                                    @if($achievement->contributor_name)
                                        <p class="text-[11px] font-medium text-slate-600 truncate">
                                            <span class="font-semibold text-slate-800">{{ $achievement->contributor_name }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer Kartu -->
                            <div class="px-3 pb-3 pt-1 border-t border-slate-50 mt-auto flex items-center justify-end">
                                <span class="text-[10px] font-semibold text-orange-600 group-hover:text-orange-700 inline-flex items-center gap-0.5">
                                    <span>Lihat Prestasi</span>
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </div>

                        </a>

                    @endforeach

                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    {{ $achievements->links() }}
                </div>

            @else

                <!-- Data Kosong -->
                <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto">
                    <p class="font-sans text-sm font-semibold">
                        Data prestasi tidak ditemukan.
                    </p>
                </div>

            @endif

        </div>

    </section>

@endsection
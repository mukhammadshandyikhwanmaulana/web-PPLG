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

            @if(isset($achievements) && $achievements->isNotEmpty())

                <!-- GRID PRESTASI (3 KOLOM KONSISTEN SEPERTI BERANDA) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

                    @foreach($achievements as $achievement)

                        @php
                            $imageUrl = $achievement->document_url;
                            $formattedDate = $achievement->achievement_date 
                                ? $achievement->achievement_date->translatedFormat('d F Y') 
                                : ($achievement->created_at ? $achievement->created_at->translatedFormat('d F Y') : '');

                            $levelText = is_object($achievement->level) && property_exists($achievement->level, 'value') 
                                ? $achievement->level->value 
                                : (string) $achievement->level;

                            $detailUrl = Route::has('public.achievements.show') && !empty($achievement->slug) 
                                ? route('public.achievements.show', $achievement->slug) 
                                : route('public.achievements.index');
                        @endphp

                        <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between h-full group relative">
                            
                            <a href="{{ $detailUrl }}" class="flex flex-col h-full p-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-2xl" aria-label="Detail prestasi {{ $achievement->title }}">
                                <div>
                                    {{-- THUMBNAIL FOTO (RASIO 16:10 KONSISTEN DENGAN BERANDA) --}}
                                    <div class="w-full aspect-[16/10] bg-slate-100 rounded-xl relative select-none mb-4">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $achievement->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300 rounded-xl"
                                                 loading="lazy"
                                                 decoding="async"
                                                 onerror="this.src='{{ asset('images/placeholder-pplg.webp') }}'">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100 rounded-xl">
                                                <span class="font-sans text-xs font-medium">Tidak ada foto</span>
                                            </div>
                                        @endif

                                        @if(!empty($levelText))
                                            <div class="absolute top-3 left-3 bg-orange-500 text-white font-sans text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                                {{ strtoupper($levelText) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- DESKRIPSI RINGKAS & TANGGAL --}}
                                    <div class="space-y-2">
                                        @if($formattedDate)
                                            <p class="font-sans text-xs font-medium text-slate-500">
                                                {{ $formattedDate }}
                                            </p>
                                        @endif

                                        <h3 class="font-sans font-semibold text-slate-900 text-base sm:text-lg leading-snug tracking-tight line-clamp-2 group-hover:text-orange-600 transition-colors">
                                            {{ $achievement->title }}
                                        </h3>

                                        @if($achievement->contributor_name)
                                            <p class="font-sans text-xs font-semibold text-slate-700 truncate">
                                                {{ $achievement->contributor_name }}
                                            </p>
                                        @endif

                                        <p class="font-sans text-slate-600 text-xs sm:text-sm leading-relaxed line-clamp-2">
                                            {{ Str::limit(strip_tags($achievement->description ?? $achievement->content ?? ''), 90) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- FOOTER KARTU --}}
                                <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-end text-xs font-semibold text-orange-600 group-hover:text-orange-700 transition-colors">
                                    <span class="flex items-center gap-1">
                                        Detail Prestasi
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </div>
                            </a>

                        </article>

                    @endforeach

                </div>

                <!-- PAGINATION -->
                <div class="pt-4 flex justify-center">
                    {{ $achievements->links() }}
                </div>

            @else

                <!-- DATA KOSONG -->
                <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-xl mx-auto">
                    <p class="font-sans text-sm">Belum ada data prestasi yang dipublikasikan.</p>
                </div>

            @endif

        </div>

    </section>

@endsection
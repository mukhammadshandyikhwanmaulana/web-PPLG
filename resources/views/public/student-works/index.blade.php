@extends('layouts.public')

@section('title', 'Karya Siswa - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS & BERSIH) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-24 sm:pb-32 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- JUDUL UTAMA --}}
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Karya Siswa
            </h1>

        </div>
    </section>

    <!-- ================= 2. KONTEN DAFTAR KARYA SISWA ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- FORM PENCARIAN -->
            <div class="max-w-md mx-auto">
                <form action="{{ route('public.student-works.index') }}" method="GET" class="flex gap-2">
                    <div class="relative w-full">
                        <input type="text" 
                               name="q" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari karya atau nama pembuat..." 
                               class="w-full px-4 py-2.5 rounded-xl text-slate-900 bg-slate-50 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 border border-slate-200 shadow-xs font-sans">
                    </div>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl transition shrink-0 shadow-sm cursor-pointer font-sans">
                        Cari
                    </button>
                </form>
            </div>

            @if($studentWorks->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                    @foreach($studentWorks as $work)
                        @php
                            $imageUrl = $work->cover_url;
                            $formattedDate = $work->published_at 
                                ? $work->published_at->translatedFormat('d M Y') 
                                : ($work->created_at ? $work->created_at->translatedFormat('d M Y') : '');
                            $photosCount = $work->galleries ? $work->galleries->count() : 0;
                            $detailUrl = route('public.student-works.show', $work->slug ?? $work->id);
                        @endphp

                        <!-- KARTU KARYA SISWA (SELURUH KARTU BISA DIKLIK) -->
                        <a href="{{ $detailUrl }}" 
                           class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between group h-full">
                            
                            <div>
                                {{-- Sampul Karya Portrait (3:4) --}}
                                <div class="w-full aspect-[3/4] max-h-[350px] sm:max-h-[370px] bg-slate-100 border-b border-slate-100 overflow-hidden relative select-none flex items-center justify-center p-2">
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" 
                                             alt="{{ $work->title }}" 
                                             class="w-full h-full object-cover object-top rounded-xl group-hover:scale-105 transition-transform duration-300 ease-out"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.src='https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=800&auto=format&fit=crop'">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 gap-2 bg-slate-100 rounded-xl">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-slate-400">Tidak ada foto karya</span>
                                        </div>
                                    @endif

                                    @if($work->is_featured)
                                        <div class="absolute top-4 left-4 bg-orange-500 text-white px-2.5 py-1 rounded-lg font-bold text-[10px] uppercase tracking-wider shadow-md">
                                            Unggulan
                                        </div>
                                    @endif
                                </div>

                                {{-- Content Body --}}
                                <div class="p-5 space-y-2.5">
                                    
                                    <div class="flex items-center justify-between gap-2">
                                        @if($photosCount > 0)
                                            <div class="flex items-center gap-1.5 text-xs font-bold text-orange-600">
                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $photosCount }} Foto</span>
                                            </div>
                                        @else
                                            <div></div>
                                        @endif

                                        <span class="text-xs text-slate-400 font-medium whitespace-nowrap">
                                            {{ $formattedDate }}
                                        </span>
                                    </div>

                                    <h3 class="font-sans font-semibold text-slate-900 text-base sm:text-lg leading-snug line-clamp-2 group-hover:text-orange-600 transition-colors">
                                        {{ $work->title }}
                                    </h3>

                                    @if($work->contributor_name)
                                        <p class="text-xs font-medium text-slate-600">
                                            Karya: <span class="font-semibold text-slate-900">{{ $work->contributor_name }}</span>
                                        </p>
                                    @endif

                                    <p class="text-slate-600 text-xs sm:text-sm line-clamp-2 leading-relaxed font-normal">
                                        {{ Str::limit(strip_tags($work->description ?? ''), 100) }}
                                    </p>

                                </div>
                            </div>

                            <!-- Footer Kartu -->
                            <div class="px-5 pb-4 pt-3 border-t border-slate-50 mt-auto flex items-center justify-between text-xs font-semibold">
                                {{-- 1. "Lihat Karya" DENGAN WARNA SLATE / ABU-ABU --}}
                                <span class="inline-flex items-center gap-1 text-slate-500 group-hover:text-slate-900 transition-colors">
                                    <span>Lihat Karya</span>
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>

                                {{-- 2. "Demo" DENGAN WARNA ORANYE --}}
                                @if($work->demo_url)
                                    <span class="text-orange-600 group-hover:text-orange-700 transition-colors inline-flex items-center gap-1">
                                        <span>Buka Demo</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>

                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="pt-4">
                    {{ $studentWorks->links() }}
                </div>
            @else
                <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto">
                    <p class="font-sans text-sm font-semibold text-slate-700">Karya siswa tidak ditemukan.</p>
                    @if(!empty($search))
                        <p class="text-xs text-slate-500 mt-1">Tidak ada hasil pencarian untuk kata kunci "{{ $search }}".</p>
                    @endif
                    <a href="{{ route('public.student-works.index') }}" class="inline-block mt-3 text-xs font-semibold text-orange-600 hover:underline">Tampilkan Semua Karya</a>
                </div>
            @endif

        </div>
    </section>

@endsection
@extends('layouts.public')

@section('title', 'Kegiatan & Agenda - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS & BERSIH) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-24 sm:pb-32 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- JUDUL UTAMA (HANYA JUDUL) --}}
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Kegiatan &amp; Agenda PPLG
            </h1>

        </div>
    </section>

    <!-- ================= 2. KONTEN DAFTAR KEGIATAN ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- FORM PENCARIAN (DIPINDAHKAN KE SINI) -->
            <div class="max-w-md mx-auto">
                <form action="{{ route('public.activities.index') }}" method="GET" class="flex gap-2">
                    <div class="relative w-full">
                        <input type="text" 
                               name="q" 
                               value="{{ $search }}" 
                               placeholder="Cari kegiatan atau topik..." 
                               class="w-full px-4 py-2.5 rounded-xl text-slate-900 bg-slate-50 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 border border-slate-200 shadow-xs font-sans">
                    </div>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl transition shrink-0 shadow-sm cursor-pointer font-sans">
                        Cari
                    </button>
                </form>
            </div>

            @if($activities->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                    @foreach($activities as $activity)
                        @php
                            $formattedDate = $activity->event_date 
                                ? \Carbon\Carbon::parse($activity->event_date)->translatedFormat('d M Y') 
                                : \Carbon\Carbon::parse($activity->created_at)->translatedFormat('d M Y');
                        @endphp

                        <article class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col group h-full">
                            
                            {{-- Cover Image --}}
                            <a href="{{ route('public.activities.show', $activity->slug ?? $activity->id) }}" class="aspect-[16/10] bg-slate-100 overflow-hidden block relative border-b border-slate-100">
                                @if($activity->cover_url)
                                    <img src="{{ $activity->cover_url }}" alt="{{ $activity->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 font-medium text-xs bg-slate-100">
                                        Gambar tidak tersedia
                                    </div>
                                @endif

                                {{-- Badge Tanggal --}}
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/60 shadow-xs">
                                    <p class="text-[10px] font-bold text-orange-600">
                                        {{ $formattedDate }}
                                    </p>
                                </div>
                            </a>

                            {{-- Text Content --}}
                            <div class="p-5 flex-grow flex flex-col justify-between bg-white space-y-4">
                                <div>
                                    <h3 class="font-sans font-semibold text-base text-slate-900 group-hover:text-orange-600 transition-colors leading-snug line-clamp-2 mb-2">
                                        <a href="{{ route('public.activities.show', $activity->slug ?? $activity->id) }}">
                                            {{ $activity->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="font-sans text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed font-normal">
                                        {{ Str::limit(strip_tags($activity->content ?? ''), 110) }}
                                    </p>
                                </div>

                                {{-- Action Link --}}
                                <div class="pt-3 border-t border-slate-50 flex items-center justify-end">
                                    <a href="{{ route('public.activities.show', $activity->slug ?? $activity->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-orange-600 group-hover:text-orange-700 transition-colors">
                                        <span>Baca Selengkapnya</span>
                                        <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    {{ $activities->links() }}
                </div>
            @else
                <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto">
                    <p class="font-sans text-sm font-semibold text-slate-700">Kegiatan tidak ditemukan.</p>
                    @if($search)
                        <p class="text-xs mt-1 text-slate-500">Tidak ada hasil pencarian untuk kata kunci "{{ $search }}".</p>
                        <a href="{{ route('public.activities.index') }}" class="inline-block mt-3 text-xs font-semibold text-orange-600 hover:underline">Tampilkan Semua Kegiatan</a>
                    @endif
                </div>
            @endif

        </div>
    </section>

@endsection
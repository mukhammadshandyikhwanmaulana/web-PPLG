@extends('layouts.public')

@section('title', 'Kegiatan & Agenda - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS & BERSIH) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-24 sm:pb-32 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- JUDUL UTAMA --}}
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Kegiatan &amp; Agenda PPLG
            </h1>

        </div>
    </section>

    <!-- ================= 2. KONTEN DAFTAR KEGIATAN ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- FORM PENCARIAN -->
            <div class="max-w-md mx-auto">
                <form action="{{ route('public.activities.index') }}" method="GET" class="flex gap-2">
                    <div class="relative w-full">
                        <input type="text" 
                               name="q" 
                               value="{{ $search ?? '' }}" 
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
                <!-- GRID KEGIATAN (3 KOLOM KONSISTEN SEPERTI BERANDA) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    @foreach($activities as $activity)
                        @php
                            $imageUrl = $activity->cover_url;
                            $formattedDate = $activity->event_date 
                                ? \Carbon\Carbon::parse($activity->event_date)->translatedFormat('d F Y') 
                                : ($activity->created_at ? \Carbon\Carbon::parse($activity->created_at)->translatedFormat('d F Y') : '');
                            $photosCount = $activity->galleries ? $activity->galleries->count() : 0;
                            $detailUrl = route('public.activities.show', $activity->slug ?? $activity->id);
                        @endphp

                        <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between h-full group relative">
                            
                            <a href="{{ $detailUrl }}" class="flex flex-col h-full p-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-2xl" aria-label="Detail kegiatan {{ $activity->title }}">
                                <div>
                                    {{-- THUMBNAIL FOTO (16:10 KONSISTEN BERANDA) --}}
                                    <div class="w-full aspect-[16/10] rounded-xl bg-slate-100 relative select-none shrink-0 mb-3">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $activity->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300 rounded-xl"
                                                 loading="lazy"
                                                 decoding="async"
                                                 onerror="this.src='{{ asset('images/placeholder-pplg.webp') }}'">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100 rounded-xl">
                                                <span class="font-sans text-xs font-medium">Tidak ada foto</span>
                                            </div>
                                        @endif

                                        @if($photosCount > 0)
                                            <div class="absolute top-2.5 left-2.5 bg-slate-900/80 backdrop-blur-md text-white font-sans text-[10px] font-semibold px-2 py-0.5 rounded shadow-sm flex items-center gap-1">
                                                <span>{{ $photosCount }} Foto</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- TANGGAL, JUDUL & DESKRIPSI --}}
                                    @if($formattedDate)
                                        <p class="font-sans text-xs font-medium text-slate-500 mb-1">
                                            {{ $formattedDate }}
                                        </p>
                                    @endif

                                    <h3 class="font-sans font-semibold text-slate-900 text-base sm:text-lg leading-snug tracking-tight line-clamp-2 group-hover:text-orange-600 transition-colors mb-1.5">
                                        {{ $activity->title }}
                                    </h3>

                                    <p class="font-sans text-slate-600 text-xs sm:text-sm leading-relaxed line-clamp-2">
                                        {{ Str::limit(strip_tags($activity->description ?? $activity->content ?? ''), 90) }}
                                    </p>
                                </div>

                                {{-- FOOTER KARTU --}}
                                <div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-end text-xs font-semibold text-orange-600 group-hover:text-orange-700 transition-colors">
                                    <span class="flex items-center gap-1">
                                        Lihat Detail
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
                    {{ $activities->links() }}
                </div>
            @else
                <!-- DATA KOSONG -->
                <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto">
                    <p class="font-sans text-sm font-semibold text-slate-700">Kegiatan tidak ditemukan.</p>
                    @if(!empty($search))
                        <p class="text-xs mt-1 text-slate-500">Tidak ada hasil pencarian untuk kata kunci "{{ $search }}".</p>
                    @endif
                    <a href="{{ route('public.activities.index') }}" class="inline-block mt-3 text-xs font-semibold text-orange-600 hover:underline">Tampilkan Semua Kegiatan</a>
                </div>
            @endif

        </div>
    </section>

@endsection
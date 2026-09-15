@extends('layouts.public')

@section('title', 'Galeri Dokumentasi - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-20 sm:pb-28 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center w-full space-y-6">
            
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Galeri Dokumentasi
            </h1>

            {{-- TAB FILTER KATEGORI --}}
            <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                <a href="{{ route('public.galleries.index') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition font-sans {{ empty($activeCategory) ? 'bg-slate-900 text-white shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                    Semua Foto
                </a>
                <a href="{{ route('public.galleries.index', ['category' => 'kegiatan']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition font-sans {{ $activeCategory === 'kegiatan' ? 'bg-slate-900 text-white shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                    Kegiatan
                </a>
                <a href="{{ route('public.galleries.index', ['category' => 'prestasi']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition font-sans {{ $activeCategory === 'prestasi' ? 'bg-slate-900 text-white shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                    Prestasi
                </a>
                <a href="{{ route('public.galleries.index', ['category' => 'karya']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold transition font-sans {{ $activeCategory === 'karya' ? 'bg-slate-900 text-white shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                    Karya Siswa
                </a>
            </div>

        </div>
    </section>

    <!-- ================= 2. GRID GALERI DOKUMENTASI ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            @if($galleries->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 items-stretch">
                    @foreach($galleries as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between group h-full">
                            
                            {{-- Container Foto --}}
                            <div class="w-full aspect-[4/3] bg-slate-100 border-b border-slate-100 overflow-hidden relative select-none">
                                <img src="{{ $item['photo_url'] }}" 
                                     alt="{{ $item['title'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy"
                                     decoding="async">

                                {{-- Badge Kategori --}}
                                <div class="absolute top-2.5 left-2.5">
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-bold text-orange-600 bg-white/95 backdrop-blur-md border border-white/60 rounded-md shadow-xs uppercase tracking-wider">
                                        {{ $item['category'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- Text Content --}}
                            <div class="p-3.5 sm:p-4 flex-grow flex flex-col justify-between space-y-2">
                                <h3 class="font-sans font-semibold text-slate-900 text-xs sm:text-sm leading-snug line-clamp-2" title="{{ $item['title'] }}">
                                    {{ $item['title'] }}
                                </h3>

                                <div class="pt-2 border-t border-slate-50 flex items-center justify-end">
                                    <a href="{{ $item['detail_route'] }}" class="text-[10px] font-semibold text-orange-600 hover:text-orange-700 hover:underline shrink-0">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    {{ $galleries->links() }}
                </div>
            @else
                <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto">
                    <p class="font-sans text-sm font-semibold text-slate-700">Belum ada foto galeri yang ditemukan.</p>
                </div>
            @endif

        </div>
    </section>

@endsection
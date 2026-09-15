@extends('layouts.public')

@section('title', 'Pertanyaan Umum (FAQ) - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS & BERSIH) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-24 sm:pb-32 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- JUDUL UTAMA (HANYA JUDUL) --}}
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Pertanyaan Sering Diajukan (FAQ)
            </h1>

        </div>
    </section>

    <!-- ================= 2. KONTEN FAQ ACCORDION ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- FORM PENCARIAN (DIPINDAHKAN KE SINI) -->
            <div class="max-w-md mx-auto">
                <form action="{{ route('public.faq.index') }}" method="GET" class="flex gap-2">
                    <div class="relative w-full">
                        <input type="text" 
                               name="q" 
                               value="{{ $search }}" 
                               placeholder="Cari pertanyaan..." 
                               class="w-full px-4 py-2.5 rounded-xl text-slate-900 bg-slate-50 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 border border-slate-200 shadow-xs font-sans">
                    </div>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl transition shrink-0 shadow-sm cursor-pointer font-sans">
                        Cari
                    </button>
                </form>
            </div>

            @if($faqs->isNotEmpty())
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                        <details class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm transition-all duration-200 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between p-5 sm:p-6 cursor-pointer font-semibold text-slate-900 group-open:text-orange-600 transition-colors">
                                <span class="text-sm sm:text-base leading-snug tracking-tight">{{ $faq->question }}</span>
                                <span class="ml-4 shrink-0 transition-transform duration-300 group-open:-rotate-180">
                                    <svg class="w-5 h-5 text-slate-400 group-open:text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </span>
                            </summary>
                            
                            <div class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-slate-600 border-t border-slate-100 pt-4 leading-relaxed font-normal tracking-tight">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            @else
                <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto">
                    <p class="font-sans text-sm font-semibold text-slate-700">Pertanyaan tidak ditemukan.</p>
                    @if($search)
                        <p class="text-xs mt-1 text-slate-500">Tidak ada hasil untuk pencarian "{{ $search }}".</p>
                        <a href="{{ route('public.faq.index') }}" class="inline-block mt-3 text-xs font-semibold text-orange-600 hover:underline">Lihat Semua FAQ</a>
                    @endif
                </div>
            @endif

        </div>
    </section>

@endsection
@extends('layouts.public')

@section('title', $achievement->title . ' - Prestasi PPLG')

@section('content')

    @php
        $imageUrl = $achievement->document_url;
        $formattedDate = $achievement->achievement_date 
            ? $achievement->achievement_date->translatedFormat('d F Y') 
            : ($achievement->created_at ? $achievement->created_at->translatedFormat('d F Y') : '');

        $levelText = is_object($achievement->level) && property_exists($achievement->level, 'value') 
            ? $achievement->level->value 
            : (string) $achievement->level;

        $allGalleryPhotos = collect();
        if ($imageUrl) {
            $allGalleryPhotos->push($imageUrl);
        }
    @endphp

    <div x-data="{
            previewOpen: false,
            currentIndex: 0,
            previewPhotos: {{ json_encode($allGalleryPhotos->all()) }},
            openPreview(index) {
                this.currentIndex = index;
                this.previewOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closePreview() {
                this.previewOpen = false;
                document.body.classList.remove('overflow-hidden');
            }
         }"
         @keydown.escape.window="closePreview()">

        <!-- HERO HEADER -->
        <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-20 sm:pb-28 flex items-center justify-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="font-sans font-bold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                    Detail Prestasi
                </h1>
            </div>
        </section>

        <!-- KONTEN UTAMA -->
        <article class="py-12 sm:py-16 bg-slate-50 font-sans border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- KOLOM KIRI (KETERANGAN & SERTIFIKAT UTAMA) -->
                    <div class="lg:col-span-8 space-y-8">

                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <div class="flex flex-wrap items-center gap-3">
                                @if(!empty($levelText))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-full">
                                        Tingkat {{ $levelText }}
                                    </span>
                                @endif
                                @if($formattedDate)
                                    <span class="text-xs text-slate-400 font-medium">Tanggal Capaian: {{ $formattedDate }}</span>
                                @endif
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight leading-snug">
                                {{ $achievement->title }}
                            </h1>

                            @if($achievement->contributor_name)
                                <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">🏆</div>
                                    <div>
                                        <p class="text-[11px] text-slate-400 font-medium">Peraih Prestasi / Juara:</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $achievement->contributor_name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($imageUrl)
                            <div class="bg-white p-3 rounded-3xl border border-slate-200/80 shadow-xs">
                                <div class="relative rounded-2xl overflow-hidden bg-slate-950 aspect-[4/3] sm:aspect-[16/10] group cursor-pointer flex items-center justify-center"
                                     @click="openPreview(0)">
                                    <img src="{{ $imageUrl }}" alt="Sertifikat" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </div>
                            </div>
                        @endif

                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Rincian Pencapaian</h3>
                            <div class="text-slate-700 leading-relaxed text-sm sm:text-base space-y-3">
                                {!! nl2br(e($achievement->description)) !!}
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('public.achievements.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-semibold rounded-2xl border border-slate-200 transition shadow-xs">
                                ← Kembali ke Daftar Prestasi
                            </a>
                        </div>
                    </div>

                    <!-- KOLOM KANAN (SIDEBAR STICKY: THUMBNAIL + JUDUL) -->
                    <div class="lg:col-span-4 sticky top-28 space-y-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                                <span>Prestasi Lainnya</span>
                                <a href="{{ route('public.achievements.index') }}" class="text-xs text-orange-600 hover:text-orange-700 font-semibold">Semua →</a>
                            </h3>

                            @if(isset($otherAchievements) && $otherAchievements->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($otherAchievements as $other)
                                        @php
                                            $otherImg = $other->document_url;
                                            $otherLevel = is_object($other->level) && property_exists($other->level, 'value') 
                                                ? $other->level->value 
                                                : (string) $other->level;
                                        @endphp
                                        <a href="{{ route('public.achievements.show', $other->slug ?? $other->id) }}" 
                                           class="group flex gap-3 items-center p-2 rounded-2xl hover:bg-orange-50/60 transition border border-transparent hover:border-orange-100">
                                            <!-- THUMBNAIL -->
                                            <div class="w-14 h-14 bg-slate-100 rounded-xl overflow-hidden shrink-0 border border-slate-200">
                                                @if($otherImg)
                                                    <img src="{{ $otherImg }}" alt="{{ $other->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[9px]">No Image</div>
                                                @endif
                                            </div>
                                            <!-- JUDUL -->
                                            <div class="flex-1 min-w-0">
                                                <span class="text-[9px] text-orange-600 font-bold uppercase block mb-0.5">
                                                    {{ $otherLevel }}
                                                </span>
                                                <h4 class="font-semibold text-slate-900 text-xs line-clamp-2 group-hover:text-orange-600 transition">
                                                    {{ $other->title }}
                                                </h4>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Belum ada prestasi lainnya.</p>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </article>

        <!-- MODAL LIGHTBOX -->
        <div x-show="previewOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs" x-cloak>
            <div class="relative max-w-4xl w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col max-h-[90vh]" @click.away="closePreview()">
                <div class="flex items-center justify-between p-4 border-b border-slate-800 text-white shrink-0">
                    <span class="text-xs text-slate-400 font-mono">Sertifikat / Dokumentasi Utama</span>
                    <button type="button" @click="closePreview()" class="text-slate-400 hover:text-white transition p-1.5 rounded-xl hover:bg-slate-800">&times;</button>
                </div>
                <div class="relative flex-1 flex items-center justify-center p-4 min-h-[350px] bg-slate-950">
                    <img :src="previewPhotos[0]" class="max-h-[65vh] max-w-full object-contain rounded-xl shadow-md">
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.public')

@section('title', 'Beranda - PPLG SMKN 1 Bangsri')

@section('content')

    @php
        // Persiapan data banner hero
        $slides = (isset($banners) && $banners->isNotEmpty())
            ? $banners->map(function($banner) {
                if ($banner->image_path) return Storage::url($banner->image_path);
                $photo = $banner->photos?->first() ?? $banner->media?->first();
                return $photo ? Storage::url($photo->file_path ?? $photo->path) : $banner->image_url;
            })->filter()
            : collect([asset('images/hero-bg.jpg')]);
    @endphp

    <!-- REDUCED MOTION SUPPORT & ACCESSIBILITY HELPER -->
    <style>
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    <!-- ================= 1. HERO SECTION ================= -->
    <section class="relative bg-slate-950 w-full min-h-screen flex flex-col justify-center items-center overflow-hidden font-sans" 
             x-data="{
                 activeSlide: 0,
                 slidesCount: {{ max($slides->count(), 1) }},
                 isPaused: false,
                 timer: null,
                 initTimer() {
                     if (this.slidesCount > 1) {
                         this.timer = setInterval(() => {
                             if (!this.isPaused) this.next();
                         }, 6000);
                     }
                 },
                 next() { this.activeSlide = (this.activeSlide + 1) % this.slidesCount; },
                 prev() { this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount; }
             }" 
             x-init="initTimer()"
             @mouseenter="isPaused = true"
             @mouseleave="isPaused = false"
             @focusin="isPaused = true"
             @focusout="isPaused = false">
        
        {{-- Background Image Slider --}}
        <div class="absolute inset-0 w-full h-full">
            @foreach($slides as $index => $imageUrl)
                <div x-show="activeSlide === {{ $index }}" 
                     x-transition:enter="transition-opacity ease-out duration-700"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-500"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 w-full h-full">
                    
                    <img src="{{ $imageUrl }}" 
                         alt="Suasana PPLG SMKN 1 Bangsri {{ $index + 1 }}" 
                         class="w-full h-full object-cover object-center" 
                         @if($loop->first) fetchpriority="high" @else loading="lazy" decoding="async" @endif
                         onerror="this.src='{{ asset('images/placeholder-pplg.webp') }}'">
                    
                    <div class="absolute inset-0 bg-slate-950/60"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-slate-750/80 via-transparent to-slate-750/90"></div>
                </div>
            @endforeach
        </div>

        {{-- Konten Utama Hero --}}
        <div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-28 sm:pt-36 lg:pt-40 pb-12 my-auto text-center flex flex-col items-center justify-center">
            <div class="max-w-4xl mx-auto flex flex-col items-center w-full">
                
                {{-- HEADING FORMAL & STATIS --}}
                <h1 class="font-sans font-bold text-3xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tighter leading-tight text-white drop-shadow-md mb-3">
                    Pengembangan Perangkat <span class="text-orange-500">Lunak</span> dan <span class="text-orange-500">Gim</span>
                </h1>

                {{-- TYPING ANIMATION UNTUK TAGLINE FORMAL --}}
                <div x-data="{
                    text: '',
                    words: ['Kreatif.', 'Inovatif.', 'Adaptif.', 'Siap Industri.'],
                    wordIndex: 0, charIndex: 0, isDeleting: false, typeSpeed: 80,

                    type() {
                        const currentWord = this.words[this.wordIndex];
                        if (this.isDeleting) {
                            this.text = currentWord.substring(0, this.charIndex - 1);
                            this.charIndex--;
                            this.typeSpeed = 40;
                        } else {
                            this.text = currentWord.substring(0, this.charIndex + 1);
                            this.charIndex++;
                            this.typeSpeed = 80;
                        }

                        if (!this.isDeleting && this.text === currentWord) {
                            this.isDeleting = true;
                            this.typeSpeed = 2200;
                        } else if (this.isDeleting && this.text === '') {
                            this.isDeleting = false;
                            this.wordIndex = (this.wordIndex + 1) % this.words.length;
                            this.typeSpeed = 300;
                        }
                        setTimeout(() => this.type(), this.typeSpeed);
                    }
                }" x-init="type()" class="h-8 flex items-center justify-center mb-4">
                    <p class="font-sans text-sm sm:text-lg md:text-xl text-slate-200 font-medium tracking-wide">
                        Membentuk Generasi <span x-text="text" class="text-orange-400 font-semibold border-b-2 border-orange-500 pb-0.5 ml-1"></span><span class="animate-pulse text-orange-500 font-bold ml-0.5" aria-hidden="true">|</span>
                    </p>
                </div>

                {{-- DESKRIPSI DINAMIS DARI ADMIN (RINGKASAN TENTANG PPLG) --}}
                <p class="font-sans text-xs sm:text-sm md:text-base text-slate-200/90 leading-relaxed max-w-2xl mx-auto mb-8 font-normal tracking-tight">
                    {{ $profile?->about_excerpt ?? 'Kompetensi keahlian di SMKN 1 Bangsri yang membekali siswa dengan keterampilan pengembangan perangkat lunak, teknologi digital, dan gim sesuai kebutuhan dunia industri.' }}
                </p>

                {{-- CTA HERO --}}
                <div class="flex items-center justify-center">
                    <a href="#sambutan-kaprog" 
                       class="inline-flex items-center justify-center px-6 py-3 text-xs sm:text-sm font-semibold text-white bg-transparent border border-white/80 hover:bg-orange-500 hover:border-orange-500 active:bg-orange-600 active:border-orange-600 rounded-lg transition-all duration-200 shadow-md uppercase tracking-wider cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                        <span>Jelajahi PPLG</span>
                    </a>
                </div>

            </div>
        </div>

    </section>

    <!-- ================= 2. SAMBUTAN KETUA KOMPETENSI KEAHLIAN ================= -->
    @if(isset($principalWelcome) && $principalWelcome->is_active)
        @php
            $staff = $principalWelcome->staffMember;
            
            $photoPath = $staff?->photo?->file_path 
                ?? $staff?->photo?->path 
                ?? $staff?->photo_path;

            $staffPhotoUrl = $photoPath 
                ? Storage::url($photoPath) 
                : asset('images/placeholder-staff.webp');
            
            $staffName = $staff?->name ?? 'Ketua Kompetensi Keahlian';

            // LOGIKA FILTERING JABATAN AGAR HANYA MUNCUL: KETUA KOMPETENSI KEAHLIAN PPLG
            $rawPosition = $staff?->position ?? '';
            $positionLower = strtolower($rawPosition);

            if (str_contains($positionLower, 'ketua') || str_contains($positionLower, 'kaprog') || str_contains($positionLower, 'kajur') || str_contains($positionLower, 'kepala')) {
                $staffTitle = 'KETUA KOMPETENSI KEAHLIAN PPLG';
            } else {
                $splitPos = explode(',', $rawPosition);
                $staffTitle = !empty($splitPos[0]) ? strtoupper(trim($splitPos[0])) : 'KETUA KOMPETENSI KEAHLIAN PPLG';
            }

            $contentLength = strlen($principalWelcome->content ?? '');
        @endphp

        <section id="sambutan-kaprog" class="py-10 sm:py-14 bg-white font-sans border-b border-slate-100">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" x-data="{ isExpanded: false }">
                    
                    {{-- FOTO & CARD NAMA KAPROG --}}
                    <div class="lg:col-span-4 flex flex-col items-center lg:items-start w-full">
                        <div class="relative w-full max-w-[240px] sm:max-w-[260px] mx-auto lg:mx-0 flex flex-col items-center">
                            
                            <div class="w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/80 shadow-sm aspect-[4/5]">
                                <img src="{{ $staffPhotoUrl }}" 
                                     alt="{{ $staffName }}" 
                                     class="w-full h-full object-cover object-top"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.src='{{ asset('images/placeholder-staff.webp') }}'">
                            </div>

                            <div class="relative z-10 -mt-5 w-[92%] bg-white px-3.5 py-2.5 rounded-xl border border-slate-200/80 shadow-sm text-center lg:text-left">
                                <h3 class="font-sans font-semibold text-slate-900 text-xs sm:text-sm leading-snug tracking-tight">
                                    {{ $staffName }}
                                </h3>
                                <p class="font-sans text-[11px] font-semibold text-orange-600 uppercase tracking-wider mt-0.5">
                                    {{ $staffTitle }}
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- TEKS SAMBUTAN --}}
                    <div class="lg:col-span-8 flex flex-col justify-start bg-slate-50 p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm">
                        
                        <div class="mb-3">
                            <h2 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight">
                                Selamat Datang di Kompetensi Keahlian 
                                <br> <span class="text-orange-500">Pengembangan Perangkat Lunak dan Gim </span>
                            </h2>
                        </div>
                        <br>
                        <div class="relative mb-3 overflow-hidden">
                            <div :class="isExpanded ? '' : 'line-clamp-5 sm:line-clamp-6'" 
                                 class="font-sans text-slate-700 text-sm sm:text-base leading-relaxed font-normal tracking-tight break-words">
                                {!! nl2br(e($principalWelcome->content)) !!}
                            </div>
                        </div>

                        @if($contentLength > 280)
                            <div class="shrink-0 pt-1">
                                <button @click="isExpanded = !isExpanded" 
                                        class="font-sans inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold text-orange-600 border border-orange-500/30 hover:bg-orange-500 hover:text-white rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 cursor-pointer">
                                    <span x-text="isExpanded ? 'Tampilkan Lebih Sedikit' : 'Baca Selengkapnya...'"></span>
                                </button>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </section>
    @endif

    <!-- ================= 2.5 SEKSYEN STATISTIK (RECTANGLE ORANYE) ================= -->
    <section id="statistik" class="py-8 bg-slate-50/60 font-sans border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- CONTAINER CARD RECTANGLE ORANYE --}}
            <div class="bg-orange-500 rounded-2xl shadow-md p-6 sm:p-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-0 divide-y md:divide-y-0 md:divide-x divide-white/20">
                    
                    {{-- 1. TAHUN BERDIRI --}}
                    <div class="flex flex-col items-center justify-center p-3 text-center">
                        <span class="font-extrabold text-4xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-none mb-2">
                            {{ $tahunBerdiri ?? 2010 }}
                        </span>
                        <span class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">
                            Tahun Berdiri
                        </span>
                    </div>

                    {{-- 2. KARYA SISWA --}}
                    <div class="flex flex-col items-center justify-center p-3 text-center pt-6 md:pt-3">
                        <span class="font-extrabold text-4xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-none mb-2">
                            {{ $totalStudentWorks ?? 0 }}
                        </span>
                        <span class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">
                            Karya Siswa
                        </span>
                    </div>

                    {{-- 3. PRESTASI --}}
                    <div class="flex flex-col items-center justify-center p-3 text-center pt-6 md:pt-3">
                        <span class="font-extrabold text-4xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-none mb-2">
                            {{ $totalAchievements ?? 0 }}
                        </span>
                        <span class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">
                            Prestasi
                        </span>
                    </div>

                    {{-- 4. FASILITAS --}}
                    <div class="flex flex-col items-center justify-center p-3 text-center pt-6 md:pt-3">
                        <span class="font-extrabold text-4xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-none mb-2">
                            {{ $totalFacilities ?? 0 }}
                        </span>
                        <span class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">
                            Fasilitas
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ================= 3. SEKSYEN KARYA SISWA ================= -->
    <section id="karya-siswa" class="py-12 sm:py-16 bg-slate-50/50 font-sans border-b border-slate-100">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-12 items-end">
                <div class="lg:col-span-6 space-y-1">
                    <h2 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight leading-tight">
                        Karya Siswa
                    </h2>
                </div>
                <div class="lg:col-span-6">
                    <p class="font-sans text-sm text-slate-600 leading-relaxed font-normal tracking-tight">
                        Showcase aplikasi web, mobile, gim interaktif, dan perangkat IoT buatan siswa PPLG SMKN 1 Bangsri.
                    </p>
                </div>
            </div>

            @if(isset($studentWorks) && $studentWorks->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
                    @foreach($studentWorks->take(4) as $work)
                        @php
                            $imageUrl = $work->cover_url;
                            $demoUrl = $work->demo_url ?? $work->link ?? null;
                            $detailUrl = route('public.student-works.show', $work->slug);
                        @endphp

                        <article class="flex flex-col bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm hover:shadow-md transition-all duration-200 h-full w-full overflow-hidden group">
                            
                            <div class="w-full aspect-[16/10] rounded-xl bg-slate-100 overflow-hidden relative select-none shrink-0 mb-3">
                                <a href="{{ $detailUrl }}" class="block w-full h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500" aria-label="Lihat detail {{ $work->title }}">
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" 
                                             alt="{{ $work->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.src='{{ asset('images/placeholder-pplg.webp') }}'">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                            <span class="font-sans text-xs font-medium">Tidak ada preview</span>
                                        </div>
                                    @endif

                                    @if($work->is_featured)
                                        <div class="absolute top-2.5 left-2.5 bg-orange-500 text-white font-sans px-2 py-0.5 rounded font-semibold text-[9px] uppercase tracking-wider shadow-sm">
                                            Unggulan
                                        </div>
                                    @endif
                                </a>
                            </div>

                            <div class="flex flex-col flex-grow justify-between space-y-2">
                                <div class="space-y-1">
                                    <h3 class="font-sans font-semibold text-slate-900 text-base leading-snug tracking-tight line-clamp-2 break-words group-hover:text-orange-600 transition-colors">
                                        <a href="{{ $detailUrl }}" class="focus:outline-none focus-visible:underline">
                                            {{ $work->title }}
                                        </a>
                                    </h3>

                                    @if($work->contributor_name)
                                        <p class="font-sans text-xs font-medium text-slate-600 truncate">
                                            Pembuat: <span class="text-slate-800 font-semibold">{{ $work->contributor_name }}</span>
                                        </p>
                                    @endif
                                </div>

                                @if($demoUrl)
                                    <div class="pt-2.5 border-t border-slate-100 mt-auto flex items-center justify-end w-full">
                                        <a href="{{ $demoUrl }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="font-sans inline-flex items-center gap-1 text-xs text-orange-600 hover:text-orange-700 font-semibold transition-colors shrink-0 focus:outline-none focus-visible:ring-1 focus-visible:ring-orange-500 rounded-sm">
                                            <span>Buka Demo</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>

                        </article>
                    @endforeach
                </div>

                <div class="flex items-center justify-center pt-2">
                    <a href="{{ route('public.student-works.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 font-sans text-xs sm:text-sm font-semibold text-slate-800 bg-white hover:bg-orange-500 hover:text-white border border-slate-300 hover:border-orange-500 rounded-lg transition-all duration-200 tracking-wider uppercase cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        <span>Lihat Semua Karya</span>
                    </a>
                </div>
            @else
                <div class="p-6 text-center bg-white rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-xl mx-auto">
                    <p class="font-sans text-sm">Belum ada data karya siswa yang dipublikasikan.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- ================= 4. SEKSYEN PRESTASI ================= -->
    <section id="prestasi" class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-12 items-end">
                <div class="lg:col-span-6 space-y-1">
                    <h2 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight leading-tight">
                        Prestasi Terkini
                    </h2>
                </div>
                <div class="lg:col-span-6">
                    <p class="font-sans text-sm text-slate-600 leading-relaxed font-normal tracking-tight">
                        Berbagai pencapaian siswa PPLG dalam bidang teknologi, pengembangan perangkat lunak, gim, dan kompetisi keahlian lainnya.
                    </p>
                </div>
            </div>

            @if(isset($achievements) && $achievements->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    @foreach($achievements->take(3) as $achievement)
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

                        <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between h-full group relative overflow-hidden">
                            
                            <a href="{{ $detailUrl }}" class="flex flex-col h-full p-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-2xl" aria-label="Detail prestasi {{ $achievement->title }}">
                                <div>
                                    <div class="w-full aspect-[16/10] bg-slate-100 rounded-xl overflow-hidden relative select-none mb-4">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $achievement->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300"
                                                 loading="lazy"
                                                 decoding="async"
                                                 onerror="this.src='{{ asset('images/placeholder-pplg.webp') }}'">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                                <span class="font-sans text-xs font-medium">Tidak ada foto</span>
                                            </div>
                                        @endif

                                        @if(!empty($levelText))
                                            <div class="absolute top-3 left-3 bg-orange-500 text-white font-sans text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                                {{ strtoupper($levelText) }}
                                            </div>
                                        @endif
                                    </div>

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

                <div class="pt-2 flex items-center justify-center">
                    <a href="{{ route('public.achievements.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 font-sans text-xs sm:text-sm font-semibold text-slate-800 bg-white hover:bg-orange-500 hover:text-white border border-slate-300 hover:border-orange-500 rounded-lg transition-all duration-200 tracking-wider uppercase cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        <span>Lihat Semua Prestasi</span>
                    </a>
                </div>
            @else
                <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500">
                    <p class="font-sans text-sm">Belum ada data prestasi yang dipublikasikan.</p>
                </div>
            @endif

        </div>

    </section>

    <!-- ================= 5. SEKSYEN KEGIATAN KEAHLIAN ================= -->
    <section id="kegiatan" class="py-12 sm:py-16 bg-slate-50/50 font-sans border-b border-slate-100">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-12 items-end">
                <div class="lg:col-span-6 space-y-1">
                    <h2 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight leading-tight">
                        Kegiatan Jurusan
                    </h2>
                </div>
                <div class="lg:col-span-6">
                    <p class="font-sans text-sm text-slate-600 leading-relaxed font-normal tracking-tight">
                        Dokumentasi aktivitas praktikum, workshop, kunjungan industri, dan agenda pembelajaran siswa PPLG.
                    </p>
                </div>
            </div>

            @if(isset($activities) && $activities->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    @foreach($activities->take(3) as $activity)
                        @php
                            $imageUrl = $activity->cover_url;
                            $formattedDate = $activity->event_date 
                                ? $activity->event_date->translatedFormat('d F Y') 
                                : ($activity->created_at ? $activity->created_at->translatedFormat('d F Y') : '');
                            $photosCount = $activity->galleries ? $activity->galleries->count() : 0;
                            $detailUrl = route('public.activities.show', $activity->slug);
                        @endphp

                        <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between h-full group relative overflow-hidden">
                            
                            <a href="{{ $detailUrl }}" class="flex flex-col h-full p-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-2xl" aria-label="Detail kegiatan {{ $activity->title }}">
                                <div>
                                    <div class="w-full aspect-[16/10] rounded-xl bg-slate-100 overflow-hidden relative select-none shrink-0 mb-3">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $activity->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300"
                                                 loading="lazy"
                                                 decoding="async"
                                                 onerror="this.src='{{ asset('images/placeholder-pplg.webp') }}'">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                                <span class="font-sans text-xs font-medium">Tidak ada foto</span>
                                            </div>
                                        @endif

                                        @if($photosCount > 0)
                                            <div class="absolute top-2.5 left-2.5 bg-slate-900/80 backdrop-blur-md text-white font-sans text-[10px] font-semibold px-2 py-0.5 rounded shadow-sm flex items-center gap-1">
                                                <span>{{ $photosCount }} Foto</span>
                                            </div>
                                        @endif
                                    </div>

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

                <div class="flex items-center justify-center pt-2">
                    <a href="{{ route('public.activities.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 font-sans text-xs sm:text-sm font-semibold text-slate-800 bg-white hover:bg-orange-500 hover:text-white border border-slate-300 hover:border-orange-500 rounded-lg transition-all duration-200 tracking-wider uppercase cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        <span>Lihat Semua Kegiatan</span>
                    </a>
                </div>
            @else
                <div class="p-6 text-center bg-white rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-xl mx-auto">
                    <p class="font-sans text-sm">Belum ada postingan kegiatan yang dipublikasikan.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- ================= 6. SEKSYEN FASILITAS & LABORATORIUM ================= -->
    <section id="fasilitas" class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-12 items-end">
                <div class="lg:col-span-6 space-y-1">
                    <h2 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight leading-tight">
                        Fasilitas &amp; Laboratorium
                    </h2>
                </div>
                <div class="lg:col-span-6">
                    <p class="font-sans text-sm text-slate-600 leading-relaxed font-normal tracking-tight">
                        Sarana praktikum modern dan laboratorium komputer standar industri untuk menunjang proses pembelajaran siswa PPLG.
                    </p>
                </div>
            </div>

            @if(isset($facilities) && $facilities->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                    @foreach($facilities->take(6) as $facility)
                        @php
                            $facilityPhotoPath = $facility->photo?->file_path ?? $facility->photo?->path ?? $facility->image_path;
                            $facilityPhotoUrl = $facilityPhotoPath 
                                ? Storage::url($facilityPhotoPath) 
                                : asset('images/placeholder-facility.webp');
                        @endphp

                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between group hover:shadow-md transition-all duration-200">
                            <div class="w-full aspect-[16/10] bg-slate-100 overflow-hidden relative select-none">
                                <img src="{{ $facilityPhotoUrl }}" 
                                     alt="{{ $facility->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.src='{{ asset('images/placeholder-facility.webp') }}'">
                            </div>

                            <div class="p-5 flex flex-col flex-grow justify-between space-y-2">
                                <div class="space-y-1.5">
                                    <h3 class="font-sans font-semibold text-slate-900 text-base leading-snug tracking-tight">
                                        {{ $facility->name }}
                                    </h3>
                                    @if($facility->description)
                                        <p class="font-sans text-slate-600 text-xs sm:text-sm leading-relaxed line-clamp-3">
                                            {{ strip_tags($facility->description) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-xl mx-auto">
                    <p class="font-sans text-sm">Belum ada data fasilitas yang dipublikasikan.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- ================= 7. SEKSYEN MITRA INDUSTRI ================= -->
    <section id="mitra" class="py-10 sm:py-14 bg-white font-sans border-b border-slate-100">
        
        @if(isset($partners) && $partners->isNotEmpty())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showAllPartners: false }">
                
                <div class="bg-orange-500 rounded-2xl relative overflow-hidden shadow-lg p-6 sm:p-8">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start relative z-10">
                        
                        {{-- AREA KIRI: CTA UTAMA (AJUKAN KOLABORASI) --}}
                        <div class="lg:col-span-4 text-center lg:text-left space-y-4">
                            <div class="space-y-1">
                                <h2 class="font-sans font-bold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                                    Mitra Industri
                                </h2>
                            </div>

                            <div>
                                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=rplsmkn1bangsri@gmail.com" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center px-5 py-2.5 font-sans text-xs sm:text-sm font-semibold text-slate-900 bg-white hover:bg-slate-900 hover:text-white rounded-lg transition-all duration-200 shadow-sm uppercase tracking-wider cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                                    <span>Ajukan Kolaborasi</span>
                                </a>
                            </div>
                        </div>

                        {{-- AREA KANAN: GRID LOGO + EXPAND INLINE TOGGLE --}}
                        <div class="lg:col-span-8 w-full space-y-4">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-stretch justify-center">
                                @foreach($partners as $index => $partner)
                                    @php
                                        $logoUrl = $partner->logo_url;
                                    @endphp

                                    <div x-show="showAllPartners || {{ $index }} < 8"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="group text-center flex flex-col items-center justify-between p-2.5 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 border border-white/20 h-full min-h-[110px]">
                                        
                                        @if(!empty($partner->website_url))
                                            <a href="{{ $partner->website_url }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               title="Kunjungi website {{ $partner->name }}"
                                               class="flex items-center justify-center w-full h-14 bg-white rounded-lg p-2 shadow-inner group-hover:scale-102 transition-transform duration-200 shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                               aria-label="Website {{ $partner->name }}">
                                        @else
                                            <div class="flex items-center justify-center w-full h-14 bg-white rounded-lg p-2 shadow-inner shrink-0">
                                        @endif

                                            @if($logoUrl)
                                                <img src="{{ $logoUrl }}" 
                                                     alt="{{ $partner->name }}" 
                                                     class="max-w-full max-h-full object-contain"
                                                     loading="lazy"
                                                     decoding="async"
                                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                <span class="hidden font-sans text-[10px] font-semibold text-slate-800 text-center line-clamp-2">
                                                    {{ $partner->name }}
                                                </span>
                                            @else
                                                <span class="font-sans text-[10px] font-semibold text-slate-800 text-center line-clamp-2">
                                                    {{ $partner->name }}
                                                </span>
                                            @endif

                                        @if(!empty($partner->website_url))
                                            </a>
                                        @else
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-center gap-1 w-full pt-1.5 my-auto">
                                            <span class="font-sans text-[11px] font-semibold text-white truncate">
                                                {{ $partner->name }}
                                            </span>
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                            @if($partners->count() > 8)
                                <div class="flex justify-center lg:justify-end pt-1">
                                    <button @click="showAllPartners = !showAllPartners" 
                                            type="button"
                                            class="inline-flex items-center gap-1.5 font-sans text-xs sm:text-sm font-semibold text-white hover:text-slate-900 transition-colors focus:outline-none focus-visible:underline cursor-pointer">
                                        <span x-text="showAllPartners ? 'Tampilkan Lebih Sedikit' : 'Lihat Seluruh {{ $partners->count() }} Mitra Industri'"></span>
                                        <svg class="w-4 h-4 transition-transform duration-200" 
                                             :class="showAllPartners ? 'rotate-180' : ''" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>
        @else
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500">
                    <p class="font-sans text-sm">Belum ada mitra industri yang dipublikasikan.</p>
                </div>
            </div>
        @endif

    </section>

    <!-- ================= 8. SEKSYEN FAQ (KOLOM KANAN PERMANEN STICKY) ================= -->
    <section id="faq" class="py-12 sm:py-16 bg-slate-50/50 font-sans border-b border-slate-100">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                
                {{-- KIRI: LIST ACCORDION PERTANYAAN (DI-SCROLL) --}}
                <div class="lg:col-span-7 order-2 lg:order-1">
                    @if(isset($faqs) && $faqs->isNotEmpty())
                        <div class="space-y-3" x-data="{ activeFaq: null }">
                            @foreach($faqs as $index => $faq)
                                <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-200">
                                    
                                    <button @click="activeFaq = (activeFaq === {{ $index }} ? null : {{ $index }})"
                                            type="button"
                                            class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 font-sans font-semibold text-slate-900 text-sm sm:text-base hover:text-orange-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 cursor-pointer"
                                            :aria-expanded="activeFaq === {{ $index }}">
                                        <span class="leading-snug tracking-tight">{{ $faq->question }}</span>
                                        
                                        <span class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-600 transition-transform duration-200"
                                              :class="activeFaq === {{ $index }} ? 'bg-orange-100 text-orange-600' : ''">
                                            <svg class="w-4 h-4 transition-transform duration-200" 
                                                 :class="activeFaq === {{ $index }} ? 'rotate-45' : ''" 
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </span>
                                    </button>

                                    <div x-show="activeFaq === {{ $index }}"
                                         x-collapse
                                         class="px-4 pb-5 sm:px-5 sm:pb-5 font-sans text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500">
                            <p class="font-sans text-sm">Belum ada pertanyaan umum yang dipublikasikan.</p>
                        </div>
                    @endif
                </div>

                {{-- KANAN: JUDUL UTAMA FAQ (TETAP DIEM / STICKY DI POJOK ATAS LAYAR) --}}
                <div class="lg:col-span-5 order-1 lg:order-2">
                    <div class="space-y-4 lg:sticky lg:top-28">
                        <h2 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight leading-tight">
                            Pertanyaan yang Sering Diajukan (FAQ)
                        </h2>

                        <p class="font-sans text-sm text-slate-600 leading-relaxed font-normal tracking-tight">
                            Temukan jawaban atas pertanyaan umum mengenai program keahlian, pembelajaran, serta peluang karir di PPLG SMKN 1 Bangsri.
                        </p>

                        <div class="pt-2">
                            <a href="{{ route('public.faq.index') }}" 
                               class="inline-flex items-center justify-center px-6 py-2.5 font-sans text-xs sm:text-sm font-semibold text-slate-800 bg-white hover:bg-orange-500 hover:text-white border border-slate-300 hover:border-orange-500 rounded-lg transition-all duration-200 tracking-wider uppercase cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 shadow-sm">
                                <span>Lihat Semua FAQ</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

@endsection
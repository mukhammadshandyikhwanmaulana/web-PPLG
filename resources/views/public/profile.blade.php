@extends('layouts.public')

@section('title', 'Profil Jurusan - ' . config('app.name', 'PPLG SMKN 1 Bangsri'))

@section('content')

    <!-- ================= 1. HERO BANNER HEADER (ORANYE POLOS KONSISTEN) ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-24 sm:pb-32 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            {{-- JUDUL UTAMA --}}
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Profil Jurusan
            </h1>

        </div>
    </section>

    <!-- ================= 2. SEJARAH SINGKAT (ANCHOR ID: #sejarah) ================= -->
    <section id="sejarah" class="py-12 sm:py-16 bg-white border-b border-slate-100 font-sans scroll-mt-28">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 sm:p-10 shadow-sm">

                <div class="mb-6">
                    <h2 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight">
                        Sejarah
                    </h2>
                </div>

                @if(optional($profile)->history_content)
                    <div class="relative pl-5 sm:pl-6 border-l-4 border-orange-500 text-slate-700 leading-relaxed text-sm sm:text-base space-y-4 font-normal tracking-tight">
                        {!! nl2br(e($profile->history_content)) !!}
                    </div>
                @else
                    <div class="relative pl-5 sm:pl-6 border-l-4 border-orange-500 text-slate-700 leading-relaxed text-sm sm:text-base space-y-4 font-normal tracking-tight">
                        <p>Kompetensi Keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) SMKN 1 Bangsri didirikan untuk menjawab kebutuhan industri digital yang kian pesat. Berfokus pada pemrograman, rekayasa perangkat lunak, serta pengembangan gim interaktif, PPLG berkomitmen mencetak talenta digital berkualitas.</p>
                    </div>
                @endif

            </div>
        </div>
    </section>

    <!-- ================= 3. VISI & MISI (ANCHOR ID: #visi-misi) ================= -->
    <section id="visi-misi" class="py-12 sm:py-16 bg-slate-50/50 border-b border-slate-100 font-sans scroll-mt-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">

                <!-- Visi Jurusan -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 flex flex-col justify-between shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-orange-500"></div>
                    <div>
                        <h2 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight mb-4">Visi Keahlian</h2>
                        <div class="text-slate-800 leading-relaxed text-base sm:text-lg font-medium italic border-l-4 border-orange-500 pl-4 py-3 bg-orange-50/50 rounded-r-xl">
                            @if(optional($profile)->vision_content)
                                "{!! nl2br(e($profile->vision_content)) !!}"
                            @else
                                "Menjadi konsentrasi keahlian yang unggul, berkarakter, berdaya saing global, serta menghasilkan talenta digital terampil di bidang rekayasa perangkat lunak dan gim."
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Misi Jurusan -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 flex flex-col justify-between shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-orange-500"></div>
                    <div>
                        <h2 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight mb-4">Misi Keahlian</h2>

                        @if(optional($profile)->mission_content)
                            <div class="text-slate-700 text-sm sm:text-base leading-relaxed space-y-3 font-normal tracking-tight">
                                {!! nl2br(e($profile->mission_content)) !!}
                            </div>
                        @else
                            <ul class="space-y-4 text-slate-700 text-sm sm:text-base leading-relaxed font-normal tracking-tight">
                                <li class="flex items-start gap-3.5">
                                    <span class="w-7 h-7 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">01</span>
                                    <span class="pt-0.5">Menyelenggarakan pembelajaran berbasis proyek (Project-Based Learning) sesuai standar kebutuhan industri perangkat lunak.</span>
                                </li>
                                <li class="flex items-start gap-3.5">
                                    <span class="w-7 h-7 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">02</span>
                                    <span class="pt-0.5">Membentuk karakter talenta digital yang berintegritas, berakhlak mulia, adaptif, dan berjiwa wirausaha.</span>
                                </li>
                                <li class="flex items-start gap-3.5">
                                    <span class="w-7 h-7 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm mt-0.5">03</span>
                                    <span class="pt-0.5">Memperkuat kemitraan strategis dengan Dunia Usaha dan Dunia Industri (DUDI) untuk penyaluran riset dan karir.</span>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= 4. GURU & STAF PENGAJAR (ANCHOR ID: #guru-staf) ================= -->
    <section id="guru-staf" class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100 scroll-mt-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-12 items-end">
                <div class="lg:col-span-6 space-y-1">
                    <h2 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 tracking-tight leading-tight">
                        Guru &amp; Staf Pengajar
                    </h2>
                </div>
                <div class="lg:col-span-6 flex justify-start lg:justify-end">
                    <a href="{{ route('public.staff.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                        <span>Lihat Struktur Organisasi & Guru</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            @if(isset($staffMembers) && $staffMembers->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6 items-stretch">
                    @foreach($staffMembers as $staff)
                        @php
                            $photoPath = $staff->photo?->path ?? $staff->photo?->file_path ?? $staff->photo_path;
                            $staffPhotoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::url($photoPath) : asset('images/default-avatar.png');
                        @endphp

                        <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-center text-center space-y-3 group hover:shadow-md transition-all duration-200">
                            <div class="w-full aspect-[4/5] rounded-xl overflow-hidden bg-slate-100 border border-slate-100">
                                <img src="{{ $staffPhotoUrl }}" 
                                     alt="{{ $staff->name }}" 
                                     class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                            </div>

                            <div class="space-y-0.5 w-full">
                                <h3 class="font-sans font-semibold text-slate-900 text-xs sm:text-sm leading-snug tracking-tight line-clamp-1" title="{{ $staff->name }}">
                                    {{ $staff->name }}
                                </h3>

                                <p class="font-sans text-[11px] font-semibold text-orange-600 leading-tight uppercase tracking-wider line-clamp-2" title="{{ $staff->display_position }}">
                                    {{ $staff->display_position }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-xl mx-auto">
                    <p class="font-sans text-sm">Belum ada data guru &amp; staf yang dipublikasikan.</p>
                </div>
            @endif

        </div>
    </section>

@endsection
@extends('layouts.public')

@section('title', 'Selamat Datang di Portal PPLG — ' . config('app.name', 'SMKN 1 Bangsri'))

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white overflow-hidden py-20 lg:py-28">
        <!-- Background Overlay / Decorative Light -->
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center pointer-events-none" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-600/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-semibold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                        Kompetensi Keahlian Unggulan
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight text-white">
                        Pengembangan Perangkat Lunak & <span class="text-indigo-400">Gim</span>
                    </h1>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Mencetak generasi pengembang perangkat lunak, desainer sistem, dan pembuat gim yang kreatif, adaptif, serta berstandar industri global.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('public.profile') }}" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition text-center">
                            Jelajahi Profil Jurusan
                        </a>
                        <a href="{{ route('public.student-works.index') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-sm font-semibold rounded-xl transition text-center">
                            Lihat Karya Siswa
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm">
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-3xl transform rotate-3 scale-105 opacity-20 blur-lg"></div>
                        <div class="relative bg-slate-800/80 backdrop-blur-md border border-slate-700/80 rounded-3xl p-6 shadow-2xl text-center space-y-4">
                            <img src="{{ asset('images/logo-pplg.png') }}" 
                                 alt="Logo PPLG" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/150x150/4f46e5/white?text=PPLG';"
                                 class="w-24 h-24 object-contain mx-auto drop-shadow-lg">
                            <div>
                                <h3 class="text-lg font-bold text-white">SMK Negeri 1 Bangsri</h3>
                                <p class="text-xs text-slate-400 mt-1">PPLG • Software & Game Development</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Ringkasan Keunggulan -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Kenapa Memilih PPLG?</h2>
                <p class="text-sm text-slate-500 mt-2">Fasilitas dan kurikulum yang didesain langsung untuk memenuhi kebutuhan industri teknologi saat ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pemrograman Web & Mobile</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Mempelajari stack modern seperti Laravel, Livewire, Flutter, dan React untuk membangun aplikasi enterprise.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2V4zm-6 8a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2v-1zm12 0a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2v-1z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pengembangan Gim</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Merancang mekanik permainan, aset 2D/3D, serta membangun game menggunakan Unity dan Construct.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Kemitraan Industri</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Bekerja sama dengan perusahaan software house ternama untuk magang (PKL) dan penyaluran kerja.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
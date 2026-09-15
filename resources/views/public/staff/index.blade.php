@extends('layouts.public')

@section('title', 'Struktur Organisasi - ' . ($siteSetting->site_name ?? config('app.name', 'PPLG SMKN 1 Bangsri')))

@push('styles')
<style>
    /* STYLING BAGAN HIRARKI DENGAN GARIS PRESISI MURNI */
    .tree-org, .tree-org ul {
        display: flex;
        justify-content: center;
        position: relative;
        padding-top: 20px;
        transition: all 0.3s;
    }
    .tree-org li {
        float: left;
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 20px 12px 0 12px;
        transition: all 0.3s;
    }

    /* Garis Vertikal dan Horizontal Atas */
    .tree-org li::before, .tree-org li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #f97316; /* Warna Oranye */
        width: 50%;
        height: 20px;
    }
    .tree-org li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #f97316;
    }

    /* Hilangkan garis horizontal di Ujung Kiri & Ujung Kanan */
    .tree-org li:only-child::after, .tree-org li:only-child::before {
        display: none;
    }
    .tree-org li:only-child {
        padding-top: 0;
    }
    .tree-org li:first-child::before, .tree-org li:last-child::after {
        border: 0 none;
    }
    .tree-org li:last-child::before {
        border-right: 2px solid #f97316;
        border-radius: 0 8px 0 0;
    }
    .tree-org li:first-child::after {
        border-radius: 8px 0 0 0;
    }

    /* Garis Vertikal Turun dari Atasan ke Bawahan */
    .tree-org ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #f97316;
        width: 0;
        height: 20px;
    }
</style>
@endpush

@section('content')

    <!-- ================= 1. HERO BANNER HEADER ================= -->
    <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-20 sm:pb-28 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                Struktur Organisasi &amp; Guru
            </h1>
        </div>
    </section>

    <!-- ================= 2. KONTEN STRUKTUR ORGANISASI ================= -->
    <section class="py-12 sm:py-16 bg-white font-sans border-b border-slate-100 min-h-[600px]">
        
        <!-- WRAPPER OVERFLOW: MEMUNGKINKAN DIPUTAR/DIGESER HALUS DI HP -->
        <div class="w-full overflow-x-auto pb-8 pt-4">
            <div class="min-w-max mx-auto px-6 flex justify-center">

                @if(isset($allStaff) && $allStaff->isNotEmpty())

                    <div class="tree-org">
                        <ul>
                            <li>
                                <!-- LEVEL 1: KETUA PROGRAM -->
                                @if($ketua)
                                    @php
                                        $photoPath = $ketua->photo?->path ?? $ketua->photo?->file_path ?? $ketua->photo_path;
                                        $ketuaPhotoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::url($photoPath) : asset('images/default-avatar.png');
                                    @endphp

                                    <div class="inline-flex flex-col items-center bg-white border-2 border-orange-500 p-5 rounded-2xl shadow-md w-60 sm:w-64 text-center group hover:shadow-lg transition-all duration-200">
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden border-2 border-orange-500 shadow-sm mb-3 bg-slate-100">
                                            <img src="{{ $ketuaPhotoUrl }}" 
                                                 alt="{{ $ketua->name }}" 
                                                 class="w-full h-full object-cover object-top"
                                                 onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                        </div>
                                        <span class="bg-orange-500 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-1.5 shadow-xs">
                                            KETUA KOMPETENSI KEAHLIAN
                                        </span>
                                        <h3 class="font-sans font-semibold text-sm text-slate-900 line-clamp-1" title="{{ $ketua->name }}">
                                            {{ $ketua->name }}
                                        </h3>
                                        @if($ketua->expertise)
                                            <span class="text-[10px] text-slate-500 mt-1 line-clamp-1">
                                                {{ $ketua->expertise }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- LEVEL 2: GURU-GURU -->
                                @if(isset($gurus) && $gurus->isNotEmpty())
                                    <ul>
                                        @foreach($gurus as $guru)
                                            @php
                                                $photoPath = $guru->photo?->path ?? $guru->photo?->file_path ?? $guru->photo_path;
                                                $guruPhotoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::url($photoPath) : asset('images/default-avatar.png');
                                            @endphp
                                            <li>
                                                <div class="inline-flex flex-col items-center bg-white border border-slate-200 p-4 rounded-2xl shadow-sm w-48 sm:w-52 text-center group hover:shadow-md hover:border-orange-300 transition-all duration-200">
                                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full overflow-hidden border-2 border-orange-500 shadow-xs mb-2 bg-slate-100">
                                                        <img src="{{ $guruPhotoUrl }}" 
                                                             alt="{{ $guru->name }}" 
                                                             class="w-full h-full object-cover object-top"
                                                             onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                    </div>
                                                    <span class="bg-orange-50 text-orange-600 border border-orange-200 text-[10px] font-semibold uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-1">
                                                        GURU PRODUKTIF
                                                    </span>
                                                    <h4 class="font-sans font-semibold text-xs text-slate-900 line-clamp-1" title="{{ $guru->name }}">
                                                        {{ $guru->name }}
                                                    </h4>
                                                    @if($guru->expertise)
                                                        <span class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">
                                                            {{ $guru->expertise }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- LEVEL 3: STAF / LABORAN (JIKA ADA DI BAWAH GURU) -->
                                                @if(isset($staffs) && $staffs->isNotEmpty() && $loop->first)
                                                    <ul>
                                                        @foreach($staffs as $staf)
                                                            @php
                                                                $photoPath = $staf->photo?->path ?? $staf->photo?->file_path ?? $staf->photo_path;
                                                                $stafPhotoUrl = $photoPath ? \Illuminate\Support\Facades\Storage::url($photoPath) : asset('images/default-avatar.png');
                                                            @endphp
                                                            <li>
                                                                <div class="inline-flex flex-col items-center bg-white border border-slate-200 p-3.5 rounded-2xl shadow-sm w-40 sm:w-44 text-center group hover:shadow-md transition-all duration-200">
                                                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden border border-slate-300 shadow-xs mb-2 bg-slate-100">
                                                                        <img src="{{ $stafPhotoUrl }}" 
                                                                             alt="{{ $staf->name }}" 
                                                                             class="w-full h-full object-cover object-top"
                                                                             onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                                    </div>
                                                                    <span class="bg-slate-100 text-slate-700 border border-slate-200 text-[9px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1">
                                                                        STAF / LABORAN
                                                                    </span>
                                                                    <h5 class="font-sans font-semibold text-xs text-slate-900 line-clamp-1" title="{{ $staf->name }}">
                                                                        {{ $staf->name }}
                                                                    </h5>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        </ul>
                    </div>

                @else
                    <!-- Empty State -->
                    <div class="p-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-500 max-w-md mx-auto space-y-3">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-700">Data Tidak Ditemukan</h3>
                        <p class="text-xs text-slate-500">
                            Data guru &amp; staf belum tersedia saat ini.
                        </p>
                    </div>
                @endif

            </div>
        </div>
    </section>

@endsection
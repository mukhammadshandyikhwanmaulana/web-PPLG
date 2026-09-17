@extends('layouts.guru.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            Selamat Datang, {{ auth()->user()?->name ?? 'Bapak/Ibu Guru' }} 👋
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
            Ringkasan kontribusi karya, kegiatan, dan prestasi bimbingan Anda di jurusan PPLG.
        </p>
    </div>

    <!-- Stats Grid: 3 Kolom Khusus Guru -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6">
        
        <!-- Card: Karya Siswa Saya -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-5 hover:border-slate-300 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Karya Siswa Bimbingan</p>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-slate-900">{{ number_format($studentWorkCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Kegiatan Saya -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-5 hover:border-slate-300 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Laporan Kegiatan & Berita</p>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-slate-900">{{ number_format($activityCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Prestasi Saya -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-5 hover:border-slate-300 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Data Prestasi Dicatat</p>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 sm:mt-4">
                <p class="text-2xl sm:text-3xl font-bold text-slate-900">{{ number_format($achievementCount ?? 0) }}</p>
            </div>
        </div>

    </div>

    <!-- Section Tabel Kontribusi Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
        
        <!-- Karya Siswa Terbaru Saya -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900">Karya Siswa Terbaru Anda</h3>
                @if(\Illuminate\Support\Facades\Route::has('guru.karya-siswa.index'))
                    <a href="{{ route('guru.karya-siswa.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentStudentWorks ?? [] as $work)
                    @php
                        $enumPublished = class_exists('\App\Enums\PublishStatus') ? \App\Enums\PublishStatus::Published : 'published';
                        $isPublished = ($work->status === $enumPublished || $work->status?->value === 'published' || ($work->is_active ?? false));
                    @endphp
                    <div class="px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div class="space-y-0.5 pr-2 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 truncate" title="{{ $work->title ?? '' }}">
                                {{ $work->title ?? 'Judul Karya' }}
                            </p>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ $work->contributor_name ?? $work->student_name ?? 'Siswa Bimbingan' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $isPublished ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Anda belum mengunggah karya siswa.</div>
                @endforelse
            </div>
        </div>

        <!-- Prestasi Terbaru Saya -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900">Prestasi Terbaru Anda</h3>
                @if(\Illuminate\Support\Facades\Route::has('guru.prestasi.index'))
                    <a href="{{ route('guru.prestasi.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentAchievements ?? [] as $achievement)
                    @php
                        $enumPublished = class_exists('\App\Enums\PublishStatus') ? \App\Enums\PublishStatus::Published : 'published';
                        $isPublished = ($achievement->status === $enumPublished || $achievement->status?->value === 'published' || ($achievement->is_active ?? false));
                    @endphp
                    <div class="px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div class="space-y-0.5 pr-2 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 truncate" title="{{ $achievement->title ?? '' }}">
                                {{ $achievement->title ?? 'Judul Prestasi' }}
                            </p>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ $achievement->contributor_name ?? 'Pemenang / Kontributor' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $isPublished ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Anda belum mencatat data prestasi.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Section Laporan Kegiatan Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900">Laporan Kegiatan Terbaru Anda</h3>
            @if(\Illuminate\Support\Facades\Route::has('guru.kegiatan.index'))
                <a href="{{ route('guru.kegiatan.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
            @endif
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentActivities ?? [] as $activity)
                @php
                    $enumPublished = class_exists('\App\Enums\PublishStatus') ? \App\Enums\PublishStatus::Published : 'published';
                    $isPublished = ($activity->status === $enumPublished || $activity->status?->value === 'published' || ($activity->is_active ?? false));
                    
                    $formattedDate = '-';
                    if (isset($activity->event_date) && $activity->event_date) {
                        $formattedDate = is_string($activity->event_date) ? \Carbon\Carbon::parse($activity->event_date)->format('d M Y') : $activity->event_date->format('d M Y');
                    } elseif (isset($activity->created_at) && $activity->created_at) {
                        $formattedDate = is_string($activity->created_at) ? \Carbon\Carbon::parse($activity->created_at)->format('d M Y') : $activity->created_at->format('d M Y');
                    }
                @endphp
                <div class="px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/50 transition">
                    <div class="space-y-0.5 pr-2 min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-slate-800 truncate" title="{{ $activity->title ?? '' }}">
                            {{ $activity->title ?? 'Judul Kegiatan' }}
                        </p>
                        <p class="text-[11px] text-slate-400">{{ $formattedDate }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $isPublished ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                        {{ $isPublished ? 'Published' : 'Draft' }}
                    </span>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">Anda belum mengunggah laporan kegiatan.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
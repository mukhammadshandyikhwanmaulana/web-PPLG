@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Dashboard Admin</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ringkasan statistik data dan informasi sistem administrasi PPLG.</p>
        </div>
    </div>

    <!-- Stats Grid: Responsive 2 s.d. 4 Kolom -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
        
        <!-- Card: Jumlah Guru -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Total Guru / Staf</p>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($guruCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Fasilitas -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Total Fasilitas</p>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($facilityCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Prestasi -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Data Prestasi</p>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($achievementCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Karya Siswa -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Karya Siswa</p>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($studentWorkCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Kegiatan -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Daftar Kegiatan</p>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($activityCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Mitra Industri -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Mitra Industri</p>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($partnerCount ?? 0) }}</p>
            </div>
        </div>

        <!-- Card: Galeri & Storage Media -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-3.5 sm:p-5 hover:border-slate-400 transition flex flex-col justify-between col-span-2 sm:col-span-2 lg:col-span-3 xl:col-span-2">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-snug">Perpustakaan Media</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Total Berkas & Storage Terpakai</p>
                </div>
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-cyan-50 text-cyan-600 border border-cyan-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-4 flex items-baseline justify-between gap-2">
                <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ number_format($mediaCount ?? 0) }} <span class="text-xs font-normal text-slate-400">Berkas</span></p>
                @php
                    $bytes = is_numeric($totalMediaSize ?? null) ? (float) $totalMediaSize : 0;
                    if ($bytes >= 1073741824) {
                        $formattedSize = number_format($bytes / 1073741824, 2) . ' GB';
                    } elseif ($bytes >= 1048576) {
                        $formattedSize = number_format($bytes / 1048576, 1) . ' MB';
                    } else {
                        $formattedSize = number_format($bytes / 1024, 0) . ' KB';
                    }
                @endphp
                <p class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                    {{ $formattedSize }}
                </p>
            </div>
        </div>

    </div>

    <!-- Section Tabel Aktivitas & Karya Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
        
        <!-- Tabel Kegiatan Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Kegiatan Terbaru</h3>
                @if(\Illuminate\Support\Facades\Route::has('admin.kegiatan.index'))
                    <a href="{{ route('admin.kegiatan.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
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
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 line-clamp-1">{{ $activity->title ?? 'Judul Kegiatan' }}</p>
                            <p class="text-[11px] text-slate-400">{{ $formattedDate }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $isPublished ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada data kegiatan.</div>
                @endforelse
            </div>
        </div>

        <!-- Tabel Karya Siswa Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Karya Siswa Terbaru</h3>
                @if(\Illuminate\Support\Facades\Route::has('admin.karya-siswa.index'))
                    <a href="{{ route('admin.karya-siswa.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
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
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 line-clamp-1">{{ $work->title ?? 'Judul Karya' }}</p>
                            <p class="text-[11px] text-slate-400">{{ $work->contributor_name ?? $work->student_name ?? 'Siswa / Kelas' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold shrink-0 {{ $isPublished ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada data karya siswa.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Widget Galeri Media Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900">Media & Berkas Terbaru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tinjauan cepat gambar yang baru diunggah ke dalam sistem.</p>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @if(\Illuminate\Support\Facades\Route::has('admin.media.create'))
                    <a href="{{ route('admin.media.create') }}" 
                       class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-1.5 rounded-xl text-xs sm:text-sm transition">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Upload</span>
                    </a>
                @endif

                <div class="h-4 w-px bg-slate-200"></div>

                @if(\Illuminate\Support\Facades\Route::has('admin.media.index'))
                    <a href="{{ route('admin.media.index') }}" 
                       class="inline-flex items-center gap-1 text-xs sm:text-sm font-semibold text-slate-600 hover:text-indigo-600 transition">
                        <span>Kelola Semua</span>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Grid Thumbnail Gambar Terbaru -->
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2.5 sm:gap-3">
            @forelse($recentMedia ?? [] as $media)
                @php
                    $mediaPath = $media->file_path ?? $media->path ?? null;
                    $mediaUrl = null;
                    if ($mediaPath) {
                        $mediaUrl = filter_var($mediaPath, FILTER_VALIDATE_URL)
                            ? $mediaPath
                            : \Illuminate\Support\Facades\Storage::disk($media->disk ?? 'public')->url(ltrim($mediaPath, '/'));
                    } else {
                        $mediaUrl = $media->url ?? '#';
                    }
                    $isPdf = strtolower(pathinfo($mediaPath ?? '', PATHINFO_EXTENSION)) === 'pdf';
                    $editMediaRoute = \Illuminate\Support\Facades\Route::has('admin.media.edit') ? route('admin.media.edit', $media) : '#';
                @endphp
                <a href="{{ $editMediaRoute }}" 
                   title="{{ $media->file_name ?? $media->original_name ?? 'Media' }}"
                   class="group relative aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50 hover:border-indigo-400 transition shadow-xs">
                    @if($isPdf)
                        <div class="w-full h-full flex flex-col items-center justify-center p-2 bg-slate-100 text-slate-500">
                            <svg class="w-8 h-8 text-rose-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-[9px] font-bold text-slate-600 truncate w-full text-center">PDF</span>
                        </div>
                    @else
                        <img src="{{ $mediaUrl }}" 
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400/e2e8f0/64748b?text=Media';"
                             alt="{{ $media->alt_text ?? 'Thumbnail' }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                    @endif
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-medium p-1 text-center truncate">
                        Edit
                    </div>
                </a>
            @empty
                <div class="col-span-3 sm:col-span-6 p-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                    Belum ada berkas media diunggah.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
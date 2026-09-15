@extends('layouts.admin.app')

@section('title', 'Kelola Konten Profil')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kelola Konten Profil</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui informasi utama program keahlian pada halaman profil & beranda.</p>
        </div>

        @if(optional($profile)->updated_at)
            <div class="inline-flex items-center gap-2 text-xs text-slate-600 bg-white border border-slate-300 px-3.5 py-2 rounded-xl shadow-xs self-start sm:self-auto shrink-0">
                <span>Diubah oleh <strong class="font-semibold text-slate-900">{{ $profile->updater?->name ?? $profile->user?->name ?? auth()->user()?->name ?? 'Sistem' }}</strong></span>
                <span class="text-slate-300">•</span>
                <span id="realtime-updated-at" 
                      data-timestamp="{{ $profile->updated_at?->toISOString() }}" 
                      title="{{ $profile->updated_at?->translatedFormat('d F Y, H:i:s') }}"
                      class="text-slate-500 font-medium">
                    {{ $profile->updated_at?->diffForHumans() }}
                </span>
            </div>
        @endif
    </div>

    <!-- Form Section -->
    <form method="POST" action="{{ route('admin.profil.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-6">
            
            <!-- Section Header -->
            <div class="border-b border-slate-200 pb-3.5 flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Deskripsi & Teks Utama Jurusan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola narasi sejarah, visi, misi, dan kutipan pengenalan jurusan PPLG.</p>
                </div>
            </div>

            {{-- Sejarah --}}
            <div>
                <label for="history_content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Sejarah Singkat Program Keahlian
                </label>
                <textarea name="history_content" id="history_content" rows="5"
                          class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('history_content') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror"
                          placeholder="Tuliskan sejarah perjalanan jurusan...">{{ old('history_content', $profile?->history_content) }}</textarea>
                @error('history_content')
                    <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Visi & Misi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="vision_content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Visi Jurusan
                    </label>
                    <textarea name="vision_content" id="vision_content" rows="6"
                              class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('vision_content') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror"
                              placeholder="Tuliskan visi jurusan...">{{ old('vision_content', $profile?->vision_content) }}</textarea>
                    @error('vision_content')
                        <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mission_content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Misi Jurusan
                    </label>
                    <textarea name="mission_content" id="mission_content" rows="6"
                              class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('mission_content') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror"
                              placeholder="Tuliskan poin-poin misi jurusan...">{{ old('mission_content', $profile?->mission_content) }}</textarea>
                    @error('mission_content')
                        <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ringkasan --}}
            <div>
                <label for="about_excerpt" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Ringkasan "Tentang PPLG" (Tampil di Beranda)
                </label>
                <textarea name="about_excerpt" id="about_excerpt" rows="3"
                          class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('about_excerpt') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror"
                          placeholder="Teks singkat penjelas di halaman beranda...">{{ old('about_excerpt', $profile?->about_excerpt) }}</textarea>
                @error('about_excerpt')
                    <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button CTA -->
        <div class="flex items-center justify-end">
            <button type="submit" 
                    class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeEl = document.getElementById('realtime-updated-at');
        if (!timeEl) return;

        const timestamp = timeEl.getAttribute('data-timestamp');
        if (!timestamp) return;

        function getRelativeTime(dateString) {
            const now = new Date();
            const past = new Date(dateString);

            if (isNaN(past.getTime())) return '';

            const diffInSeconds = Math.floor((now - past) / 1000);

            if (diffInSeconds < 10) return 'Baru saja';
            if (diffInSeconds < 60) return `${diffInSeconds} detik yang lalu`;

            const diffInMinutes = Math.floor(diffInSeconds / 60);
            if (diffInMinutes < 60) return `${diffInMinutes} menit yang lalu`;

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours} jam yang lalu`;

            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 30) return `${diffInDays} hari yang lalu`;

            const diffInMonths = Math.floor(diffInDays / 30);
            if (diffInMonths < 12) return `${diffInMonths} bulan yang lalu`;

            const diffInYears = Math.floor(diffInDays / 365);
            return `${diffInYears} tahun yang lalu`;
        }

        function updateTime() {
            const relTime = getRelativeTime(timestamp);
            if (relTime) timeEl.textContent = relTime;
        }

        updateTime();
        setInterval(updateTime, 10000);
    });
</script>
@endsection
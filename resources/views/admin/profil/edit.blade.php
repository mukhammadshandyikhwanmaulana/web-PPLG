@extends('layouts.admin.app')

@section('title', 'Kelola Konten Profil')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kelola Konten Profil</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui informasi utama program keahlian pada halaman beranda.</p>
        </div>

        <!-- Badge Realtime Editor & Waktu Pembaruan -->
        @if($profile->updated_at)
            <div class="inline-flex items-center gap-2 text-xs text-slate-600 bg-white border border-slate-300 px-3.5 py-2 rounded-xl shadow-xs self-start sm:self-auto shrink-0">
                <span>Diubah oleh <strong class="font-semibold text-slate-900">{{ $profile->updater?->name ?? auth()->user()?->name ?? 'Sistem' }}</strong></span>
                <span class="text-slate-300">•</span>
                <span id="realtime-updated-at" 
                      data-timestamp="{{ $profile->updated_at->toISOString() }}" 
                      title="{{ $profile->updated_at->translatedFormat('d F Y, H:i:s') }}"
                      class="text-slate-400">
                    {{ $profile->updated_at->diffForHumans() }}
                </span>
            </div>
        @endif
    </div>

    <!-- Form & Card Input Konten -->
    <form method="POST" action="{{ route('admin.profil.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-5">
            <div>
                <label for="history_content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Sejarah (RPL &rarr; PPLG)
                </label>
                <textarea name="history_content" id="history_content" rows="5"
                          class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 {{ $errors->has('history_content') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-300' }}"
                          placeholder="Tuliskan sejarah perjalanan jurusan...">{{ old('history_content', $profile->history_content) }}</textarea>
                @error('history_content')
                    <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Visi & Misi Grid 2 Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="vision_content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Visi
                    </label>
                    <textarea name="vision_content" id="vision_content" rows="5"
                              class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 {{ $errors->has('vision_content') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-300' }}"
                              placeholder="Tuliskan visi jurusan...">{{ old('vision_content', $profile->vision_content) }}</textarea>
                    @error('vision_content')
                        <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mission_content" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Misi
                    </label>
                    <textarea name="mission_content" id="mission_content" rows="5"
                              class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 {{ $errors->has('mission_content') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-300' }}"
                              placeholder="Tuliskan poin-poin misi jurusan...">{{ old('mission_content', $profile->mission_content) }}</textarea>
                    @error('mission_content')
                        <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="about_excerpt" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Ringkasan "Tentang PPLG" (Beranda)
                </label>
                <textarea name="about_excerpt" id="about_excerpt" rows="3"
                          class="w-full rounded-xl border text-sm p-3.5 resize-none transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 {{ $errors->has('about_excerpt') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-300' }}"
                          placeholder="Teks singkat penjelas di halaman beranda...">{{ old('about_excerpt', $profile->about_excerpt) }}</textarea>
                @error('about_excerpt')
                    <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Konten Profil</span>
            </button>
        </div>
    </form>
</div>

<!-- Script Penghitung Waktu Relatif Realtime -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeEl = document.getElementById('realtime-updated-at');
        if (!timeEl) return;

        const timestamp = timeEl.getAttribute('data-timestamp');
        if (!timestamp) return;

        function getRelativeTime(dateString) {
            const now = new Date();
            const past = new Date(dateString);
            const diffInSeconds = Math.floor((now - past) / 1000);

            if (diffInSeconds < 10) return 'Baru saja';
            if (diffInSeconds < 60) return `${diffInSeconds} detik yang lalu`;

            const diffInMinutes = Math.floor(diffInSeconds / 60);
            if (diffInMinutes < 60) return `${diffInMinutes} menit yang lalu`;

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours} jam ` + 'yang lalu';

            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 30) return `${diffInDays} hari yang lalu`;

            const diffInMonths = Math.floor(diffInDays / 30);
            if (diffInMonths < 12) return `${diffInMonths} bulan yang lalu`;

            const diffInYears = Math.floor(diffInDays / 365);
            return `${diffInYears} tahun yang lalu`;
        }

        function updateTime() {
            timeEl.textContent = getRelativeTime(timestamp);
        }

        updateTime();
        setInterval(updateTime, 10000);
    });
</script>
@endsection
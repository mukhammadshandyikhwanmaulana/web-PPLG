@extends('layouts.admin.app')

@section('title', 'Unit Usaha')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Unit Usaha</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola tautan eksternal Unit Usaha / Teaching Factory yang tampil pada navigasi publik.</p>
        </div>

        @if(isset($link) && optional($link)->updated_at)
            <div class="inline-flex items-center gap-2 text-xs text-slate-600 bg-white border border-slate-300 px-3.5 py-2 rounded-xl shadow-xs self-start sm:self-auto shrink-0">
                <span>Diubah oleh <strong class="font-semibold text-slate-900">{{ $link->updater?->name ?? auth()->user()?->name ?? 'Sistem' }}</strong></span>
                <span class="text-slate-300">•</span>
                <span id="realtime-updated-at" 
                      data-timestamp="{{ $link?->updated_at?->toISOString() }}" 
                      title="{{ $link?->updated_at?->translatedFormat('d F Y, H:i:s') }}"
                      class="text-slate-500 font-medium">
                    {{ $link?->updated_at?->diffForHumans() }}
                </span>
            </div>
        @endif
    </div>

    <!-- Form Section -->
    <form method="POST" 
          action="{{ route('admin.unit-usaha.update') }}" 
          x-data="{
              isActive: {{ old('is_active', $link->is_active ?? false) ? 'true' : 'false' }},
              navLabel: @js(old('label', $link->label ?? '')),
              externalUrl: @js(old('external_url', $link->external_url ?? '')),
              get hasUrl() { return (this.externalUrl || '').trim().length > 0 },
              get isDisplayable() { return this.isActive && this.hasUrl },
              get formattedUrl() {
                  let url = (this.externalUrl || '').trim();
                  if (!url) return '#';
                  return (/^https?:\/\//i.test(url)) ? url : 'https://' + url;
              }
          }"
          class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Preview Card -->
        <div class="bg-slate-50 border border-slate-300 rounded-2xl p-4 sm:p-5 shadow-xs transition-all space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Preview Navigasi Publik</span>
                
                <!-- Indikator Status -->
                <div class="flex items-center gap-1.5 shrink-0 select-none">
                    <span :class="isDisplayable ? 'bg-emerald-500' : 'bg-amber-500'" 
                          class="w-2 h-2 rounded-full inline-block"></span>
                    <span :class="isDisplayable ? 'text-emerald-700 font-bold' : 'text-amber-700 font-semibold'"
                          class="text-xs" 
                          x-text="isDisplayable ? 'Tampil' : 'Belum Tampil'"></span>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white p-3.5 rounded-xl border border-slate-300 shadow-2xs">
                <svg class="w-5 h-5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate text-slate-900" x-text="(navLabel || '').trim() !== '' ? navLabel : 'Unit Usaha'"></p>
                    <p class="text-xs text-slate-500 truncate mt-0.5" x-text="hasUrl ? externalUrl : 'Belum ada URL eksternal yang diatur'"></p>
                </div>
                <template x-if="hasUrl">
                    <a :href="formattedUrl" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 rounded-lg transition shrink-0">
                        Uji Link ↗
                    </a>
                </template>
            </div>
        </div>

        <!-- Form Input Card -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-5">
            
            <div class="border-b border-slate-200 pb-3.5 flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 005.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Konfigurasi Tautan Eksternal</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Atur nama menu dan URL tujuan dari Unit Usaha / TeFa.</p>
                </div>
            </div>

            {{-- Label Navigasi --}}
            <div>
                <label for="label_input" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    Label Navigasi <span x-show="isActive" class="text-rose-500 ml-0.5">*</span>
                </label>
                <input type="text" name="label" id="label_input"
                       x-model="navLabel"
                       :required="isActive"
                       maxlength="255"
                       placeholder="Contoh: Unit Usaha / Teaching Factory"
                       class="w-full rounded-xl border text-sm px-3.5 py-2.5 transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('label') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror">
                @error('label')
                    <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL Eksternal --}}
            <div>
                <label for="external_url_input" class="block text-sm font-semibold text-slate-900 mb-1.5">
                    URL Eksternal <span x-show="isActive" class="text-rose-500 ml-0.5">*</span>
                </label>
                <input type="url" name="external_url" id="external_url_input"
                       x-model="externalUrl"
                       :required="isActive"
                       maxlength="255"
                       placeholder="https://unitusaha.smk.sch.id"
                       class="w-full rounded-xl border text-sm px-3.5 py-2.5 transition focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('external_url') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror">
                <p class="text-xs text-slate-400 mt-1.5">Wajib diawali <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-600 font-mono">http://</code> atau <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-600 font-mono">https://</code></p>
                @error('external_url')
                    <p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Toggle Aktif --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                <div class="cursor-pointer select-none flex-1" @click="isActive = !isActive">
                    <span class="block text-sm font-semibold text-slate-900">
                        Aktifkan di Navigasi Publik
                    </span>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Tautan hanya tampil pada navbar publik jika opsi ini aktif dan URL eksternal terisi.
                    </p>
                </div>

                <button type="button"
                        @click="isActive = !isActive"
                        :class="isActive ? 'bg-indigo-600' : 'bg-slate-300'"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    <span :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                </button>
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
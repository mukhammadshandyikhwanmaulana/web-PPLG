@extends('layouts.public')

@section('title', $activity->title . ' - PPLG SMKN 1 Bangsri')

@section('content')

    @php
        $mainCoverMedia = $activity->cover;
        $mainCoverPath = $mainCoverMedia?->path ?? $mainCoverMedia?->file_path;
        $mainCoverDisk = $mainCoverMedia?->disk ?? 'public';
        $mainCoverUrl = $activity->cover_url 
            ?? ($mainCoverPath ? \Illuminate\Support\Facades\Storage::disk($mainCoverDisk)->url(ltrim($mainCoverPath, '/')) : null);

        $formattedDate = $activity->event_date 
            ? \Carbon\Carbon::parse($activity->event_date)->translatedFormat('d F Y') 
            : \Carbon\Carbon::parse($activity->created_at)->translatedFormat('d F Y');
            
        $galleryPhotos = $activity->galleries ? $activity->galleries->map(function($g) {
            $m = $g->media ?? $g;
            return $m->url ?? ($m->path ? \Illuminate\Support\Facades\Storage::disk($m->disk ?? 'public')->url(ltrim($m->path, '/')) : null);
        })->filter()->values() : collect([]);
    @endphp

    <div x-data="{
            previewOpen: false,
            currentIndex: 0,
            previewPhotos: {{ json_encode($galleryPhotos->all()) }},
            openPreview(index) {
                this.currentIndex = index;
                this.previewOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closePreview() {
                this.previewOpen = false;
                document.body.classList.remove('overflow-hidden');
            },
            nextPhoto() {
                this.currentIndex = (this.currentIndex + 1) % this.previewPhotos.length;
            },
            prevPhoto() {
                this.currentIndex = (this.currentIndex - 1 + this.previewPhotos.length) % this.previewPhotos.length;
            }
         }"
         @keydown.escape.window="closePreview()">

        <!-- HERO HEADER -->
        <section class="bg-orange-500 text-white font-sans pt-32 sm:pt-40 pb-20 sm:pb-28 flex items-center justify-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight">
                    Detail Kegiatan
                </h1>
            </div>
        </section>

        <!-- KONTEN UTAMA -->
        <article class="py-12 sm:py-16 bg-slate-50 font-sans border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- KOLOM KIRI (KETERANGAN & GAMBAR UTAMA) -->
                    <div class="lg:col-span-8 space-y-8">

                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight leading-snug">
                                {{ $activity->title }}
                            </h1>
                            <div class="flex items-center gap-4 text-xs font-semibold text-slate-500 pt-4 border-t border-slate-100">
                                <span class="text-orange-600 font-bold">📅 {{ $formattedDate }}</span>
                                <span>•</span>
                                <span>Oleh: {{ $activity->creator?->name ?? 'Admin PPLG' }}</span>
                            </div>
                        </div>

                        @if($mainCoverUrl)
                            <div class="bg-white p-3 rounded-3xl border border-slate-200/80 shadow-xs">
                                <div class="relative rounded-2xl overflow-hidden bg-slate-950 aspect-[16/10] sm:aspect-[16/9] group cursor-pointer"
                                     @if(count($galleryPhotos) > 0) @click="openPreview(0)" @endif>
                                    <img src="{{ $mainCoverUrl }}" alt="{{ $activity->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </div>
                            </div>
                        @endif

                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Rincian & Narasi Kegiatan</h3>
                            <div class="text-slate-700 leading-relaxed text-sm sm:text-base space-y-4">
                                {!! nl2br(e($activity->content)) !!}
                            </div>
                        </div>

                        @if(count($galleryPhotos) > 0)
                            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Dokumentasi Galeri</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                                    @foreach($galleryPhotos as $index => $photoUrl)
                                        <div @click="openPreview({{ $index }})" class="aspect-square bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 cursor-pointer relative group">
                                            <img src="{{ $photoUrl }}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <a href="{{ route('public.activities.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-semibold rounded-2xl border border-slate-200 transition shadow-xs">
                                ← Kembali ke Daftar Kegiatan
                            </a>
                        </div>
                    </div>

                    <!-- KOLOM KANAN (SIDEBAR STICKY: THUMBNAIL + JUDUL) -->
                    <div class="lg:col-span-4 sticky top-28 space-y-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                                <span>Kegiatan Lainnya</span>
                                <a href="{{ route('public.activities.index') }}" class="text-xs text-orange-600 hover:text-orange-700 font-semibold">Semua →</a>
                            </h3>

                            @if(isset($recentActivities) && $recentActivities->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($recentActivities as $other)
                                        @php
                                            $cMedia = $other->cover;
                                            $cPath = $cMedia?->path ?? $cMedia?->file_path;
                                            $cDisk = $cMedia?->disk ?? 'public';

                                            $oCover = $other->cover_url 
                                                ?? ($cPath ? \Illuminate\Support\Facades\Storage::disk($cDisk)->url(ltrim($cPath, '/')) : null)
                                                ?? ($other->galleries?->first()?->media?->url 
                                                    ?? ($other->galleries?->first()?->media?->path ? \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim($other->galleries->first()->media->path, '/')) : null));
                                        @endphp
                                        <a href="{{ route('public.activities.show', $other->slug ?? $other->id) }}" 
                                           class="group flex gap-3 items-center p-2 rounded-2xl hover:bg-orange-50/60 transition border border-transparent hover:border-orange-100">
                                            <!-- THUMBNAIL -->
                                            <div class="w-14 h-14 bg-slate-100 rounded-xl overflow-hidden shrink-0 border border-slate-200">
                                                @if($oCover)
                                                    <img src="{{ $oCover }}" alt="{{ $other->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[9px]">No Image</div>
                                                @endif
                                            </div>
                                            <!-- JUDUL -->
                                            <div class="flex-1 min-w-0">
                                                <span class="text-[9px] text-orange-600 font-medium block mb-0.5">
                                                    {{ $other->event_date ? \Carbon\Carbon::parse($other->event_date)->translatedFormat('d M Y') : '' }}
                                                </span>
                                                <h4 class="font-semibold text-slate-900 text-xs line-clamp-2 group-hover:text-orange-600 transition">
                                                    {{ $other->title }}
                                                </h4>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Belum ada kegiatan lainnya.</p>
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
                    <span class="text-xs text-slate-400 font-mono" x-text="`Dokumentasi Foto (${currentIndex + 1}/${previewPhotos.length})`"></span>
                    <button type="button" @click="closePreview()" class="text-slate-400 hover:text-white transition p-1.5 rounded-xl hover:bg-slate-800">&times;</button>
                </div>
                <div class="relative flex-1 flex items-center justify-center p-4 min-h-[350px] bg-slate-950">
                    <template x-if="previewPhotos.length > 0">
                        <img :src="previewPhotos[currentIndex]" class="max-h-[65vh] max-w-full object-contain rounded-xl shadow-md">
                    </template>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.admin.app')

@section('title', 'Edit Media')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.media.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Galeri Media
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                {{ isset($albumMedia) && $albumMedia->count() > 1 ? 'Edit Album Media / Karya' : 'Edit Deskripsi Media' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Perbarui deskripsi karya dan kelola berkas media yang diunggah.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-6">

        <form method="POST" action="{{ route('admin.media.update', ['medium' => $media]) }}" enctype="multipart/form-data" class="space-y-6"
              x-data="{
                  onReplaceFileSelected(event) {
                      const file = event.target.files[0];
                      if (!file) return;

                      if (file.size > 5 * 1024 * 1024) {
                          alert('Ukuran berkas pengganti terlalu besar! Maksimal 5 MB.');
                          event.target.value = '';
                          return;
                      }

                      const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

                      if (!isPdf && file.type.startsWith('image/')) {
                          // Potong gambar jika pengganti berupa gambar
                          $dispatch('open-cropper', {
                              title: 'Potong / Sesuaikan Media Gambar Pengganti',
                              aspectRatio: NaN,
                              file: file,
                              targetInput: $refs.replaceFileInput,
                              targetPreview: $refs.replaceFilePreviewImg
                          });
                      }
                  }
              }">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-900">
                    Daftar Berkas Media
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $itemsToRender = isset($albumMedia) && $albumMedia->isNotEmpty() ? $albumMedia : collect([$media]);
                    @endphp

                    @foreach($itemsToRender as $item)
                        @php
                            $isPdf = \Illuminate\Support\Str::contains($item->mime_type ?? '', 'pdf') || \Illuminate\Support\Str::endsWith(strtolower($item->original_name ?? ''), '.pdf');
                            $itemDisk = $item->disk ?? 'public';
                            $itemUrl = $item->file_url ?? ($item->path ? \Illuminate\Support\Facades\Storage::disk($itemDisk)->url($item->path) : null);
                        @endphp

                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-20 h-20 bg-white border border-slate-200 rounded-lg overflow-hidden flex items-center justify-center shrink-0">
                                    @if($isPdf)
                                        <div class="text-rose-600 font-bold text-center">
                                            <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                                            </svg>
                                            <span class="text-[9px] font-black uppercase tracking-wider">PDF</span>
                                        </div>
                                    @else
                                        <img @if($item->id === $media->id) x-ref="replaceFilePreviewImg" @endif 
                                             src="{{ $itemUrl ?? 'https://placehold.co/600x400/e2e8f0/64748b?text=File' }}" alt="{{ $item->original_name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>

                                <div class="space-y-1 text-xs text-slate-600 min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        @if($item->id === $media->id)
                                            <span class="bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shrink-0">Fokus Utama</span>
                                        @endif
                                        <p class="font-semibold text-slate-900 text-xs truncate" title="{{ $item->original_name }}">{{ $item->original_name }}</p>
                                    </div>
                                    <p><span class="text-slate-400">Tipe Berkas:</span> <span class="font-mono text-[11px]">{{ $item->mime_type ?? '—' }}</span></p>
                                    <p><span class="text-slate-400">Ukuran File:</span> <span class="font-mono text-[11px]">{{ ($item->size ?? 0) >= 1048576 ? number_format(($item->size ?? 0) / 1048576, 1) . ' MB' : number_format(($item->size ?? 0) / 1024, 0) . ' KB' }}</span></p>
                                </div>
                            </div>

                            @if($item->id === $media->id)
                                <div class="pt-2 border-t border-slate-200/80">
                                    <label for="file" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Ganti Berkas Ini <span class="text-[10px] font-normal text-slate-400">(Kosongkan jika tidak ingin mengganti file)</span>
                                    </label>
                                    <input type="file"
                                           name="file"
                                           id="file"
                                           x-ref="replaceFileInput"
                                           accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,application/pdf"
                                           @change="onReplaceFileSelected($event)"
                                           class="w-full text-xs text-slate-500 p-1 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-lg cursor-pointer bg-white shadow-xs focus:outline-none">
                                    @error('file')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @include('admin.media._form', ['media' => $media])

            <div class="pt-4 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.media.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
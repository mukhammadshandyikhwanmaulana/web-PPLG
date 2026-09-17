@extends('layouts.admin.app')

@section('title', 'Manajemen Gambar Hero')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4"
     x-data="{
         previewOpen: false,
         previewPhoto: '',
         openPreview(photo) {
             if(!photo) return;
             this.previewPhoto = photo;
             this.previewOpen = true;
         }
     }"
     @keydown.escape.window="previewOpen = false">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Gambar Hero Slider</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola koleksi gambar latar belakang untuk slider beranda.</p>
        </div>
        <div>
            <a href="{{ route('admin.banner.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Gambar
            </a>
        </div>
    </div>

    <!-- TAMPILAN HP / MOBILE -->
    <div class="block md:hidden space-y-3">
        @forelse ($banners as $item)
            @php
                $imageUrl = $item->image_path ? Storage::disk('public')->url(ltrim($item->image_path, '/')) : $item->image_url;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-xs flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="relative group cursor-pointer shrink-0 rounded-xl overflow-hidden border border-slate-200"
                         @click="openPreview('{{ $imageUrl }}')">
                        <img src="{{ $imageUrl }}" alt="Hero Image" class="w-24 h-16 object-cover bg-slate-50">
                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-700">Urutan #{{ $item->order }}</span>
                            @if($item->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">Sembunyi</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Diunggah: {{ $item->created_at?->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.banner.edit', $item) }}"
                       class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                        Edit
                    </a>
                    <form action="{{ route('admin.banner.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar hero ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-slate-500 text-sm shadow-xs">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Belum ada gambar hero yang diunggah.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN LAPTOP / DESKTOP -->
    <div class="hidden md:block bg-white rounded-2xl shadow-xs border border-slate-300 overflow-hidden">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-3.5 px-4 w-36">Preview Gambar</th>
                    <th class="py-3.5 px-4">Informasi Berkas</th>
                    <th class="py-3.5 px-4 w-32 text-center">Status Tampil</th>
                    <th class="py-3.5 px-4 w-28 text-center">Urutan Slider</th>
                    <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($banners as $item)
                    @php
                        $imageUrl = $item->image_path ? Storage::disk('public')->url(ltrim($item->image_path, '/')) : $item->image_url;
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 px-4">
                            <div class="relative group cursor-pointer w-28 h-16 rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-50"
                                 @click="openPreview('{{ $imageUrl }}')">
                                <img src="{{ $imageUrl }}" alt="Hero Image" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            Gambar Slider #{{ $item->order }}
                            <div class="text-xs text-slate-400 font-normal mt-0.5">Tanggal Unggah: {{ $item->created_at?->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($item->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif Tampil
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Disembunyikan
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 font-mono">
                                #{{ $item->order }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.banner.edit', $item) }}"
                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition leading-none">
                                    Edit
                                </a>
                                <form action="{{ route('admin.banner.destroy', $item) }}" method="POST" class="inline-flex items-center m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar hero ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer leading-none p-0 border-0 bg-transparent">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 px-4 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Belum ada gambar hero yang diunggah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($banners, 'hasPages') && $banners->hasPages())
        <div class="px-4 py-3 bg-white border border-slate-300 rounded-2xl shadow-xs">
            {{ $banners->links() }}
        </div>
    @endif

    <!-- MODAL PREVIEW FOTO (LIGHTBOX) -->
    <div x-show="previewOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs"
         x-cloak>
        
        <div class="relative max-w-3xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col"
             @click.away="previewOpen = false">
            
            <div class="flex items-center justify-between p-4 border-b border-slate-800 text-white">
                <span class="text-sm font-semibold">Preview Gambar Hero</span>
                <button type="button" @click="previewOpen = false" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex items-center justify-center p-4 bg-slate-950">
                <img :src="previewPhoto" class="max-h-[70vh] max-w-full object-contain rounded-lg">
            </div>
        </div>
    </div>
</div>
@endsection
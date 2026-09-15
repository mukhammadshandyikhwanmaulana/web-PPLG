@extends('layouts.admin.app')

@section('title', 'Edit Karya Siswa')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.karya-siswa.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Karya Siswa
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Karya Siswa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui informasi karya, foto sampul, dan kelola foto galeri pendukung.</p>
        </div>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.karya-siswa.update', $studentWork) }}" enctype="multipart/form-data" class="space-y-5"
              x-data="{
                  removedGalleries: [],
                  toggleRemoveGallery(id) {
                      if (this.removedGalleries.includes(id)) {
                          this.removedGalleries = this.removedGalleries.filter(item => item !== id);
                      } else {
                          this.removedGalleries.push(id);
                      }
                  }
              }">
            @csrf
            @method('PUT')

            @include('admin.karya-siswa._form', ['studentWork' => $studentWork, 'supervisors' => $supervisors ?? []])

            <!-- Submit Button: CTA Standardized -->
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.karya-siswa.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">
                    Batal
                </a>
                <button type="submit" 
                        :class="removedGalleries.length > 0 ? 'bg-rose-600 hover:bg-rose-700 focus:ring-2 focus:ring-rose-500/30' : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/30'"
                        class="inline-flex items-center justify-center gap-1.5 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    
                    <template x-if="removedGalleries.length > 0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </template>

                    <template x-if="removedGalleries.length === 0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>

                    <span x-text="removedGalleries.length > 0 ? `Hapus (${removedGalleries.length}) & Simpan` : 'Simpan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.admin.app')

@section('title', 'Upload Media')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">
    
    <!-- Header Section -->
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
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Upload Media Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Unggah berkas gambar atau dokumen (PDF) baru ke dalam perpustakaan media sistem.</p>
        </div>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="space-y-5"
              x-data="{
                  fileName: '',
                  isPdf: false,
                  onFileSelected(event) {
                      const file = event.target.files[0];
                      if (!file) {
                          this.resetFile();
                          return;
                      }

                      if (file.size > 5 * 1024 * 1024) {
                          alert('Ukuran berkas terlalu besar! Maksimal ukuran adalah 5 MB.');
                          event.target.value = '';
                          this.resetFile();
                          return;
                      }

                      this.fileName = file.name;
                      this.isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

                      if (!this.isPdf && file.type.startsWith('image/')) {
                          // Panggil Cropper Modal Global (Rasio Bebas/Free-crop)
                          $dispatch('open-cropper', {
                              title: 'Potong / Sesuaikan Media Gambar',
                              aspectRatio: NaN,
                              file: file,
                              targetInput: $refs.fileInput,
                              targetPreview: $refs.filePreviewImg,
                              onCropComplete: () => {
                                  if ($refs.filePreviewBox) $refs.filePreviewBox.classList.remove('hidden');
                                  if ($refs.pdfPreviewBox) $refs.pdfPreviewBox.classList.add('hidden');
                              }
                          });
                      } else if (this.isPdf) {
                          if ($refs.filePreviewBox) $refs.filePreviewBox.classList.add('hidden');
                          if ($refs.pdfPreviewBox) $refs.pdfPreviewBox.classList.remove('hidden');
                      }
                  },
                  resetFile() {
                      if ($refs.fileInput) $refs.fileInput.value = '';
                      if ($refs.filePreviewImg) $refs.filePreviewImg.src = '#';
                      this.fileName = '';
                      this.isPdf = false;
                      if ($refs.filePreviewBox) $refs.filePreviewBox.classList.add('hidden');
                      if ($refs.pdfPreviewBox) $refs.pdfPreviewBox.classList.add('hidden');
                  }
              }">
            @csrf

            <div>
                <label for="file" class="block text-sm font-semibold text-slate-900 mb-1">
                    Pilih File Media <span class="text-rose-500 ml-1">*</span>
                </label>
                <p class="text-xs text-slate-400 mb-3">Format: JPG, JPEG, PNG, WEBP, GIF, SVG, PDF (Maksimal 5MB)</p>

                <!-- Pratinjau Gambar & PDF -->
                <div class="mb-3 flex items-center gap-4">
                    <!-- Preview Jika Gambar Terpilih -->
                    <div x-ref="filePreviewBox" class="relative w-32 h-32 bg-slate-50 rounded-xl border border-indigo-200 shadow-xs p-1 hidden overflow-hidden">
                        <img x-ref="filePreviewImg" src="#" class="w-full h-full object-contain">
                    </div>

                    <!-- Preview Jika File PDF -->
                    <div x-ref="pdfPreviewBox" class="w-24 h-24 bg-rose-50 border border-rose-200 text-rose-600 rounded-xl flex flex-col items-center justify-center font-bold shadow-xs hidden">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-wider mt-1">PDF</span>
                    </div>
                </div>

                <input type="file"
                       name="file"
                       id="file"
                       x-ref="fileInput"
                       required
                       accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,application/pdf"
                       @change="onFileSelected($event)"
                       class="w-full text-sm text-slate-500 p-1 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl cursor-pointer bg-slate-50/50 shadow-xs focus:outline-none flex items-center @error('file') border-rose-300 bg-rose-50/30 @enderror">
                @error('file')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            @include('admin.media._form', ['media' => null])

            <!-- Submit Button Bar -->
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.media.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Upload</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
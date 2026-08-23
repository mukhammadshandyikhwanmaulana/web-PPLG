@extends('layouts.admin.app')
@section('title', 'Edit Fasilitas')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-4">

    {{-- Link Kembali --}}
    <a href="{{ route('admin.fasilitas.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 text-sm font-medium transition-colors mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Daftar Fasilitas</span>
    </a>

    {{-- Judul Halaman --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Fasilitas</h1>
        <p class="text-sm text-slate-500 mt-1">Isi formulir di bawah ini untuk memperbarui data fasilitas.</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-5 sm:p-8">
        <form method="POST" action="{{ route('admin.fasilitas.update', $facility) }}" enctype="multipart/form-data" class="space-y-5 sm:space-y-6">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nama Fasilitas <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $facility->name) }}" required class="w-full rounded-xl border border-slate-400 bg-white text-slate-900 shadow-2xs focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 text-base sm:text-sm px-3.5 py-2.5">
                @error('name') <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-xl border border-slate-400 bg-white text-slate-900 shadow-2xs focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 text-base sm:text-sm px-3.5 py-2.5 resize-none">{{ old('description', $facility->description) }}</textarea>
                @error('description') <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Foto Fasilitas --}}
            <div>
                <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-2">Foto Fasilitas</label>
                
                @if ($facility->photo)
                    <div class="mb-3 sm:mb-4 p-3 sm:p-3.5 bg-slate-50 rounded-xl border border-slate-300 flex items-center gap-3 sm:gap-4">
                        <img src="{{ Storage::url($facility->photo->file_path) }}" alt="{{ $facility->name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover border border-slate-300 shrink-0">
                        <div class="min-w-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-xs font-semibold bg-emerald-100 text-emerald-800 mb-1">Foto Aktif</span>
                            <p class="text-xs text-slate-500">Pilih berkas di bawah jika ingin mengganti foto ini.</p>
                        </div>
                    </div>
                @endif

                <div class="border-2 border-dashed border-slate-400 hover:border-slate-600 bg-slate-50/50 hover:bg-slate-50 rounded-2xl p-4 sm:p-6 transition-all text-center relative">
                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewImage(event)">

                    <div id="upload-placeholder" class="space-y-2 cursor-pointer" onclick="document.getElementById('photo').click()">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center mx-auto text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-slate-700"><span class="text-blue-600 font-semibold underline hover:text-blue-700">Klik di sini</span> untuk memilih foto pengganti</p>
                        <p class="text-[11px] sm:text-xs text-slate-500">PNG, JPG, WEBP maks. 2MB</p>
                    </div>

                    <div id="image-preview-container" class="hidden space-y-3">
                        <p class="text-xs font-semibold text-emerald-600">Preview Foto Baru:</p>
                        <img id="image-preview" src="#" alt="Preview Baru" class="max-h-40 sm:max-h-52 rounded-xl mx-auto shadow-sm border border-slate-300 object-cover">
                        <button type="button" onclick="resetImagePreview()" class="text-xs font-medium text-rose-600 hover:underline">Batal Ganti Foto</button>
                    </div>
                </div>
                @error('photo') <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Urutan Tampil --}}
            <div>
                <label for="sort_order" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Urutan Tampil</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $facility->sort_order) }}" min="0" class="w-full sm:w-36 rounded-xl border border-slate-400 bg-white text-slate-900 shadow-2xs focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 text-base sm:text-sm px-3.5 py-2.5">
                @error('sort_order') <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Buttons (Tombol Perbarui Biru) --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 sm:gap-3 pt-4">
                <a href="{{ route('admin.fasilitas.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 border border-slate-400 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors focus:outline-none">Batal</a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-sm font-semibold shadow-xs transition-colors focus:outline-none">Perbarui Fasilitas</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('image-preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetImagePreview() {
        document.getElementById('photo').value = '';
        document.getElementById('upload-placeholder').classList.remove('hidden');
        document.getElementById('image-preview-container').classList.add('hidden');
    }
</script>
@endsection
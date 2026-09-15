@extends('layouts.admin.app')

@section('title', 'Tambah Akun Guru')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.guru.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Guru
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Tambah Akun Guru Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Isi formulir di bawah ini untuk menambahkan akun dan profil guru.</p>
        </div>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6"
         x-data="{
            hasImage: false,
            onFileSelected(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ukuran berkas terlalu besar! Maksimal 10 MB.');
                        event.target.value = '';
                        return;
                    }

                    // Panggil Event Cropper Modal Global (Rasio 1:1 untuk Foto Profil Guru)
                    $dispatch('open-cropper', {
                        title: 'Potong Foto Profil Guru (1:1)',
                        aspectRatio: 1,
                        file: file,
                        targetInput: $refs.photoInput,
                        targetPreview: $refs.photoPreviewImg
                    });

                    this.hasImage = true;
                }
            },
            resetImage() {
                if ($refs.photoInput) $refs.photoInput.value = '';
                if ($refs.photoPreviewImg) $refs.photoPreviewImg.src = '#';
                this.hasImage = false;
            }
         }">
        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Subheader Akun -->
            <div class="pb-2 border-b border-slate-200">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kredensial Akun</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div>
                    <label for="name" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">Nama Lengkap <span class="text-rose-500 ml-1">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap guru..." class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('name') border-rose-300 bg-rose-50/30 @enderror">
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">Alamat Email <span class="text-rose-500 ml-1">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@sekolah.sch.id" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('email') border-rose-300 bg-rose-50/30 @enderror">
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Password dengan Toggle Lihat Password --}}
                <div x-data="{ showPassword: false }">
                    <label for="password" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">Password <span class="text-rose-500 ml-1">*</span></label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               required 
                               placeholder="Kombinasi huruf & angka (min. 8)..." 
                               class="w-full text-sm border border-slate-300 rounded-xl pl-3.5 pr-10 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('password') border-rose-300 bg-rose-50/30 @enderror">
                        
                        <button type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
                                tabindex="-1">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 013.25-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Minimal 8 karakter (kombinasi huruf dan angka).</p>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div x-data="{ showConfirmPassword: false }">
                    <label for="password_confirmation" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">Konfirmasi Password <span class="text-rose-500 ml-1">*</span></label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               placeholder="Ulangi password..." 
                               class="w-full text-sm border border-slate-300 rounded-xl pl-3.5 pr-10 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                        
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
                                tabindex="-1">
                            <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 013.25-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Akun Is Active --}}
            <div class="flex items-center gap-2 pt-1 select-none">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Akun dapat login ke sistem</label>
            </div>

            <!-- Subheader Profil Publik -->
            <div class="pt-4 pb-2 border-b border-slate-200">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Profil Publik</h2>
            </div>

            {{-- Positions Checkboxes & Expertise --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="h-6 flex items-center gap-1.5 text-sm font-semibold text-slate-900 mb-1.5">
                        <span>Jabatan</span>
                        <span class="text-xs font-normal text-slate-400">(Bisa pilih lebih dari satu)</span>
                    </label>
                    <div class="flex flex-wrap gap-3 p-3 bg-slate-50/70 border border-slate-300 rounded-xl">
                        <label class="inline-flex items-center gap-2 {{ ($isKajurExist ?? false) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }} select-none">
                            <input type="checkbox" name="positions[]" value="Ketua Kompetensi Keahlian PPLG" 
                                   {{ (in_array('Ketua Kompetensi Keahlian PPLG', old('positions', [])) || in_array('Kepala Kompetensi Keahlian PPLG', old('positions', []))) ? 'checked' : '' }}
                                   {{ ($isKajurExist ?? false) ? 'disabled' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 {{ ($isKajurExist ?? false) ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            <span class="text-sm font-medium text-slate-700">
                                Ketua Kompetensi Keahlian
                                @if($isKajurExist ?? false)
                                    <span class="text-xs text-rose-500 font-normal ml-1">(Sudah Terisi)</span>
                                @endif
                            </span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="positions[]" value="Guru Produktif PPLG" 
                                   {{ in_array('Guru Produktif PPLG', old('positions', ['Guru Produktif PPLG'])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="text-sm font-medium text-slate-700">Guru Produktif</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="positions[]" value="Staff PPLG" 
                                   {{ in_array('Staff PPLG', old('positions', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                            <span class="text-sm font-medium text-slate-700">Staff / Laboran</span>
                        </label>
                    </div>
                    @error('positions')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Expertise --}}
                <div>
                    <label for="expertise" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">Keahlian / Bidang</label>
                    <input type="text" name="expertise" id="expertise" value="{{ old('expertise') }}" placeholder="Contoh: Pemrograman Web & Laravel..." class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('expertise') border-rose-300 bg-rose-50/30 @enderror">
                    @error('expertise')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Photo --}}
            <div>
                <label for="photo" class="h-6 flex items-baseline gap-1.5 text-sm font-semibold text-slate-900 mb-1.5">
                    <span>Foto Profil</span>
                    <span class="text-xs text-slate-400 font-normal">(JPG, JPEG, PNG, WebP — Potongan 1:1)</span>
                </label>

                <div x-show="hasImage" x-cloak class="mb-3 flex items-center gap-3">
                    <img x-ref="photoPreviewImg" src="#" alt="Preview Foto" class="w-20 h-20 object-cover rounded-xl border border-slate-300 shadow-xs">
                    <button type="button" @click="resetImage()" class="text-xs font-medium text-rose-600 hover:underline cursor-pointer">Hapus / Batal Pilih</button>
                </div>

                <input type="file" name="photo" id="photo" x-ref="photoInput" accept="image/jpeg,image/png,image/webp,image/jpg" @change="onFileSelected($event)" class="w-full text-sm text-slate-500 p-1 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl cursor-pointer bg-slate-50/50 shadow-xs focus:outline-none flex items-center @error('photo') border-rose-300 bg-rose-50/30 @enderror">
                @error('photo')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Staff Is Active --}}
            <div class="flex items-center gap-2 pt-1 select-none">
                <input type="checkbox" name="staff_is_active" id="staff_is_active" value="1" {{ old('staff_is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                <label for="staff_is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Tampilkan profil di halaman website publik</label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.guru.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
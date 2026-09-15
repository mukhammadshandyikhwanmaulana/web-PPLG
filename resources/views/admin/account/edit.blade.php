@extends('layouts.admin.app')

@section('title', 'Pengaturan Akun Saya')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="mb-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Pengaturan Akun Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola informasi data diri, foto profil, dan kata sandi akun admin Anda.</p>
        </div>
    </div>

    @php
        // Penanganan URL Avatar secara dinamis & safe-null
        $initialAvatarUrl = $user->avatar_url ?? null;
        if (!$initialAvatarUrl) {
            $avatarPath = $user->avatar ?? $user->photo ?? $user->staffMember?->photoMedia?->file_path ?? $user->staffMember?->photo?->path ?? null;
            if ($avatarPath) {
                $initialAvatarUrl = filter_var($avatarPath, FILTER_VALIDATE_URL) 
                    ? $avatarPath 
                    : \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim($avatarPath, '/'));
            }
        }
    @endphp

    <!-- Form Card Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6">
        <form action="{{ route('admin.account.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bagian 1: Foto Profil & Informasi Dasar -->
            <div class="space-y-6">
                <div class="pb-2 border-b border-slate-200">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Akun</h2>
                </div>

                <!-- Section Upload Foto Profil dengan Cropper JS -->
                <div x-data="{ 
                    photoPreview: @js($initialAvatarUrl),
                    removePhoto: false,
                    onFileSelected(event) {
                        const file = event.target.files[0];
                        if (file) {
                            if (file.size > 10 * 1024 * 1024) {
                                alert('Ukuran berkas awal terlalu besar! Maksimal 10 MB.');
                                event.target.value = '';
                                return;
                            }

                            // Panggil Event Cropper Modal Global (Rasio 1:1)
                            $dispatch('open-cropper', {
                                title: 'Potong Foto Profil Admin',
                                aspectRatio: 1,
                                file: file,
                                targetInput: $refs.avatarInput,
                                targetPreview: $refs.avatarImgPreview
                            });
                        }
                    },
                    clearPhoto() {
                        if (this.photoPreview && this.photoPreview.startsWith('blob:')) {
                            URL.revokeObjectURL(this.photoPreview);
                        }
                        this.photoPreview = null;
                        this.removePhoto = true;
                        if ($refs.avatarInput) $refs.avatarInput.value = '';
                    }
                }" class="flex flex-col sm:flex-row items-start sm:items-center gap-5 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    
                    <input type="hidden" name="remove_avatar" :value="removePhoto ? 1 : 0">

                    <!-- Lingkaran Preview Foto -->
                    <div class="relative shrink-0">
                        <img x-ref="avatarImgPreview"
                             :src="photoPreview || 'https://placehold.co/100x100/4f46e5/white?text={{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}'" 
                             alt="Foto Profil" 
                             class="w-20 h-20 rounded-2xl object-cover border-2 border-indigo-500/20 shadow-xs">
                    </div>

                    <!-- Tombol Upload & Kontrol -->
                    <div class="space-y-2 flex-1 min-w-0">
                        <label class="block text-sm font-semibold text-slate-900">Foto Profil Admin</label>
                        <p class="text-xs text-slate-500">Unggah foto profil dalam format JPG, PNG, atau WEBP. Gambar akan dipotong secara presisi (1:1).</p>
                        
                        <div class="flex items-center gap-2 pt-1">
                            <label class="inline-flex items-center gap-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-2 rounded-xl cursor-pointer transition shadow-xs">
                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span>Pilih Foto Baru</span>
                                <input type="file" name="avatar" x-ref="avatarInput" @change="onFileSelected($event)" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden">
                            </label>

                            <button type="button" 
                                    x-show="photoPreview" 
                                    @click="clearPhoto()" 
                                    class="inline-flex items-center gap-1.5 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 text-xs font-semibold px-3 py-2 rounded-xl cursor-pointer transition shrink-0">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Hapus Foto</span>
                            </button>
                        </div>
                        @error('avatar')
                            <p class="text-xs text-rose-600 font-medium pt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="name" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">
                            Nama Lengkap <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autocomplete="off"
                               class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white @error('name') border-rose-300 bg-rose-50/30 @enderror">
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">
                            Alamat Email <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="off"
                               class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white @error('email') border-rose-300 bg-rose-50/30 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Password Baru (Opsional) -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kata Sandi Baru (Opsional)</h2>
                    <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengganti kata sandi.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Password Baru --}}
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">
                            Password Baru
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   id="password" 
                                   placeholder="Kosongkan jika tidak diubah..." 
                                   autocomplete="new-password"
                                   class="w-full text-sm border border-slate-300 rounded-xl pl-3.5 pr-10 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white @error('password') border-rose-300 bg-rose-50/30 @enderror">
                            
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
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div x-data="{ showConfirmPassword: false }">
                        <label for="password_confirmation" class="h-6 flex items-baseline text-sm font-semibold text-slate-900 mb-1.5">
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   placeholder="Ulangi password baru..." 
                                   autocomplete="new-password"
                                   class="w-full text-sm border border-slate-300 rounded-xl pl-3.5 pr-10 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition bg-white">
                            
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
            </div>

            {{-- Submit & Cancel Button --}}
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between sm:justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition text-center shrink-0">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
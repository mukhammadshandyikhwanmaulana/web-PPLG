@extends('layouts.admin.app')

@section('title', 'Pengaturan Website')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-6 lg:px-8 py-4">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Pengaturan Website</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola identitas umum, informasi kontak, dan tautan media sosial website PPLG.</p>
        </div>
    </div>

    @php
        $settingObj = $setting ?? null;
        $settingsObj = $settings ?? null;

        $getSetting = function($key) use ($settingObj, $settingsObj) {
            if (isset($settingObj) && is_object($settingObj)) {
                return $settingObj->{$key} ?? '';
            }
            if (isset($settingsObj) && (is_array($settingsObj) || is_object($settingsObj))) {
                return is_array($settingsObj) ? ($settingsObj[$key] ?? '') : ($settingsObj->{$key} ?? '');
            }
            return '';
        };
    @endphp

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: IDENTITAS WEBSITE -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-4">
            <div class="border-b border-slate-200 pb-3 flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Identitas Website</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pengaturan nama, jargon, dan deskripsi utama yang tampil di website.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="site_name" class="block text-sm font-semibold text-slate-900 mb-1.5">Nama Website <span class="text-rose-500 ml-1">*</span></label>
                    <input type="text" name="site_name" id="site_name" required
                           value="{{ old('site_name', $getSetting('site_name')) }}"
                           placeholder="Contoh: PPLG SMKN 1 Bangsri"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('site_name') border-rose-300 bg-rose-50/30 @enderror">
                    @error('site_name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="site_tagline" class="block text-sm font-semibold text-slate-900 mb-1.5">Tagline / Jargon</label>
                    <input type="text" name="site_tagline" id="site_tagline"
                           value="{{ old('site_tagline', $getSetting('site_tagline')) }}"
                           placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('site_tagline') border-rose-300 bg-rose-50/30 @enderror">
                    @error('site_tagline')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="site_description" class="block text-sm font-semibold text-slate-900 mb-1.5">Deskripsi Singkat Website</label>
                <textarea name="site_description" id="site_description" rows="3"
                          placeholder="Tuliskan gambaran umum mengenai website atau program keahlian..."
                          class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('site_description') border-rose-300 bg-rose-50/30 @enderror">{{ old('site_description', $getSetting('site_description')) }}</textarea>
                @error('site_description')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- SECTION 2: INFORMASI KONTAK -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-4">
            <div class="border-b border-slate-200 pb-3 flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Informasi Kontak</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Alamat email, nomor telepon, dan lokasi resmi yang dapat dihubungi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="contact_email" class="block text-sm font-semibold text-slate-900 mb-1.5">Email Kontak</label>
                    <input type="email" name="contact_email" id="contact_email"
                           value="{{ old('contact_email', $getSetting('contact_email')) }}"
                           placeholder="Contoh: rplsmkn1bangsri@gmail.com"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('contact_email') border-rose-300 bg-rose-50/30 @enderror">
                    @error('contact_email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-semibold text-slate-900 mb-1.5">Nomor Telepon / HP</label>
                    <input type="text" name="contact_phone" id="contact_phone"
                           value="{{ old('contact_phone', $getSetting('contact_phone')) }}"
                           placeholder="Contoh: (0291) 7701100 atau 08123456789"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('contact_phone') border-rose-300 bg-rose-50/30 @enderror">
                    @error('contact_phone')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="contact_address" class="block text-sm font-semibold text-slate-900 mb-1.5">Alamat Lengkap</label>
                <textarea name="contact_address" id="contact_address" rows="3"
                          placeholder="Tuliskan alamat lengkap sekolah/kantor..."
                          class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition resize-none @error('contact_address') border-rose-300 bg-rose-50/30 @enderror">{{ old('contact_address', $getSetting('contact_address')) }}</textarea>
                @error('contact_address')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- SECTION 3: MEDIA SOSIAL (TIKTOK, INSTAGRAM, YOUTUBE) -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-xs p-4 sm:p-6 space-y-4">
            <div class="border-b border-slate-200 pb-3 flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 005.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Media Sosial & Tautan Eksternal</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tautan akun jejaring sosial resmi untuk dihubungkan ke halaman publik.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label for="tiktok_url" class="block text-sm font-semibold text-slate-900 mb-1.5">TikTok URL</label>
                    <input type="url" name="tiktok_url" id="tiktok_url"
                           value="{{ old('tiktok_url', $getSetting('tiktok_url')) }}"
                           placeholder="https://tiktok.com/@pplg_smkn1bangsri"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('tiktok_url') border-rose-300 bg-rose-50/30 @enderror">
                    @error('tiktok_url')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="instagram_url" class="block text-sm font-semibold text-slate-900 mb-1.5">Instagram URL</label>
                    <input type="url" name="instagram_url" id="instagram_url"
                           value="{{ old('instagram_url', $getSetting('instagram_url')) }}"
                           placeholder="https://instagram.com/pplg_smkn1bangsri"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('instagram_url') border-rose-300 bg-rose-50/30 @enderror">
                    @error('instagram_url')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="youtube_url" class="block text-sm font-semibold text-slate-900 mb-1.5">YouTube URL</label>
                    <input type="url" name="youtube_url" id="youtube_url"
                           value="{{ old('youtube_url', $getSetting('youtube_url')) }}"
                           placeholder="https://youtube.com/@pplg_smkn1bangsri"
                           class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 shadow-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition @error('youtube_url') border-rose-300 bg-rose-50/30 @enderror">
                    @error('youtube_url')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- BOTTOM SUBMIT ACTION -->
        <div class="pt-2 flex items-center justify-end">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-xs transition duration-150 text-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
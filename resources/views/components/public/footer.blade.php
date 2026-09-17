<footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800 font-sans mt-auto shrink-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">

            <!-- Identitas Sekolah -->
            <div class="space-y-4 md:col-span-1">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-pplg.png') }}" 
                         alt="Logo PPLG" 
                         onerror="this.onerror=null; this.src='https://placehold.co/100x100/FB8C00/white?text=PPLG';"
                         class="w-9 h-9 object-contain shrink-0">
                    <span class="font-bold text-lg text-white tracking-tight">
                        {{ $siteSetting->site_name ?? config('app.name', 'PPLG SMKN 1 Bangsri') }}
                    </span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    {{ $siteSetting->site_description ?? 'Kompetensi keahlian yang berfokus pada rekayasa perangkat lunak, pemrograman web, mobile, serta pengembangan gim berstandar industri.' }}
                </p>
            </div>

            <!-- Tautan Navigasi Cepat -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Navigasi Cepat</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-orange-400 transition-colors duration-200">Beranda</a>
                    </li>
                    <li>
                        <a href="{{ route('public.profile') }}" class="hover:text-orange-400 transition-colors duration-200">Tentang / Profil</a>
                    </li>
                    <li>
                        <a href="{{ route('public.profile') }}#guru-staf" class="hover:text-orange-400 transition-colors duration-200">Guru &amp; Staf Pengajar</a>
                    </li>
                    <li>
                        <a href="{{ route('public.achievements.index') }}" class="hover:text-orange-400 transition-colors duration-200">Prestasi Siswa</a>
                    </li>
                    <li>
                        <a href="{{ route('public.activities.index') }}" class="hover:text-orange-400 transition-colors duration-200">Kegiatan PPLG</a>
                    </li>
                </ul>
            </div>

            <!-- Dokumentasi & Informasi -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Informasi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('public.student-works.index') }}" class="hover:text-orange-400 transition-colors duration-200">Karya Siswa</a>
                    </li>
                    <li>
                        {{-- PERBAIKAN: Mengarahkan rute ke halaman Galeri Dokumentasi Utama (public.galleries.index) --}}
                        <a href="{{ route('public.galleries.index') }}" class="hover:text-orange-400 transition-colors duration-200">Galeri &amp; Dokumentasi</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#faq" class="hover:text-orange-400 transition-colors duration-200">Pertanyaan Umum (FAQ)</a>
                    </li>

                    {{-- Bagian Tautan Unit Usaha --}}
                    @if(isset($unitUsahaLink) && !empty($unitUsahaLink?->is_active) && (!empty($unitUsahaLink?->external_url) || !empty($unitUsahaLink?->url)))
                        <li>
                            <a href="{{ $unitUsahaLink->external_url ?? $unitUsahaLink->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-orange-400 transition-colors duration-200 inline-flex items-center gap-1">
                                {{ $unitUsahaLink->label ?? 'Unit Usaha' }} &nearr;
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Kontak & Alamat Dinamis -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Kontak Jurusan</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    @if(!empty($siteSetting?->contact_address))
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $siteSetting->contact_address }}</span>
                        </li>
                    @endif

                    @if(!empty($siteSetting?->contact_email))
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <a href="mailto:{{ $siteSetting->contact_email }}" class="hover:text-orange-400 transition-colors duration-200">
                                {{ $siteSetting->contact_email }}
                            </a>
                        </li>
                    @endif

                    @if(!empty($siteSetting?->contact_phone))
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>{{ $siteSetting->contact_phone }}</span>
                        </li>
                    @endif
                </ul>

                <!-- Media Sosial Links -->
                @if(!empty($siteSetting?->tiktok_url) || !empty($siteSetting?->instagram_url) || !empty($siteSetting?->youtube_url))
                    <div class="mt-4 pt-4 border-t border-slate-800">
                        <p class="text-xs font-semibold text-slate-400 mb-2.5">Ikuti Media Sosial</p>
                        <div class="flex items-center gap-2.5">

                            {{-- TikTok Icon --}}
                            @if(!empty($siteSetting->tiktok_url))
                                <a href="{{ $siteSetting->tiktok_url }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 flex items-center justify-center bg-black hover:bg-slate-800 text-white rounded-xl transition-all duration-200 group border border-slate-700/80 shadow-xs shrink-0" 
                                   title="TikTok PPLG">
                                    <svg class="w-4 h-4 fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64c.29 0 .56.04.82.12V9.4a6.33 6.33 0 00-1-.08A6.34 6.34 0 003 15.66a6.34 6.34 0 0010.86 4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.02z"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Instagram Icon --}}
                            @if(!empty($siteSetting->instagram_url))
                                <a href="{{ $siteSetting->instagram_url }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 flex items-center justify-center bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 hover:opacity-90 text-white rounded-xl transition-all duration-200 group shadow-xs shrink-0" 
                                   title="Instagram PPLG">
                                    <svg class="w-4 h-4 fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- YouTube Icon --}}
                            @if(!empty($siteSetting->youtube_url))
                                <a href="{{ $siteSetting->youtube_url }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition-all duration-200 group shadow-xs shrink-0" 
                                   title="YouTube PPLG">
                                    <svg class="w-4 h-4 fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                            @endif

                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Hak Cipta -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; {{ date('Y') }} {{ $siteSetting->site_name ?? config('app.name', 'PPLG SMKN 1 Bangsri') }}. All rights reserved.</p>
            <p>{{ $siteSetting->site_tagline ?? 'Dikembangkan untuk Kompetensi Keahlian PPLG' }}</p>
        </div>
    </div>
</footer>
<header x-data="{ scrolled: false, mobileOpen: false, aboutOpen: false }"
        @scroll.window="scrolled = (window.pageYOffset > 20)"
        @click.away="mobileOpen = false; aboutOpen = false"
        :class="{
            'bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs py-0 text-slate-800': scrolled || mobileOpen,
            'bg-transparent py-1 text-white': !scrolled && !mobileOpen
        }"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 font-sans">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">

            <!-- 1. BRAND / LOGO KIRI -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <img src="{{ asset('images/logo-pplg.png') }}" 
                         alt="Logo PPLG" 
                         onerror="this.onerror=null; this.src='https://placehold.co/100x100/FB8C00/white?text=PPLG';"
                         class="w-10 h-10 object-contain group-hover:scale-105 transition duration-300">

                    <div class="h-8 w-px transition-colors duration-300" 
                         :class="(scrolled || mobileOpen) ? 'bg-slate-300' : 'bg-white/40'"></div>

                    <div class="flex flex-col justify-center">
                        <span class="font-black text-xl leading-none tracking-tight transition-colors duration-300"
                              :class="(scrolled || mobileOpen) ? 'text-orange-500' : 'text-white'">
                            PPLG
                        </span>
                        <span class="text-[10px] font-bold tracking-wider uppercase mt-1 transition-colors duration-300 hidden sm:inline-block xl:block"
                              :class="(scrolled || mobileOpen) ? 'text-slate-600' : 'text-white'">
                            Pengembangan Perangkat Lunak &amp; Gim
                        </span>
                    </div>
                </a>
            </div>

            <!-- 2. NAVIGASI TENGAH (DESKTOP) -->
            <nav class="hidden lg:flex items-center justify-center gap-6 xl:gap-8 flex-1">

                {{-- BERANDA --}}
                @php $activeHome = request()->routeIs('home'); @endphp
                <a href="{{ route('home') }}" 
                   :class="scrolled 
                       ? '{{ $activeHome ? "text-orange-600 font-bold" : "text-slate-700 hover:text-orange-600 font-medium" }}' 
                       : '{{ $activeHome ? "text-white font-bold" : "text-white/90 hover:text-white font-medium" }}'"
                   class="text-sm tracking-wide transition-all duration-200 relative py-1.5 group whitespace-nowrap">
                    Beranda
                    <span class="absolute bottom-0 left-0 h-0.5 rounded-full transition-all duration-300 {{ $activeHome ? 'w-full' : 'w-0 group-hover:w-full' }}"
                          :class="scrolled ? 'bg-orange-500' : 'bg-white'"></span>
                </a>

                {{-- DROPDOWN: TENTANG KAMI --}}
                @php $activeProfile = request()->routeIs('public.profile'); @endphp
                <div class="relative" @mouseleave="aboutOpen = false">
                    <button type="button" 
                            @click="aboutOpen = !aboutOpen"
                            @mouseenter="aboutOpen = true"
                            :class="scrolled 
                                ? '{{ $activeProfile ? "text-orange-600 font-bold" : "text-slate-700 hover:text-orange-600 font-medium" }}' 
                                : '{{ $activeProfile ? "text-white font-bold" : "text-white/90 hover:text-white font-medium" }}'"
                            class="text-sm tracking-wide transition-all duration-200 relative py-1.5 group whitespace-nowrap inline-flex items-center cursor-pointer">
                        <span>Tentang Kami</span>
                        <span class="absolute bottom-0 left-0 h-0.5 rounded-full transition-all duration-300 {{ $activeProfile ? 'w-full' : 'w-0 group-hover:w-full' }}"
                              :class="scrolled ? 'bg-orange-500' : 'bg-white'"></span>
                    </button>

                    <!-- ISINYA DROPDOWN (DESKTOP) -->
                    <div x-show="aboutOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         x-cloak
                         class="absolute top-full left-0 w-48 pt-2 z-50">
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl p-2 space-y-1 text-slate-800">
                            <a href="{{ route('public.profile') }}#sejarah" 
                               @click="aboutOpen = false"
                               class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                Sejarah Jurusan
                            </a>
                            <a href="{{ route('public.profile') }}#visi-misi" 
                               @click="aboutOpen = false"
                               class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                Visi &amp; Misi
                            </a>
                            <a href="{{ route('public.profile') }}#guru-staf" 
                               @click="aboutOpen = false"
                               class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                Guru &amp; Staf Pengajar
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KARYA SISWA --}}
                @php $activeWorks = request()->routeIs('public.student-works.*'); @endphp
                <a href="{{ route('public.student-works.index') }}" 
                   :class="scrolled 
                       ? '{{ $activeWorks ? "text-orange-600 font-bold" : "text-slate-700 hover:text-orange-600 font-medium" }}' 
                       : '{{ $activeWorks ? "text-white font-bold" : "text-white/90 hover:text-white font-medium" }}'"
                   class="text-sm tracking-wide transition-all duration-200 relative py-1.5 group whitespace-nowrap">
                    Karya Siswa
                    <span class="absolute bottom-0 left-0 h-0.5 rounded-full transition-all duration-300 {{ $activeWorks ? 'w-full' : 'w-0 group-hover:w-full' }}"
                          :class="scrolled ? 'bg-orange-500' : 'bg-white'"></span>
                </a>

                {{-- PRESTASI --}}
                @php $activeAch = request()->routeIs('public.achievements.*'); @endphp
                <a href="{{ route('public.achievements.index') }}" 
                   :class="scrolled 
                       ? '{{ $activeAch ? "text-orange-600 font-bold" : "text-slate-700 hover:text-orange-600 font-medium" }}' 
                       : '{{ $activeAch ? "text-white font-bold" : "text-white/90 hover:text-white font-medium" }}'"
                   class="text-sm tracking-wide transition-all duration-200 relative py-1.5 group whitespace-nowrap">
                    Prestasi
                    <span class="absolute bottom-0 left-0 h-0.5 rounded-full transition-all duration-300 {{ $activeAch ? 'w-full' : 'w-0 group-hover:w-full' }}"
                          :class="scrolled ? 'bg-orange-500' : 'bg-white'"></span>
                </a>

                {{-- KEGIATAN --}}
                @php $activeAct = request()->routeIs('public.activities.*'); @endphp
                <a href="{{ route('public.activities.index') }}" 
                   :class="scrolled 
                       ? '{{ $activeAct ? "text-orange-600 font-bold" : "text-slate-700 hover:text-orange-600 font-medium" }}' 
                       : '{{ $activeAct ? "text-white font-bold" : "text-white/90 hover:text-white font-medium" }}'"
                   class="text-sm tracking-wide transition-all duration-200 relative py-1.5 group whitespace-nowrap">
                    Kegiatan
                    <span class="absolute bottom-0 left-0 h-0.5 rounded-full transition-all duration-300 {{ $activeAct ? 'w-full' : 'w-0 group-hover:w-full' }}"
                          :class="scrolled ? 'bg-orange-500' : 'bg-white'"></span>
                </a>

                <!-- Unit Usaha (Opsional) -->
                @if(isset($unitUsahaLink) && !empty($unitUsahaLink?->is_active) && (!empty($unitUsahaLink?->external_url) || !empty($unitUsahaLink?->url)))
                    <a href="{{ $unitUsahaLink->external_url ?? $unitUsahaLink->url }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       :class="scrolled ? 'text-slate-700 hover:text-orange-600 font-medium' : 'text-white/90 hover:text-white font-medium'"
                       class="text-sm tracking-wide transition-all duration-200 relative py-1.5 group whitespace-nowrap">
                        {{ $unitUsahaLink->label ?? 'Unit Usaha' }}
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 rounded-full transition-all duration-300 group-hover:w-full"
                              :class="scrolled ? 'bg-orange-500' : 'bg-white'"></span>
                    </a>
                @endif
            </nav>

            <!-- 3. CTA KANAN (DESKTOP) -->
            <div class="hidden lg:flex items-center shrink-0">
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=rplsmkn1bangsri@gmail.com" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   :class="scrolled 
                       ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-sm' 
                       : 'bg-transparent text-white border border-white/80 hover:bg-orange-500 hover:border-orange-500 hover:text-white'"
                   class="inline-flex items-center justify-center px-5 py-2 text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer whitespace-nowrap tracking-wider">
                    HUBUNGI KAMI
                </a>
            </div>

            <!-- TOGGLE MENU MOBILE -->
            <div class="lg:hidden flex items-center justify-end">
                <button type="button" 
                        @click="mobileOpen = !mobileOpen" 
                        :aria-expanded="mobileOpen"
                        aria-label="Toggle Navigation Menu"
                        :class="(scrolled || mobileOpen) ? 'text-slate-800 hover:text-orange-600' : 'text-white hover:text-slate-200'"
                        class="p-2 focus:outline-hidden rounded-xl transition-colors duration-200 cursor-pointer">
                    <svg class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-90': mobileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU CONTAINER -->
    <div x-show="mobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         x-cloak
         class="lg:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-xl px-4 pt-3 pb-5 space-y-1.5 text-slate-800 shadow-xl">

        <div class="px-3 py-2 mb-2 border-b border-slate-100">
            <p class="text-xs font-black text-orange-600 tracking-tight">PPLG SMKN 1 BANGSRI</p>
            <p class="text-[11px] font-medium text-slate-500">Pengembangan Perangkat Lunak dan Gim</p>
        </div>

        <a href="{{ route('home') }}" 
           class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('home') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-orange-600' }}">
            Beranda
        </a>

        <!-- ACCORDION DROPDOWN MOBILE: TENTANG KAMI -->
        <div x-data="{ mobileAbout: false }">
            <button type="button" 
                    @click="mobileAbout = !mobileAbout"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition-all duration-150 cursor-pointer">
                <span>Tentang Kami</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': mobileAbout }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="mobileAbout" x-cloak class="pl-4 pr-2 py-1 space-y-1 bg-slate-50/60 rounded-xl my-1">
                <a href="{{ route('public.profile') }}#sejarah" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-orange-600">
                    Sejarah Jurusan
                </a>
                <a href="{{ route('public.profile') }}#visi-misi" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-orange-600">
                    Visi &amp; Misi
                </a>
                <a href="{{ route('public.profile') }}#guru-staf" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-orange-600">
                    Guru &amp; Staf Pengajar
                </a>
            </div>
        </div>

        <a href="{{ route('public.student-works.index') }}" 
           class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('public.student-works.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-orange-600' }}">
            Karya Siswa
        </a>

        <a href="{{ route('public.achievements.index') }}" 
           class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('public.achievements.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-orange-600' }}">
            Prestasi
        </a>

        <a href="{{ route('public.activities.index') }}" 
           class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('public.activities.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-orange-600' }}">
            Kegiatan
        </a>

        @if(isset($unitUsahaLink) && !empty($unitUsahaLink?->is_active) && (!empty($unitUsahaLink?->external_url) || !empty($unitUsahaLink?->url)))
            <a href="{{ $unitUsahaLink->external_url ?? $unitUsahaLink->url }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition-all duration-150">
                {{ $unitUsahaLink->label ?? 'Unit Usaha' }}
            </a>
        @endif

        <div class="pt-3 mt-2 border-t border-slate-100">
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=rplsmkn1bangsri@gmail.com" 
               target="_blank" 
               rel="noopener noreferrer"
               class="block w-full text-center px-4 py-2.5 text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-xs transition duration-200 tracking-wider">
                HUBUNGI KAMI
            </a>
        </div>
    </div>
</header>
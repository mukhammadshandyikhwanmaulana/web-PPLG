<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Guru') — {{ config('app.name', 'PPLG System') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-hvNR0F/e2J7zPPfLC9auFe3/SE0yG4aJCOd/qxew74NN7eyiSKjr7xJJMu1Jy2wf7FXITpWS1E/RY8yzuXN7VA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
        .no-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased overflow-hidden" 
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">
    
    @php
        $user = auth()->user();
        $userAvatar = $user?->avatar_url ?? ($user?->avatar ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) : null);
        $guruAccountRoute = \Illuminate\Support\Facades\Route::has('guru.account.edit') ? route('guru.account.edit') : '#';
    @endphp

    <div class="h-full flex overflow-hidden">
        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"></div>

        {{-- Sidebar Guru --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static shrink-0 flex flex-col h-full shadow-xl lg:shadow-none"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="px-4 py-4 border-b border-slate-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ asset('images/logo-pplg.png') }}" 
                         alt="Logo PPLG" 
                         onerror="this.onerror=null; this.src='https://placehold.co/100x100/4f46e5/white?text=PPLG';"
                         class="w-7 h-7 object-contain shrink-0">

                    <div class="flex flex-col min-w-0">
                        <span class="text-xs sm:text-sm font-bold text-white tracking-wide leading-tight whitespace-nowrap">Portal Guru</span>
                        <span class="text-[9px] sm:text-[10px] font-semibold text-indigo-400 tracking-wider uppercase whitespace-nowrap">Kompetensi Keahlian PPLG</span>
                    </div>
                </div>
                
                <button type="button" 
                        @click="sidebarOpen = false" 
                        class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg active:bg-slate-800 transition cursor-pointer shrink-0 ml-1" 
                        aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <nav id="guru-sidebar-nav" class="p-4 space-y-1 text-sm overflow-y-auto flex-1 no-scrollbar">
                @if(\Illuminate\Support\Facades\Route::has('guru.dashboard'))
                    <a href="{{ route('guru.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('guru.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                @endif

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold">Konten Pembelajaran</div>
                
                @if(\Illuminate\Support\Facades\Route::has('guru.karya-siswa.index'))
                    <a href="{{ route('guru.karya-siswa.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('guru.karya-siswa.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Karya Siswa</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('guru.kegiatan.index'))
                    <a href="{{ route('guru.kegiatan.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('guru.kegiatan.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kegiatan & Berita</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('guru.prestasi.index'))
                    <a href="{{ route('guru.prestasi.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('guru.prestasi.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>Prestasi</span>
                    </a>
                @endif

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold">Akun Saya</div>
                <a href="{{ $guruAccountRoute }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('guru.account.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Pengaturan Profil</span>
                </a>
            </nav>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto no-scrollbar">
            
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-10 shadow-xs shrink-0 gap-3">
                
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <button type="button" 
                            class="lg:hidden p-1.5 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none cursor-pointer transition shrink-0" 
                            @click="sidebarOpen = !sidebarOpen" 
                            aria-label="Toggle menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-[11px] sm:text-sm font-bold text-slate-800 tracking-tight whitespace-nowrap overflow-x-auto no-scrollbar">
                            <span>Portal Pengajar & Guru Bimbingan</span>
                            <span class="text-indigo-600 ml-0.5">(PPLG)</span>
                        </h2>
                        <p class="hidden sm:block text-[11px] text-slate-400 font-medium whitespace-nowrap mt-0.5">
                            Sistem Manajemen Konten Pembelajaran & Kegiatan Siswa
                        </p>
                    </div>
                </div>

                <div class="relative shrink-0" x-data="{ profileDropdownOpen: false }" @keydown.escape.window="profileDropdownOpen = false">
                    <button type="button" 
                            @click="profileDropdownOpen = !profileDropdownOpen" 
                            @click.away="profileDropdownOpen = false"
                            class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer group focus:outline-none select-none">
                        
                        @if($userAvatar)
                            <img src="{{ $userAvatar }}" 
                                 alt="{{ $user?->name }}" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/100x100/4f46e5/white?text={{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}';"
                                 class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl object-cover border border-indigo-100 shadow-xs shrink-0">
                        @else
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-50 text-indigo-700 group-hover:bg-indigo-600 group-hover:text-white font-bold text-xs flex items-center justify-center border border-indigo-100 shadow-xs shrink-0 transition">
                                {{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}
                            </div>
                        @endif
                        
                        <div class="hidden sm:block text-left">
                            <p class="text-xs sm:text-sm font-semibold text-slate-800 group-hover:text-indigo-600 leading-none whitespace-nowrap transition">
                                {{ $user?->name ?? 'Guru PPLG' }} 
                            </p>
                            <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-0.5 whitespace-nowrap">
                                Tenaga Pendidik / Guru
                            </p>
                        </div>

                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-200 shrink-0 hidden sm:block" 
                             :class="profileDropdownOpen ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="profileDropdownOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 divide-y divide-slate-100">
                        
                        <div class="px-4 py-2.5">
                            <p class="text-xs font-bold text-slate-800 tracking-wide uppercase">Pengaturan Guru</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ $guruAccountRoute }}" 
                               @click="profileDropdownOpen = false"
                               class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profil Akun</span>
                            </a>
                        </div>

                        <div class="py-1">
                            @if(\Illuminate\Support\Facades\Route::has('logout'))
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center gap-3 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer border-0 bg-transparent">
                                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Alert Container Global --}}
            <div class="px-4 sm:px-6 pt-5 space-y-2 max-w-7xl mx-auto w-full shrink-0">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-cloak class="bg-emerald-50 text-emerald-900 border border-emerald-200 rounded-2xl p-4 text-sm shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold p-1 cursor-pointer">✕</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-cloak class="bg-rose-50 text-rose-900 border border-rose-200 rounded-xl p-4 text-sm flex justify-between items-start shadow-xs">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="font-bold mb-1">Terdapat beberapa kesalahan input:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-800 text-sm font-bold p-1 cursor-pointer">✕</button>
                    </div>
                @endif
            </div>

            <main class="flex-1 pb-12">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- MODAL GLOBAL CROPPER JS --}}
    <div x-data="globalImageCropper()" 
         x-show="open" 
         x-cloak 
         @open-cropper.window="initCrop($event.detail)"
         @keydown.escape.window="closeModal()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
        
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh]"
             @click.away="closeModal()">
            
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="text-sm font-bold text-slate-800" x-text="title">Potong Gambar</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-4 flex-1 overflow-hidden flex items-center justify-center bg-slate-900 min-h-[300px]">
                <img x-ref="cropImage" :src="imageSrc" class="max-w-full max-h-[60vh] object-contain">
            </div>

            <div class="px-5 py-4 border-t border-slate-100 bg-white flex items-center justify-end gap-2.5">
                <button type="button" @click="closeModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="applyCrop()" class="px-4 py-2 text-xs font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                    Simpan Potongan
                </button>
            </div>
        </div>
    </div>

    <!-- Cropper.js Script (Tanpa integrity agar tidak terblokir oleh browser) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        (function() {
            const nav = document.getElementById('guru-sidebar-nav');
            if (!nav) return;

            const activeMenu = nav.querySelector('.active-menu');
            const savedPos = localStorage.getItem('guru_sidebar_scroll_pos');

            if (savedPos !== null) {
                nav.scrollTop = parseInt(savedPos, 10);
            } else if (activeMenu) {
                activeMenu.scrollIntoView({ block: 'nearest' });
            }

            nav.addEventListener('scroll', () => {
                localStorage.setItem('guru_sidebar_scroll_pos', nav.scrollTop);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
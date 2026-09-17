<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name', 'PPLG System') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Cropper.js CSS via cdnjs Cloudflare (Lebih Stabil & Aman dari Blokir) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

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
        $userRoleName = $user ? ($user->role instanceof \BackedEnum ? $user->role->value : (string)$user->role) : 'admin';
        $userAvatar = $user?->avatar_url ?? ($user?->avatar ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) : null);
        
        $accountEditRoute = \Illuminate\Support\Facades\Route::has('admin.account.edit') ? route('admin.account.edit') : '#';
        $profilRoute      = \Illuminate\Support\Facades\Route::has('admin.profil.edit') ? route('admin.profil.edit') : '#';
        $sambutanRoute    = \Illuminate\Support\Facades\Route::has('admin.sambutan.edit') ? route('admin.sambutan.edit') : '#';
        $unitUsahaRoute   = \Illuminate\Support\Facades\Route::has('admin.unit-usaha.edit') ? route('admin.unit-usaha.edit') : '#';
        $pengaturanRoute  = \Illuminate\Support\Facades\Route::has('admin.pengaturan.edit') ? route('admin.pengaturan.edit') : '#';
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

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static shrink-0 flex flex-col h-full shadow-xl lg:shadow-none"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="px-4 py-4 border-b border-slate-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ asset('images/logo-pplg.png') }}" 
                         alt="Logo PPLG" 
                         onerror="this.onerror=null; this.src='https://placehold.co/100x100/4f46e5/white?text=PPLG';"
                         class="w-7 h-7 object-contain shrink-0">

                    <div class="flex flex-col min-w-0">
                        <span class="text-xs sm:text-sm font-bold text-white tracking-wide leading-tight whitespace-nowrap">Admin Panel</span>
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
            
            <nav id="admin-sidebar-nav" class="p-4 space-y-1 text-sm overflow-y-auto flex-1 no-scrollbar">
                @if(\Illuminate\Support\Facades\Route::has('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                @endif

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold">Manajemen Akun</div>
                @if(\Illuminate\Support\Facades\Route::has('admin.guru.index'))
                    <a href="{{ route('admin.guru.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.guru.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Guru & Staf</span>
                    </a>
                @endif

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold">Manajemen Konten</div>
                
                @if(\Illuminate\Support\Facades\Route::has('admin.banner.index'))
                    <a href="{{ route('admin.banner.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.banner.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Banner Hero</span>
                    </a>
                @endif

                <a href="{{ $profilRoute }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profil.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Profil Jurusan</span>
                </a>
                
                <a href="{{ $sambutanRoute }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.sambutan.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Sambutan Kajur</span>
                </a>

                @if(\Illuminate\Support\Facades\Route::has('admin.fasilitas.index'))
                    <a href="{{ route('admin.fasilitas.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.fasilitas.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Fasilitas</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('admin.prestasi.index'))
                    <a href="{{ route('admin.prestasi.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.prestasi.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>Prestasi</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('admin.karya-siswa.index'))
                    <a href="{{ route('admin.karya-siswa.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.karya-siswa.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Karya Siswa</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('admin.kegiatan.index'))
                    <a href="{{ route('admin.kegiatan.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.kegiatan.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kegiatan</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('admin.mitra.index'))
                    <a href="{{ route('admin.mitra.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.mitra.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Mitra Industri</span>
                    </a>
                @endif
                
                <a href="{{ $unitUsahaRoute }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.unit-usaha.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Unit Usaha</span>
                </a>

                @if(\Illuminate\Support\Facades\Route::has('admin.faq.index'))
                    <a href="{{ route('admin.faq.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.faq.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>FAQ</span>
                    </a>
                @endif

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold">Sistem</div>
                @if(\Illuminate\Support\Facades\Route::has('admin.media.index'))
                    <a href="{{ route('admin.media.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.media.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Perpustakaan Media</span>
                    </a>
                @endif
                
                <a href="{{ $pengaturanRoute }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.pengaturan.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan Web</span>
                </a>

                @if(\Illuminate\Support\Facades\Route::has('admin.activity-log.index'))
                    <a href="{{ route('admin.activity-log.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.activity-log.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/20 active-menu' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Activity Log</span>
                    </a>
                @endif
            </nav>
        </aside>

        {{-- Main Content --}}
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
                            <span class="hidden md:inline">Pengembangan Perangkat Lunak dan Gim</span>
                            <span class="md:hidden">Kompetensi Keahlian</span>
                            <span class="text-indigo-600 ml-0.5">(PPLG)</span>
                        </h2>
                        <p class="hidden sm:block text-[11px] text-slate-400 font-medium whitespace-nowrap mt-0.5">
                            Sistem Informasi & Portal Admin
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    {{-- Notifikasi --}}
                    @auth
                        @php
                            $unreadNotifications = auth()->user()->unreadNotifications->take(5);
                            $unreadCount = auth()->user()->unreadNotifications->count();
                            $markAllRoute = \Illuminate\Support\Facades\Route::has('admin.notifications.markAllAsRead') ? route('admin.notifications.markAllAsRead') : '#';
                        @endphp

                        <div class="relative" 
                             x-data="{ notificationOpen: false, count: {{ $unreadCount }} }" 
                             @keydown.escape.window="notificationOpen = false">
                            
                            <button type="button" 
                                    @click="notificationOpen = !notificationOpen" 
                                    @click.away="notificationOpen = false"
                                    class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition cursor-pointer focus:outline-none"
                                    title="Notifikasi Sistem">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>

                                <template x-if="count > 0">
                                    <span class="absolute -top-1 -right-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold text-white shadow-xs ring-2 ring-white animate-pulse"
                                          x-text="count > 9 ? '9+' : count">
                                    </span>
                                </template>
                            </button>

                            <div x-show="notificationOpen" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 overflow-hidden">
                                
                                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs sm:text-sm font-bold text-slate-900">Notifikasi Aktivitas</h3>
                                        <template x-if="count > 0">
                                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-indigo-100 text-indigo-700 rounded-full" x-text="count + ' baru'"></span>
                                        </template>
                                    </div>

                                    @if($unreadCount > 0 && $markAllRoute !== '#')
                                        <form method="POST" action="{{ $markAllRoute }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer bg-transparent border-0 p-0">
                                                ✓ Baca Semua
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 no-scrollbar">
                                    <template x-if="count > 0">
                                        <div>
                                            @foreach($unreadNotifications as $notification)
                                                @php
                                                    $actorName = $notification->data['actor_name'] ?? 'Sistem';
                                                    $subjectType = $notification->data['subject_type'] ?? 'Aktivitas';
                                                    $messageText = $notification->data['message'] ?? 'Aktivitas baru tercatat.';
                                                    $actorAvatar = $notification->data['actor_avatar'] ?? null;
                                                    $markReadSingleRoute = \Illuminate\Support\Facades\Route::has('admin.notifications.markAsRead') ? route('admin.notifications.markAsRead', $notification->id) : '#';
                                                @endphp
                                                <form method="POST" action="{{ $markReadSingleRoute }}" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left p-3.5 hover:bg-slate-50 transition flex items-start gap-3 bg-transparent border-b border-slate-100 last:border-b-0 cursor-pointer">
                                                        @if($actorAvatar)
                                                            <img src="{{ $actorAvatar }}" alt="{{ $actorName }}" class="w-8 h-8 rounded-full object-cover shrink-0 mt-0.5 border border-slate-200">
                                                        @else
                                                            <div class="w-2 h-2 rounded-full bg-indigo-600 mt-2 shrink-0"></div>
                                                        @endif
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $actorName }} ({{ $subjectType }})</p>
                                                            <p class="text-xs text-slate-600 mt-0.5 line-clamp-2 leading-relaxed">{{ $messageText }}</p>
                                                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </template>

                                    <div x-show="count === 0" class="p-6 text-center text-xs text-slate-400">
                                        Belum ada notifikasi baru.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endauth

                    {{-- Dropdown Profil --}}
                    <div class="relative shrink-0" x-data="{ profileDropdownOpen: false }" @keydown.escape.window="profileDropdownOpen = false">
                        <button type="button" 
                                @click="profileDropdownOpen = !profileDropdownOpen" 
                                @click.away="profileDropdownOpen = false"
                                class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer group focus:outline-none select-none">
                            
                            @if($userAvatar)
                                <img src="{{ $userAvatar }}" 
                                     alt="{{ $user?->name }}" 
                                     onerror="this.onerror=null; this.src='https://placehold.co/100x100/4f46e5/white?text={{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}';"
                                     class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl object-cover border border-indigo-100 shadow-xs shrink-0">
                            @else
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-50 text-indigo-700 group-hover:bg-indigo-600 group-hover:text-white font-bold text-xs flex items-center justify-center border border-indigo-100 shadow-xs shrink-0 transition">
                                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            
                            <div class="hidden sm:block text-left">
                                <p class="text-xs sm:text-sm font-semibold text-slate-800 group-hover:text-indigo-600 leading-none whitespace-nowrap transition">
                                    {{ $user?->name ?? 'Administrator' }} 
                                </p>
                                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-0.5 whitespace-nowrap">
                                    {{ ucfirst($userRoleName) }}
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
                                <p class="text-xs font-bold text-slate-800 tracking-wide uppercase">Pengaturan</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ $accountEditRoute }}" 
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
                </div>
            </header>

            {{-- Global Alerts --}}
            <div class="px-4 sm:px-6 pt-5 space-y-2 max-w-7xl mx-auto w-full shrink-0">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-cloak class="bg-emerald-50 text-emerald-900 border border-emerald-200 rounded-2xl p-4 text-sm shadow-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="font-bold">{{ session('success') }}</span>
                            </div>
                            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold p-1 cursor-pointer">✕</button>
                        </div>
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

    {{-- MODAL GLOBAL CROPPER --}}
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
                <img x-ref="cropImage" src="" class="max-w-full max-h-[60vh] object-contain">
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

    <!-- Cropper.js Script via cdnjs Cloudflare -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <script>
        (function() {
            const nav = document.getElementById('admin-sidebar-nav');
            if (!nav) return;

            const activeMenu = nav.querySelector('.active-menu');
            const savedPos = localStorage.getItem('admin_sidebar_scroll_pos');

            if (savedPos !== null) {
                nav.scrollTop = parseInt(savedPos, 10);
            } else if (activeMenu) {
                activeMenu.scrollIntoView({ block: 'nearest' });
            }

            nav.addEventListener('scroll', () => {
                localStorage.setItem('admin_sidebar_scroll_pos', nav.scrollTop);
            });
        })();

        document.addEventListener('alpine:init', () => {
            Alpine.data('globalImageCropper', () => ({
                open: false,
                cropper: null,
                title: 'Potong Gambar',
                aspectRatio: 1,
                targetInput: null,
                targetPreview: null,
                currentFile: null,
                onCropComplete: null,

                initCrop(detail) {
                    this.title = detail.title || 'Potong Gambar';
                    this.aspectRatio = detail.aspectRatio || 1;
                    this.targetInput = detail.targetInput;
                    this.targetPreview = detail.targetPreview;
                    this.currentFile = detail.file;
                    this.onCropComplete = detail.onCropComplete || null;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.$refs.cropImage.src = e.target.result;
                        this.open = true;
                        this.$nextTick(() => {
                            if (this.cropper) {
                                this.cropper.destroy();
                            }
                            this.cropper = new Cropper(this.$refs.cropImage, {
                                aspectRatio: this.aspectRatio,
                                viewMode: 1,
                                autoCropArea: 1,
                                responsive: true
                            });
                        });
                    };
                    reader.readAsDataURL(detail.file);
                },

                applyCrop() {
                    if (!this.cropper) return;

                    const canvas = this.cropper.getCroppedCanvas();
                    if (!canvas) {
                        alert('Gagal memproses potongan gambar.');
                        return;
                    }

                    canvas.toBlob((blob) => {
                        if (!blob) return;

                        const croppedFile = new File([blob], this.currentFile.name, {
                            type: this.currentFile.type || 'image/png',
                            lastModified: Date.now()
                        });

                        if (this.targetInput) {
                            const container = new DataTransfer();
                            container.items.add(croppedFile);
                            this.targetInput.files = container.files;
                        }

                        if (this.targetPreview) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.targetPreview.src = e.target.result;
                            };
                            reader.readAsDataURL(croppedFile);
                        }

                        if (typeof this.onCropComplete === 'function') {
                            this.onCropComplete(croppedFile);
                        }

                        this.closeModal();
                    }, this.currentFile.type || 'image/png');
                },

                closeModal() {
                    this.open = false;
                    if (this.cropper) {
                        this.cropper.destroy();
                        this.cropper = null;
                    }
                }
            }));
        });
    </script>

    @stack('scripts')
</body>
</html>
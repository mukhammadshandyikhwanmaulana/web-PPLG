<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        {{-- Mobile Backdrop Overlay --}}
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900/60 lg:hidden" 
             style="display: none;"></div>

        {{-- Sidebar Navigasi --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-auto shrink-0 flex flex-col"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Header Sidebar -->
            <div class="px-6 py-4 text-lg font-bold border-b border-gray-800 flex items-center justify-between tracking-wide">
                <span class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    Admin Panel
                </span>
                
                <!-- Tombol Silang (X) Mobile -->
                <button type="button" 
                        @click.stop="sidebarOpen = false" 
                        class="lg:hidden text-gray-400 hover:text-white p-2.5 -mr-2 rounded-lg active:bg-gray-800 transition cursor-pointer relative z-50" 
                        aria-label="Tutup menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Link Navigasi -->
            <nav class="p-4 space-y-1 text-sm overflow-y-auto flex-1 custom-scrollbar">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-gray-400 font-bold">Manajemen Akun</div>
                <a href="{{ route('admin.guru.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.guru.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Guru
                </a>

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-gray-400 font-bold">Manajemen Konten</div>
                <a href="{{ route('admin.profil.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.profil.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Profil
                </a>
                <a href="{{ route('admin.fasilitas.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.fasilitas.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Fasilitas
                </a>
                <a href="{{ route('admin.prestasi.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.prestasi.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Prestasi
                </a>
                <a href="{{ route('admin.karya-siswa.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.karya-siswa.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Karya Siswa
                </a>
                <a href="{{ route('admin.kegiatan.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.kegiatan.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kegiatan
                </a>
                <a href="{{ route('admin.mitra.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.mitra.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Mitra
                </a>
                
                @foreach (['Unit Usaha', 'FAQ'] as $label)
                    <span class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-500 cursor-not-allowed text-xs">
                        <span>{{ $label }}</span>
                        <span class="text-[10px] bg-gray-800 text-gray-400 px-1.5 py-0.5 rounded">Segera</span>
                    </span>
                @endforeach

                <div class="pt-5 pb-1.5 px-3 text-[11px] uppercase tracking-wider text-gray-400 font-bold">System</div>
                @foreach (['Media', 'Pengaturan', 'Activity Log'] as $label)
                    <span class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-500 cursor-not-allowed text-xs">
                        <span>{{ $label }}</span>
                        <span class="text-[10px] bg-gray-800 text-gray-400 px-1.5 py-0.5 rounded">Segera</span>
                    </span>
                @endforeach
            </nav>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <button type="button" class="lg:hidden p-1.5 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none cursor-pointer" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center gap-4 ml-auto">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:inline-block">
                            {{ auth()->user()?->name ?? 'User' }} 
                            <span class="text-xs text-gray-400 font-normal">({{ auth()->user()?->getRoleNames()->first() ?? 'Admin' }})</span>
                        </span>
                    </div>

                    <div class="h-4 w-px bg-gray-200"></div>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-800 font-medium transition cursor-pointer">
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            {{-- Flash Alert Container --}}
            <div class="px-4 sm:px-6 pt-4 space-y-2 max-w-7xl mx-auto w-full">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" class="bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg p-3.5 text-sm flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-900 text-xs font-semibold cursor-pointer">✕</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" class="bg-rose-50 text-rose-800 border border-rose-200 rounded-lg p-3.5 text-sm flex justify-between items-start shadow-sm">
                        <div>
                            <p class="font-semibold mb-1">Terdapat beberapa kesalahan input:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-900 text-xs font-semibold cursor-pointer">✕</button>
                    </div>
                @endif
            </div>

            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
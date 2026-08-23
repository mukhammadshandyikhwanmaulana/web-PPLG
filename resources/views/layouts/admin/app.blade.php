<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900" x-data="{ sidebarOpen: false }">
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
             class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden" 
             style="display: none;"></div>

        {{-- Sidebar Navigasi --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white transform transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-auto shrink-0 flex flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="px-6 py-4 text-lg font-semibold border-b border-gray-800 flex items-center justify-between">
                <span>Admin Panel</span>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <nav class="p-4 space-y-1 text-sm overflow-y-auto flex-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Dashboard</a>

                <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-gray-500 font-semibold">Manajemen Akun</div>
                <a href="{{ route('admin.guru.index') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.guru.*') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Guru</a>

                <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-gray-500 font-semibold">Manajemen Konten</div>
                <a href="{{ route('admin.profil.edit') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.profil.*') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Profil</a>
                <a href="{{ route('admin.fasilitas.index') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.fasilitas.*') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Fasilitas</a>
                <a href="{{ route('admin.prestasi.index') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.prestasi.*') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Prestasi</a>
                <a href="{{ route('admin.karya-siswa.index') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.karya-siswa.*') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Karya Siswa</a>
                <a href="{{ route('admin.kegiatan.index') }}" class="block px-3 py-2 rounded-lg transition hover:bg-gray-800 {{ request()->routeIs('admin.kegiatan.*') ? 'bg-gray-800 font-medium text-white' : 'text-gray-300' }}">Kegiatan</a>
                
                @foreach (['Unit Usaha', 'Mitra', 'FAQ'] as $label)
                    <span class="block px-3 py-2 rounded-lg text-gray-500 cursor-not-allowed text-xs">{{ $label }} <span class="text-[10px] text-gray-600">(segera)</span></span>
                @endforeach

                <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-gray-500 font-semibold">System</div>
                @foreach (['Media', 'Pengaturan', 'Activity Log'] as $label)
                    <span class="block px-3 py-2 rounded-lg text-gray-500 cursor-not-allowed text-xs">{{ $label }} <span class="text-[10px] text-gray-600">(segera)</span></span>
                @endforeach
            </nav>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-10">
                <button class="lg:hidden p-1.5 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-4 ml-auto">
                    <span class="text-sm font-medium text-gray-700">
                        {{ auth()->user()?->name ?? 'User' }} 
                        <span class="text-xs text-gray-400 font-normal">({{ auth()->user()?->getRoleNames()->first() ?? 'Admin' }})</span>
                    </span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-800 font-medium transition">Keluar</button>
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
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 text-xs font-semibold">✕</button>
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
                        <button @click="show = false" class="text-rose-600 hover:text-rose-900 text-xs font-semibold">✕</button>
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
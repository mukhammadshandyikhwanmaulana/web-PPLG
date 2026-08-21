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
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white transform transition-transform lg:translate-x-0 lg:static lg:inset-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="px-6 py-4 text-lg font-semibold border-b border-gray-800">Admin Panel</div>
            <nav class="p-4 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">Dashboard</a>

                <div class="pt-4 pb-1 px-3 text-xs uppercase text-gray-500">Manajemen Akun</div>
                <a href="{{ route('admin.guru.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.guru.*') ? 'bg-gray-800' : '' }}">Guru</a>

                <div class="pt-4 pb-1 px-3 text-xs uppercase text-gray-500">Manajemen Konten</div>
                <a href="{{ route('admin.profil.edit') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.profil.*') ? 'bg-gray-800' : '' }}">Profil</a>
                <a href="{{ route('admin.fasilitas.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.fasilitas.*') ? 'bg-gray-800' : '' }}">Fasilitas</a>
                <a href="{{ route('admin.prestasi.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.prestasi.*') ? 'bg-gray-800' : '' }}">Prestasi</a>
                <a href="{{ route('admin.karya-siswa.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.karya-siswa.*') ? 'bg-gray-800' : '' }}">Karya Siswa</a>
            @foreach (['Kegiatan', 'Unit Usaha', 'Mitra', 'FAQ'] as $label)
                <span class="block px-3 py-2 rounded text-gray-500 cursor-not-allowed">{{ $label }} <span class="text-xs">(segera)</span></span>
            @endforeach

                <div class="pt-4 pb-1 px-3 text-xs uppercase text-gray-500">System</div>
                @foreach (['Media', 'Pengaturan', 'Activity Log'] as $label)
                    <span class="block px-3 py-2 rounded text-gray-500 cursor-not-allowed">{{ $label }} <span class="text-xs">(segera)</span></span>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b px-4 py-3 flex items-center justify-between">
                <button class="lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-4 ml-auto">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }} <span class="text-xs text-gray-400">({{ auth()->user()->getRoleNames()->first() }})</span></span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Keluar</button>
                    </form>
                </div>
            </header>

            <div class="px-4 pt-4 space-y-2">
                @if (session('success'))
                    <div class="bg-green-50 text-green-800 border border-green-200 rounded px-4 py-2 text-sm">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-50 text-red-800 border border-red-200 rounded px-4 py-2 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <main class="flex-1 p-4">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
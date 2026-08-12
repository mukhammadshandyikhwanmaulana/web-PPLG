<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guru') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <header class="bg-white border-b px-4 py-3 flex items-center justify-between">
        <div class="font-semibold">Guru Panel</div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">{{ auth()->user()->name }} <span class="text-xs text-gray-400">({{ auth()->user()->getRoleNames()->first() }})</span></span>
            <form method="POST" action="{{ route('guru.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:underline">Keluar</button>
            </form>
        </div>
    </header>

    <div class="px-4 pt-4 space-y-2">
        @if (session('success'))
            <div class="bg-green-50 text-green-800 border border-green-200 rounded px-4 py-2 text-sm">{{ session('success') }}</div>
        @endif
    </div>

    <main class="p-4">
        @yield('content')
    </main>
</body>
</html>
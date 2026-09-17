<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title & SEO Dinamis dari Pengaturan Web -->
    <title>
        @hasSection('title')
            @yield('title') - {{ $siteSetting->site_name ?? 'PPLG SMKN 1 Bangsri' }}
        @else
            {{ $siteSetting->site_name ?? 'PPLG SMKN 1 Bangsri' }} - {{ $siteSetting->site_tagline ?? 'Pengembangan Perangkat Lunak & Gim' }}
        @endif
    </title>
    
    <meta name="description" content="@yield('meta_description', $siteSetting->site_description ?? 'Kompetensi Keahlian Pengembangan Perangkat Lunak dan Gim SMKN 1 Bangsri Jepara.')">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo-pplg.png') }}" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800;900&family=JetBrains+Mono:ital,wght@0,400..800;1,400..800&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans selection:bg-orange-500 selection:text-white flex flex-col min-h-screen">

    <!-- Header / Navigation Bar -->
    @include('components.public.navbar')

    <!-- Konten Utama Halaman (flex-grow & flex-1 mengisi sisa ruang secara dinamis) -->
    <main class="w-full flex-grow flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.public.footer')

    @stack('scripts')
</body>
</html>
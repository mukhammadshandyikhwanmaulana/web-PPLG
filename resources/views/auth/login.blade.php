<!DOCTYPE html>
<html lang="id" class="min-h-screen w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk ke Akun Anda — {{ config('app.name', 'PPLG Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        input:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="min-h-screen w-full font-sans antialiased text-slate-800 relative bg-slate-900 select-none flex items-center justify-center p-4 sm:p-6">

    <!-- Background Image Utama -->
    <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat pointer-events-none"
         style="background-image: url('{{ asset('images/login-bg.jpg') }}');">
        <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs"></div>
    </div>

    <!-- Container Utama Form Login -->
    <div class="relative z-10 w-full max-w-sm lg:max-w-4xl my-auto"
         x-data="{ 
            selectedRole: '{{ strtolower(old('role', 'guru')) }}', 
            showPassword: false 
         }">
        
        <!-- Main Card Container -->
        <div class="w-full rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-white/30 transition-all duration-300">
            
            <!-- AREA KIRI: Glassmorphism Blur Tipis / Transparan -->
            <div class="lg:col-span-5 bg-white/10 backdrop-blur-md text-white p-6 sm:p-8 flex flex-col justify-center items-center text-center border-b lg:border-b-0 lg:border-r border-white/30 relative">
                
                <div class="space-y-4 w-full my-auto flex flex-col items-center">
                    
                    <!-- Logo Tanpa Container -->
                    <div class="inline-flex items-center justify-center">
                        <img src="{{ asset('images/logo-pplg.png') }}" 
                             alt="Logo PPLG" 
                             onerror="this.onerror=null; this.src='https://placehold.co/120x120/transparent/ffffff?text=PPLG';"
                             class="h-16 sm:h-20 lg:h-24 w-auto object-contain filter drop-shadow-md">
                    </div>

                    <!-- Teks Judul & Sub-Judul Responsif Tanpa Terpotong -->
                    <div class="space-y-1 w-full px-2">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight drop-shadow-md">PPLG</h2>
                        <p class="text-[11px] sm:text-xs lg:text-sm text-white/95 font-semibold leading-relaxed w-full break-words drop-shadow-sm">
                            Pengembangan Perangkat Lunak dan Gim
                        </p>
                    </div>

                    <!-- Quote Box Ringkas (Desktop) -->
                    <div class="hidden lg:block w-full pt-2">
                        <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/20 text-center shadow-inner">
                            <p class="text-[11px] text-white/95 italic font-normal leading-relaxed drop-shadow-xs">
                                "Mengembangkan potensi pengajar dan peserta didik melalui integrasi teknologi yang terstruktur."
                            </p>
                        </div>
                    </div>

                </div>

            </div>

            <!-- AREA KANAN: Form Area -->
            <div class="lg:col-span-7 bg-stone-50/95 p-6 sm:p-8 flex flex-col justify-center space-y-4">
                
                <!-- Header Greeting -->
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-800">Selamat Datang!</h1>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Silakan masuk menggunakan kredensial akun Anda</p>
                </div>

                <!-- Tab Switcher Role (Admin / Guru) -->
                <div class="bg-stone-200/70 p-1 rounded-2xl flex items-center gap-1 border border-stone-300/60">
                    <button type="button" 
                            @click="selectedRole = 'admin'"
                            :class="selectedRole === 'admin' 
                                ? 'bg-orange-500 text-white font-bold shadow-sm' 
                                : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="flex-1 py-2 px-3 rounded-xl text-xs transition duration-200 text-center cursor-pointer">
                        Login Admin
                    </button>

                    <button type="button" 
                            @click="selectedRole = 'guru'"
                            :class="selectedRole === 'guru' 
                                ? 'bg-orange-500 text-white font-bold shadow-sm' 
                                : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="flex-1 py-2 px-3 rounded-xl text-xs transition duration-200 text-center cursor-pointer">
                        Login Guru
                    </button>
                </div>

                <!-- Flash Alert Error Message -->
                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-cloak class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-700 font-medium flex items-start justify-between gap-2 shadow-xs">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-400 hover:text-rose-700 font-bold p-0.5 cursor-pointer">✕</button>
                    </div>
                @endif

                <!-- Form Autentikasi dengan Autocomplete OFF -->
                <form action="{{ route('login.store') }}" method="POST" autocomplete="off" class="space-y-3.5">
                    @csrf

                    <!-- Hidden Input Role -->
                    <input type="hidden" name="role" :value="selectedRole">

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="off"
                               placeholder="nama@smkn1bangsri.sch.id"
                               class="w-full px-3.5 py-2.5 text-xs bg-white text-slate-800 font-medium rounded-xl border border-stone-300 focus:border-stone-400 focus:outline-none focus:ring-0 transition shadow-xs">
                    </div>

                    <!-- Password Input dengan Autocomplete new-password untuk Cegah Auto-Fill Browser -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   required
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full pl-3.5 pr-10 py-2.5 text-xs bg-white text-slate-800 font-medium rounded-xl border border-stone-300 focus:border-stone-400 focus:outline-none focus:ring-0 transition shadow-xs">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                                    aria-label="Toggle password visibility">
                                
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.122-.463c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.274 1.274L3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox Remember Me -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="w-3.5 h-3.5 bg-white border-stone-300 rounded text-orange-500 focus:ring-0 focus:outline-none">
                            <span class="text-xs text-slate-600 font-medium">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Button CTA Login Simpel -->
                    <div class="pt-1">
                        <button type="submit" 
                                class="w-full bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition duration-150 cursor-pointer active:scale-[0.99] flex items-center justify-center gap-2 shadow-sm">
                            <span>Login</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Back Link & Copyright -->
                <div class="text-center pt-3 border-t border-stone-200/80 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/') }}" class="hover:text-orange-600 transition">
                        ← Kembali ke Beranda
                    </a>
                    <span>© {{ date('Y') }} PPLG SMKN 1 Bangsri</span>
                </div>

            </div>

        </div>

    </div>

    <!-- Skrip Paksa Kosongkan Input Kata Sandi Saat Halaman Dimuat -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.value = '';
                setTimeout(() => {
                    passwordInput.value = '';
                }, 100);
            }
        });
    </script>

</body>
</html>
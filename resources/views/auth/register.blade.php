<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Akun Baru - TIECO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        if (window.tailwind) {
            tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    },
                    keyframes: {
                        'fade-in-up': {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                    animation: {
                        'fade-in-up': 'fade-in-up 0.5s ease-out',
                    }
                }
            }
            }
        }
    </script>
</head>
<body class="bg-slate-900 min-h-[100dvh] flex items-center justify-center p-4 antialiased selection:bg-brand-500 selection:text-white relative overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('img/wplogin.jpeg') }}?v={{ time() }}');">
    
    <!-- Dark Overlay for better contrast -->
    <div class="absolute inset-0 bg-black/40 pointer-events-none z-0"></div>

    <!-- Back Button -->
    <a href="/" class="absolute top-6 left-6 sm:top-8 sm:left-8 flex items-center gap-2 text-white bg-black/30 hover:bg-black/50 backdrop-blur-md px-4 py-2 sm:px-5 sm:py-2.5 rounded-full font-semibold transition-all text-xs sm:text-sm z-20 shadow-lg border border-white/10 hover:scale-105 hover:-translate-y-1 duration-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>

    <!-- Container Form -->
    <div class="w-full max-w-[380px] bg-white/95 backdrop-blur-xl rounded-[1.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/20 p-5 sm:p-6 relative z-10 animate-fade-in-up max-h-[90vh] overflow-y-auto no-scrollbar" style="scrollbar-width: none;">
        
        @if($errors->any())
        <div class="mb-3 p-2.5 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[11px] sm:text-xs text-center font-medium animate-fade-in-up">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Header -->
        <div class="text-center mb-4">
            <a href="/" class="inline-flex items-center justify-center w-10 h-10 bg-brand-50 rounded-xl mb-2 sm:mb-3 transition-transform hover:scale-110 hover:rotate-3 duration-300">
                <span class="text-xl">♻️</span>
            </a>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Daftar Akun</h1>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-1">Lengkapi data di bawah untuk bergabung</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-3">
            @csrf
            
            <div class="grid grid-cols-2 gap-3">
                <!-- Nama Lengkap -->
                <div class="space-y-1 group col-span-2">
                    <label for="name" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all placeholder:text-slate-400 hover:bg-slate-100" required>
                </div>

                <!-- Username -->
                <div class="space-y-1 group col-span-1">
                    <label for="username" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="budi123" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all placeholder:text-slate-400 hover:bg-slate-100" required>
                </div>

                <!-- Role Selection -->
                <div class="space-y-1 group col-span-1">
                    <label for="role" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Daftar Sebagai</label>
                    <div class="relative">
                        <select name="role" id="role" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all cursor-pointer hover:bg-slate-100">
                            <option value="warga" {{ old('role') == 'warga' ? 'selected' : '' }}>Warga</option>
                            <option value="umkm" {{ old('role') == 'umkm' ? 'selected' : '' }}>UMKM Mitra</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 sm:px-3 text-slate-500 transition-transform group-hover:translate-y-[1px]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-1 group">
                <label for="email" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="contoh@email.com" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all placeholder:text-slate-400 hover:bg-slate-100" required>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Password -->
                <div class="space-y-1 group col-span-1">
                    <label for="password" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Kata Sandi</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all placeholder:text-slate-400 hover:bg-slate-100" required>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1 group col-span-1">
                    <label for="password_confirmation" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all placeholder:text-slate-400 hover:bg-slate-100" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-brand-500/30 hover:-translate-y-0.5 mt-2">
                Daftar Sekarang
            </button>
        </form>

        <!-- Divider -->
        <div class="relative flex items-center py-3 sm:py-4">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink-0 mx-3 text-slate-400 text-[10px] sm:text-[11px] font-medium">atau daftar dengan</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <!-- Google Register -->
        <a href="{{ route('auth.google') }}" id="googleLoginBtn" class="w-full bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 font-medium rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm transition-all duration-300 flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
        </a>

        <!-- Login Link -->
        <p class="text-center text-[11px] sm:text-xs text-slate-500 mt-4 sm:mt-5">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Masuk di sini</a>
        </p>

    </div>
</body>
</html>

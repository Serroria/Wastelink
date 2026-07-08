<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lupa Sandi - TIECO</title>
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
    <div class="w-full max-w-[360px] bg-white/95 backdrop-blur-xl rounded-[1.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/20 p-5 sm:p-6 relative z-10 animate-fade-in-up">
        
        @if(session('success'))
        <div class="mb-3 p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-[11px] sm:text-xs text-center font-medium animate-fade-in-up">
            {{ session('success') }}
        </div>
        @endif

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
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Lupa Sandi</h1>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-1 leading-relaxed">Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-3">
            @csrf
            
            <!-- Email -->
            <div class="space-y-1 group">
                <label for="email" class="block text-[10px] sm:text-[11px] font-semibold text-slate-700 transition-colors group-focus-within:text-brand-600">Alamat Email</label>
                <input type="email" name="email" id="email" placeholder="contoh@email.com" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all placeholder:text-slate-400 hover:bg-slate-100" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl px-3 py-2 sm:px-3.5 sm:py-2.5 text-xs sm:text-sm transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-brand-500/30 hover:-translate-y-0.5 mt-2">
                Kirim Tautan Reset
            </button>
        </form>

        <!-- Back Link -->
        <p class="text-center text-[11px] sm:text-xs text-slate-500 mt-5">
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors hover:tracking-wide duration-300">← Kembali ke Login</a>
        </p>

    </div>
</body>
</html>

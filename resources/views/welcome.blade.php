<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIECO — Platform Digital Bank Sampah Terintegrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (window.tailwind) {
            tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#059669',
                            600: '#047857',
                            700: '#065f46',
                            900: '#022c22',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                    }
                }
            }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,400;1,700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <style>
        body {
            background-color: #ffffff;
            color: #0f172a;
            overflow-x: hidden;
        }

        .hero-wave {
            background: radial-gradient(circle at top right, #a7f3d0 0%, #ecfdf5 45%, #ffffff 80%);
        }

        /* Floating animations */
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-12px) rotate(3deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        @keyframes float-reverse {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(10px) rotate(-3deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-slow {
            animation: float-reverse 8s ease-in-out infinite;
        }

        .animate-float-leaf {
            animation: float 4s ease-in-out infinite;
        }

        .nav-item {
            position: relative;
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .nav-item:hover {
            color: #047857;
            background-color: rgba(15, 23, 42, 0.05);
        }

        .nav-item.active {
            color: #047857;
        }

        #nav-indicator {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Scroll Reveal Animation Styles */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.25, 1, 0.5, 1), transform 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Mouse parallax decoration items transition */
        .parallax-item {
            transition: transform 0.15s cubic-bezier(0.25, 1, 0.5, 1);
        }
    </style>
</head>

<body class="antialiased">

    {{-- ===== HEADER ===== --}}
    <header
        class="sticky top-0 w-full bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm px-4 lg:px-8 py-4 flex items-center justify-between z-50">
        {{-- Logo (tanpa animasi) --}}
        <a href="#" class="flex items-center gap-2 group">
            <span class="text-2xl group-hover:rotate-12 transition-transform duration-300">♻️</span>
            <span class="text-xl font-extrabold tracking-tight text-emerald-700">TIECO</span>
        </a>

        {{-- Center Navigation --}}
        <nav class="hidden md:flex items-center gap-1 relative py-2" id="nav-container">
            {{-- Sliding active tab underline --}}
            <div id="nav-indicator"
                class="absolute bottom-0 left-0 h-[2.5px] bg-emerald-700 rounded-full transition-all pointer-events-none z-0 opacity-0">
            </div>

            <a href="#beranda" class="nav-item active px-4 py-2 rounded-lg relative z-10">Beranda</a>
            <a href="#kategori" class="nav-item px-4 py-2 rounded-lg relative z-10">Kategori Sampah</a>
            <a href="#pilah" class="nav-item px-4 py-2 rounded-lg relative z-10">Pilah Rumah</a>
            <a href="#keunggulan" class="nav-item px-4 py-2 rounded-lg relative z-10">Keunggulan</a>
            <a href="#dampak" class="nav-item px-4 py-2 rounded-lg relative z-10">Dampak</a>
        </nav>

        {{-- Right Side: Actions --}}
        <div class="flex items-center gap-2">
            @auth
                @php
                    $dashboardRoute = match (auth()->user()->role) {
                        'warga' => route('warga.dashboard'),
                        'bank_sampah' => route('bank-sampah.dashboard'),
                        'umkm' => route('umkm.dashboard'),
                        'pembeli' => route('pembeli.dashboard'),
                        default => '#',
                    };
                @endphp
                <a href="{{ $dashboardRoute }}"
                    class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-bold rounded-full text-xs transition-all shadow-md hover:scale-105 active:scale-95 transform">
                    Ke Dasbor
                </a>
                <form action="{{ route('demo.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 border-2 border-red-600 text-red-600 hover:text-white hover:bg-red-600 font-bold rounded-full text-xs transition-all hover:scale-105 active:scale-95 transform shadow-sm">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="px-4 py-2 border-2 border-emerald-800 text-emerald-800 hover:text-white hover:bg-emerald-800 font-bold rounded-full text-xs transition-all hover:scale-105 active:scale-95 transform shadow-sm">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                    class="px-4 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-bold rounded-full text-xs transition-all shadow-md shadow-emerald-800/30 hover:shadow-emerald-900/40 hover:scale-105 active:scale-95 transform">
                    Daftar
                </a>
            @endauth
        </div>
    </header>

    {{-- ===== SECTION 1: HERO (Ponchiki Bez Zamorozki Style) ===== --}}
    <section id="beranda"
        class="hero-wave min-h-[85vh] flex items-center relative overflow-hidden px-4 lg:px-8 pt-10 pb-20">

        <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {{-- Left Content --}}
            <div class="lg:col-span-6 space-y-8 text-left">

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-none">
                    Pilah Sampah,<br>
                    <span class="font-serif italic font-normal text-emerald-700">Tabung Kebaikan</span>
                </h1>

                <p class="text-slate-600 text-sm md:text-base max-w-lg leading-relaxed">
                    Setiap botol plastik, kardus bekas, dan kaleng minuman di rumah Anda punya nilai jual.
                    Kumpulkan, timbang di bank sampah terdekat, lalu terima poin yang bisa langsung dipakai
                    untuk bayar listrik, beli pulsa, atau belanja sembako di warung mitra.
                </p>

                <div class="flex flex-wrap gap-3 items-center">
                    <a href="{{ route('register') }}"
                        class="px-8 py-3.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-full text-xs transition-all shadow-lg shadow-emerald-700/30 hover:shadow-emerald-600/40 hover:scale-105 transform">
                        Mulai Setor Sampah
                    </a>
                </div>

                {{-- Daftar Sebagai Role --}}
                <div class="pt-6 space-y-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Daftar Sebagai:</span>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('register') }}"
                            class="group flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 rounded-full text-xs font-semibold text-slate-600 hover:text-emerald-700 transition-all">
                            <span
                                class="w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-[10px] group-hover:scale-110 transition-transform">👤</span>
                            Nasabah
                        </a>
                        <a href="{{ route('register') }}"
                            class="group flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-sky-300 hover:bg-sky-50 rounded-full text-xs font-semibold text-slate-600 hover:text-sky-700 transition-all">
                            <span
                                class="w-5 h-5 bg-sky-100 text-sky-700 rounded-full flex items-center justify-center text-[10px] group-hover:scale-110 transition-transform">🏦</span>
                            Operator Bank Sampah
                        </a>
                        <a href="{{ route('register') }}"
                            class="group flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-amber-300 hover:bg-amber-50 rounded-full text-xs font-semibold text-slate-600 hover:text-amber-700 transition-all">
                            <span
                                class="w-5 h-5 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-[10px] group-hover:scale-110 transition-transform">🏪</span>
                            UMKM Mitra
                        </a>
                        <a href="{{ route('register') }}"
                            class="group flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-purple-300 hover:bg-purple-50 rounded-full text-xs font-semibold text-slate-600 hover:text-purple-700 transition-all">
                            <span
                                class="w-5 h-5 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center text-[10px] group-hover:scale-110 transition-transform">🏭</span>
                            Pembeli Industri
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Graphic --}}
            <div class="lg:col-span-6 flex justify-center items-center relative">
                <div
                    class="w-80 h-80 md:w-96 md:h-96 rounded-full bg-gradient-to-br from-emerald-100/50 to-teal-50/50 absolute -z-10 blur-3xl opacity-80">
                </div>

                {{-- Main circular graphic frame --}}
                <div
                    class="w-72 h-72 md:w-80 md:h-80 rounded-full border-[10px] border-emerald-50 bg-white/80 shadow-2xl flex items-center justify-center animate-float relative overflow-hidden">
                    <span class="text-[120px] md:text-[140px] select-none text-emerald-600">♻️</span>
                </div>

                {{-- Floating Price Tag --}}
                <div
                    class="absolute bottom-6 right-6 md:right-12 bg-white border border-emerald-100 shadow-xl rounded-2xl p-4 animate-float-slow max-w-[180px]">
                    <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Kurs Tabungan Hari Ini
                    </div>
                    <div class="text-xs font-black text-slate-800 mt-1">1 kg Plastik PET</div>
                    <div class="text-xs font-extrabold text-emerald-600 mt-0.5">≈ 1.000 Poin (Rp 10.000)</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SECTION 2: CATEGORY — Interactive Carousel Slider ===== --}}
    <section id="kategori" class="py-24 w-full text-center space-y-12 overflow-hidden">
        <div class="max-w-xl mx-auto space-y-4 px-4">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">
                PILIH JENIS <span class="font-serif italic font-normal text-emerald-600">Sampahmu</span>
            </h2>
            <p class="text-slate-400 text-xs">Geser kartu untuk menjelajahi jenis sampah yang bisa ditukar menjadi poin
                tabungan.</p>
        </div>

        {{-- Carousel Container --}}
        <div class="relative w-full" id="carouselWrapper">
            {{-- Prev / Next Arrows --}}
            <button onclick="carouselPrev()"
                class="absolute left-2 lg:left-8 top-1/2 -translate-y-1/2 z-30 w-10 h-10 bg-white/90 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 rounded-full flex items-center justify-center text-slate-500 hover:text-emerald-600 shadow-lg transition-all hover:scale-110"
                aria-label="Previous">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button onclick="carouselNext()"
                class="absolute right-2 lg:right-8 top-1/2 -translate-y-1/2 z-30 w-10 h-10 bg-white/90 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 rounded-full flex items-center justify-center text-slate-500 hover:text-emerald-600 shadow-lg transition-all hover:scale-110"
                aria-label="Next">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Track --}}
            <div class="flex items-center justify-center w-full py-8" style="perspective: 1200px;" id="carouselTrack">

                {{-- Card 0: Plastik --}}
                <div class="carousel-card" data-index="0">
                    <div class="card-inner flex flex-col items-center">
                        <div class="card-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                            <span class="text-5xl">🥤</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mt-5">Plastik PET & HDPE</h3>
                        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed px-2">Botol air mineral, gelas cup,
                            botol detergen, wadah shampoo, dan jeriken bersih.</p>
                        <div class="card-poin">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kurs
                                Tabungan</span>
                            <div class="text-base font-black text-emerald-600 mt-0.5">1.000 <span
                                    class="text-xs font-semibold">Poin/kg</span></div>
                        </div>
                        <div class="card-examples">
                            <span>Botol 600ml</span><span>Gelas Cup</span><span>Jeriken</span>
                        </div>
                    </div>
                </div>

                {{-- Card 1: Kertas --}}
                <div class="carousel-card" data-index="1">
                    <div class="card-inner flex flex-col items-center">
                        <div class="card-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                            <span class="text-5xl">📦</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mt-5">Kertas & Karton</h3>
                        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed px-2">Kardus packing, kertas koran,
                            HVS bekas, buku tulis lama, dan majalah kering.</p>
                        <div class="card-poin">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kurs
                                Tabungan</span>
                            <div class="text-base font-black text-emerald-600 mt-0.5">400 <span
                                    class="text-xs font-semibold">Poin/kg</span></div>
                        </div>
                        <div class="card-examples">
                            <span>Kardus</span><span>Koran</span><span>Buku Bekas</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Logam --}}
                <div class="carousel-card" data-index="2">
                    <div class="card-inner flex flex-col items-center">
                        <div class="card-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
                            <span class="text-5xl">🥫</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mt-5">Logam & Kaleng</h3>
                        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed px-2">Kaleng alumunium, besi tua,
                            kawat, kaleng biskuit, tutup botol logam, dan panci rusak.</p>
                        <div class="card-poin">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kurs
                                Tabungan</span>
                            <div class="text-base font-black text-emerald-600 mt-0.5">1.500 <span
                                    class="text-xs font-semibold">Poin/kg</span></div>
                        </div>
                        <div class="card-examples">
                            <span>Kaleng</span><span>Besi</span><span>Alumunium</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Minyak Jelantah --}}
                <div class="carousel-card" data-index="3">
                    <div class="card-inner flex flex-col items-center">
                        <div class="card-icon" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8);">
                            <span class="text-5xl">🛢️</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mt-5">Minyak Jelantah</h3>
                        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed px-2">Minyak goreng bekas rumah tangga
                            yang sudah disaring dan bersih dari sisa makanan.</p>
                        <div class="card-poin">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kurs
                                Tabungan</span>
                            <div class="text-base font-black text-emerald-600 mt-0.5">800 <span
                                    class="text-xs font-semibold">Poin/liter</span></div>
                        </div>
                        <div class="card-examples">
                            <span>Minyak Goreng</span><span>Minyak Kelapa</span>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Kaca --}}
                <div class="carousel-card" data-index="4">
                    <div class="card-inner flex flex-col items-center">
                        <div class="card-icon" style="background: linear-gradient(135deg, #ccfbf1, #99f6e4);">
                            <span class="text-5xl">🫙</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mt-5">Kaca & Botol</h3>
                        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed px-2">Botol kaca minuman, toples bekas
                            selai, cermin pecah, dan pecahan kaca bersih.</p>
                        <div class="card-poin">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kurs
                                Tabungan</span>
                            <div class="text-base font-black text-emerald-600 mt-0.5">300 <span
                                    class="text-xs font-semibold">Poin/kg</span></div>
                        </div>
                        <div class="card-examples">
                            <span>Botol Kecap</span><span>Toples</span><span>Cermin</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dot Indicators --}}
            <div class="flex items-center justify-center gap-2 mt-4" id="carouselDots"></div>
        </div>
    </section>

    {{-- Carousel Styles --}}
    <style>
        .carousel-card {
            position: absolute;
            width: 280px;
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: grab;
            user-select: none;
        }

        .carousel-card:active {
            cursor: grabbing;
        }

        .carousel-card .card-inner {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .carousel-card.active .card-inner {
            border-color: #a7f3d0;
            box-shadow: 0 20px 50px rgba(16, 185, 129, 0.12), 0 8px 20px rgba(0, 0, 0, 0.06);
            transform: scale(1);
        }

        .carousel-card .card-icon {
            width: 90px;
            height: 90px;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.5s ease;
        }

        .carousel-card.active .card-icon {
            width: 100px;
            height: 100px;
            border-radius: 1.5rem;
        }

        .carousel-card .card-poin {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        .carousel-card .card-examples {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.35rem;
            margin-top: 0.75rem;
        }

        .carousel-card .card-examples span {
            font-size: 9px;
            font-weight: 600;
            color: #94a3b8;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .carousel-card.active .card-examples span {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .carousel-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #cbd5e1;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-dot.active {
            width: 28px;
            background: #10b981;
        }
    </style>

    {{-- ===== SECTION 3: PILAH RUMAH ===== --}}
    <section id="pilah" class="py-20 bg-slate-50/50 overflow-hidden reveal">
        <div class="w-full px-4 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {{-- Left Content --}}
            <div class="lg:col-span-6 space-y-6 text-left">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-tight">
                    Ga Perlu Ribet,<br>
                    <span class="font-serif italic font-normal text-emerald-700">Tinggal Pisahin Aja</span>
                </h2>
                <p class="text-slate-600 text-sm leading-relaxed max-w-md">
                    Sediakan 3 wadah di rumah: satu untuk plastik, satu untuk kertas, satu untuk kaleng.
                    Pas petugas datang jemput, sampah Anda langsung ditimbang tanpa perlu antri sortir di TPS.
                </p>
                <a href="{{ route('register') }}"
                    class="inline-block px-8 py-3.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-full text-xs transition-all shadow-md shadow-emerald-700/20 hover:scale-105 transform">
                    Coba Sekarang
                </a>
            </div>

            {{-- Right Circular Frame Graphic --}}
            <div class="lg:col-span-6 flex justify-center items-center relative">
                <div
                    class="w-80 h-80 rounded-full border-[10px] border-emerald-100/50 bg-white shadow-2xl flex items-center justify-center overflow-hidden animate-float relative">
                    <span class="text-8xl select-none">📦</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SECTION 4: KEUNGGULAN ===== --}}
    <section id="keunggulan" class="py-24 w-full px-4 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-16 reveal">
        {{-- Left Narrative --}}
        <div class="lg:col-span-6 space-y-6 text-left">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Kenapa Harus Lewat TIECO?</h2>
            <p class="text-slate-600 text-sm leading-relaxed">
                Di Indonesia, 14.900 unit bank sampah tersebar di seluruh kelurahan, tapi mayoritas masih pakai
                pencatatan buku tulis. Warga tidak tahu berapa saldo poinnya, operator kewalahan menghitung manual,
                dan industri daur ulang kesulitan menemukan pemasok sampah terpilah dalam volume besar.
            </p>
            <p class="text-slate-600 text-sm leading-relaxed">
                TIECO mendigitalisasi seluruh rantai ini. Poin Anda bukan angka fiktif, nilainya ditopang langsung
                dari hasil penjualan sampah terpilah ke pabrik daur ulang rekanan kami.
            </p>
        </div>

        {{-- Right Features List --}}
        <div class="lg:col-span-6 space-y-6">
            <div
                class="flex items-start gap-4 p-5 bg-white border border-slate-100 rounded-2xl hover:shadow-lg hover:border-emerald-200 transition-all">
                <span class="text-2xl p-3 bg-emerald-100 text-emerald-700 rounded-xl">🚚</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Dijemput ke Rumah, Gratis</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Petugas kelurahan datang setiap hari Sabtu
                        pagi. Anda tinggal taruh kotak di depan pagar — tidak perlu repot bawa ke TPS sendiri.</p>
                </div>
            </div>

            <div
                class="flex items-start gap-4 p-5 bg-white border border-slate-100 rounded-2xl hover:shadow-lg hover:border-emerald-200 transition-all">
                <span class="text-2xl p-3 bg-emerald-100 text-emerald-700 rounded-xl">⚖️</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Ditimbang Transparan, Ada Buktinya</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Setiap jenis sampah ditimbang terpisah oleh
                        operator. Berat dan foto bukti timbangan langsung muncul di dasbor Anda — tidak ada
                        tebak-tebakan.</p>
                </div>
            </div>

            <div
                class="flex items-start gap-4 p-5 bg-white border border-slate-100 rounded-2xl hover:shadow-lg hover:border-emerald-200 transition-all">
                <span class="text-2xl p-3 bg-emerald-100 text-emerald-700 rounded-xl">📱</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Poin Langsung Bisa Dipakai</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Bayar token listrik, isi pulsa, top-up
                        e-wallet, atau tukar sembako di warung mitra — semua langsung dari saldo poin Anda.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SECTION 5: DAMPAK SOSIAL ===== --}}
    <section id="dampak" class="py-20 bg-emerald-50/20 overflow-hidden reveal">
        <div class="w-full px-4 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {{-- Left Circular Graphic --}}
            <div class="lg:col-span-6 flex justify-center items-center relative">
                <div
                    class="w-80 h-80 rounded-full border-[10px] border-emerald-100 bg-white shadow-2xl flex items-center justify-center overflow-hidden animate-float relative">
                    <span class="text-8xl select-none">👩‍👩‍👧</span>
                    {{-- Decorative clean icons --}}
                    <span class="absolute top-10 right-10 text-2xl animate-pulse">🌍</span>
                    <span class="absolute bottom-10 left-10 text-3xl">🍃</span>
                </div>
            </div>

            {{-- Right Content --}}
            <div class="lg:col-span-6 space-y-6 text-left">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-tight">
                    Bukan Cuma Soal Sampah,<br>
                    <span class="font-serif italic font-normal text-emerald-700">Ini Soal Tetangga Kita</span>
                </h2>
                <p class="text-slate-600 text-sm leading-relaxed max-w-md">
                    1 kg plastik yang Anda setor = 1 keluarga kurang mampu dapat voucher sembako gratis.
                    Seluruh keuntungan penjualan B2B sampah dialokasikan untuk subsidi pangan warga prasejahtera
                    dan beasiswa anak-anak di kelurahan.
                </p>
                <a href="{{ auth()->check() ? route('dampak.realtime') : route('login', ['redirect' => route('dampak.realtime', absolute: false)]) }}"
                    class="inline-block px-8 py-3.5 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-full text-xs transition-all shadow-md shadow-emerald-700/20 hover:scale-105 transform">
                    Lihat Laporan Dampak
                </a>
            </div>
        </div>
    </section>

    {{-- ===== SECTION 6: TRANSIT & LOGISTIK ===== --}}
    <section class="py-24 w-full px-4 lg:px-8 text-center space-y-12 reveal">
        <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Dari Rumah Anda ke Pabrik Daur Ulang
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-left">
            <div class="space-y-4 p-6 bg-white border border-slate-100 rounded-2xl flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="text-2xl">🚛</span>
                        TPS Transit Kelurahan
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed mt-2">
                        Sampah dari rumah-rumah warga dikumpulkan di gudang TPS kelurahan.
                        Di sini sampah dipadatkan dengan mesin press, dibungkus rapi dalam bal-bal besar,
                        lalu dijual langsung ke pabrik daur ulang rekanan.
                    </p>
                </div>
            </div>

            <div class="space-y-4 p-6 bg-white border border-slate-100 rounded-2xl flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="text-2xl">💸</span>
                        Uang Masuk, Poin Aman
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed mt-2">
                        Poin tabungan Anda bukan janji kosong. Setiap poin dijamin oleh kas nyata hasil penjualan sampah
                        ke industri. Pencairan voucher UMKM mitra dibayarkan setiap minggu setelah rekonsiliasi selesai.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SECTION 7: PENUTUP — Stats Dashboard & Interactive Call-to-Action ===== --}}
    <section class="py-24 bg-slate-50/50 overflow-hidden px-4 lg:px-8 reveal">
        <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            {{-- Left Content --}}
            <div class="lg:col-span-5 space-y-8 text-left">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                    Satu Langkah Kecil,<br>
                    <span class="font-serif italic font-normal text-emerald-700">Dampak Nyata untuk RT Kita</span>
                </h2>
                <p class="text-slate-600 text-sm leading-relaxed max-w-md">
                    Bergabunglah bersama ribuan kepala keluarga lainnya. TIECO membantu Anda menyalurkan sampah
                    secara produktif sekaligus berkontribusi langsung pada jaring pengaman sosial kelurahan.
                </p>
                <a href="{{ route('register') }}"
                    class="inline-block px-8 py-4 bg-emerald-700 hover:bg-emerald-600 text-white font-bold rounded-full text-xs transition-all shadow-lg shadow-emerald-700/25 hover:shadow-emerald-600/35 hover:scale-105 transform">
                    Gabung Sekarang
                </a>
            </div>

            {{-- Right Content: Visual Animated Stats Dashboard Grid --}}
            <div class="lg:col-span-7 grid grid-cols-2 gap-6 relative text-emerald-700">
                {{-- Decorative absolute leaf backdrops --}}
                <span class="absolute -top-10 -left-10 text-3xl opacity-25 animate-float-leaf parallax-item"
                    data-depth="0.1">🍃</span>
                <span class="absolute -bottom-8 -right-8 text-4xl opacity-15 animate-float-slow parallax-item"
                    data-depth="0.25">🍁</span>

                {{-- Stat Card 1 --}}
                <div
                    class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <span class="text-3xl mb-4">👥</span>
                    <div>
                        <div class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight"><span
                                class="stat-count" data-target="{{ $totalWarga ?: 0 }}">0</span>+</div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Kepala Keluarga</p>
                    </div>
                </div>

                {{-- Stat Card 2 --}}
                <div
                    class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <span class="text-3xl mb-4">🚛</span>
                    <div>
                        <div class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                            <span class="stat-count" data-target="{{ $totalWeight >= 1000 ? round($totalWeight / 1000) : round($totalWeight) }}">0</span> 
                            {{ $totalWeight >= 1000 ? 'Ton' : 'kg' }}
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Sampah Terdaur Ulang
                        </p>
                    </div>
                </div>

                {{-- Stat Card 3 --}}
                <div
                    class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <span class="text-3xl mb-4">🏪</span>
                    <div>
                        <div class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight"><span
                                class="stat-count" data-target="{{ $totalUmkm ?: 0 }}">0</span>+</div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Warung UMKM Rekanan
                        </p>
                    </div>
                </div>

                {{-- Stat Card 4 --}}
                <div
                    class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <span class="text-3xl mb-4">🎫</span>
                    <div>
                        <div class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight"><span
                                class="stat-count" data-target="{{ $totalVouchers ?: 0 }}">0</span>+</div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Voucher Sembako
                            Disalurkan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ===== FOOTER ===== --}}
    <footer class="bg-emerald-800 border-t border-emerald-700 py-16 px-4 lg:px-8">
        <div class="w-full grid grid-cols-1 md:grid-cols-12 gap-12 text-emerald-50">
            {{-- Left Tautan --}}
            <div class="md:col-span-4 space-y-4">
                <a href="#" class="flex items-center gap-2">
                    <span class="text-xl">♻️</span>
                    <span class="text-lg font-extrabold tracking-tight text-white">TIECO</span>
                </a>
            </div>

            {{-- Center Newsletter --}}
            <div class="md:col-span-4 space-y-4">
                <h4 class="text-xs font-bold text-white uppercase tracking-widest">Langganan Info Terbaru</h4>
                <div class="flex gap-2">
                    <input type="email"
                        class="flex-1 px-4 py-2.5 border border-emerald-800 rounded-xl text-xs focus:outline-none focus:border-emerald-400 bg-emerald-900 text-white placeholder-emerald-500"
                        placeholder="Masukkan email Anda...">
                    <button
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition-all shadow-sm">Kirim</button>
                </div>
            </div>

            {{-- Right Contacts + Social Icons --}}
            <div class="md:col-span-4 space-y-4 text-xs">
                <h4 class="text-xs font-bold text-white uppercase tracking-widest">Hubungi Kami</h4>
                <p class="text-emerald-300">📍 TPS Pengolahan Utama Kel. Cempaka Putih Timur, Jakarta Pusat</p>
                <div class="flex flex-col gap-2">
                    <a href="tel:081234567890" class="font-bold text-white hover:text-emerald-300">📞 +62
                        858-8103-8357</a>
                    <a href="mailto:info@tieco.id" class="font-bold text-white hover:text-emerald-300">✉️
                        info@tieco.id</a>
                </div>

                {{-- Social Icons --}}
                <div class="flex items-center gap-3 pt-3">
                    <a href="#"
                        class="flex w-8 h-8 items-center justify-center rounded-full border border-emerald-800 text-emerald-300 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 hover:scale-110 hover:rotate-6 transition-all"
                        title="Instagram">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="flex w-8 h-8 items-center justify-center rounded-full border border-emerald-800 text-emerald-300 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 hover:scale-110 hover:-rotate-6 transition-all"
                        title="Twitter / X">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="flex w-8 h-8 items-center justify-center rounded-full border border-emerald-800 text-emerald-300 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 hover:scale-110 hover:rotate-6 transition-all"
                        title="WhatsApp">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="w-full border-t border-emerald-900 mt-12 pt-8 text-center text-xs text-emerald-400">
            TIECO © 2026 — Sistem Bank Sampah Digital Kelurahan | PEKAN IT 2026
        </div>
    </footer>

    {{-- ===== ANIMASI INTERAKTIF DAUN BERJALAN (JavaScript) ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const leaf = document.getElementById('nav-leaf');
            const navItems = document.querySelectorAll('.nav-item');
            const container = document.getElementById('nav-container');

            function moveLeaf(target) {
                const rect = target.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();

                // Calculate position relative to container
                const left = rect.left - containerRect.left + (rect.width / 2);
                const top = rect.bottom - containerRect.top + 2;

                leaf.style.left = left + 'px';
                leaf.style.top = top + 'px';
                leaf.style.opacity = '1';

                // Random natural rotation & scale
                const rotation = (Math.random() - 0.5) * 30;
                leaf.style.transform = `translateX(-50%) rotate(${rotation}deg) scale(1.15)`;
            }

            navItems.forEach(item => {
                item.addEventListener('mouseenter', () => moveLeaf(item));

                item.addEventListener('click', function (e) {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    moveLeaf(this);
                });
            });

            container.addEventListener('mouseleave', () => {
                const active = document.querySelector('.nav-item.active');
                if (active) {
                    moveLeaf(active);
                } else {
                    leaf.style.opacity = '0';
                    leaf.style.transform = 'scale(0.5)';
                }
            });

            // Set initial position
            const active = document.querySelector('.nav-item.active');
            if (active) {
                setTimeout(() => moveLeaf(active), 300);
            }
        });
    </script>

    {{-- ===== CAROUSEL ENGINE ===== --}}
    <script>
        (function () {
            const cards = document.querySelectorAll('.carousel-card');
            const dotsContainer = document.getElementById('carouselDots');
            const track = document.getElementById('carouselTrack');
            const totalCards = cards.length;
            let currentIndex = 0;
            let autoplayTimer = null;
            let isDragging = false;
            let startX = 0;
            let dragThreshold = 50;

            // Set track height to accommodate cards
            track.style.position = 'relative';
            track.style.height = '460px';

            // Create dot indicators
            for (let i = 0; i < totalCards; i++) {
                const dot = document.createElement('button');
                dot.className = 'carousel-dot';
                dot.setAttribute('aria-label', 'Slide ' + (i + 1));
                dot.addEventListener('click', () => goTo(i));
                dotsContainer.appendChild(dot);
            }

            // Click on any card to select it
            cards.forEach((card) => {
                card.addEventListener('click', () => {
                    const idx = parseInt(card.getAttribute('data-index'));
                    if (idx !== currentIndex) {
                        goTo(idx);
                    }
                });
            });

            function positionCards() {
                const trackWidth = track.offsetWidth;
                const centerX = trackWidth / 2;

                cards.forEach((card, i) => {
                    let offset = i - currentIndex;

                    // Wrap around for infinite feel
                    if (offset > Math.floor(totalCards / 2)) offset -= totalCards;
                    if (offset < -Math.floor(totalCards / 2)) offset += totalCards;

                    const absOffset = Math.abs(offset);
                    const spacing = Math.min(trackWidth * 0.22, 320);
                    const translateX = offset * spacing;
                    const scale = offset === 0 ? 1 : Math.max(0.7 - absOffset * 0.08, 0.55);
                    const opacity = offset === 0 ? 1 : Math.max(0.65 - absOffset * 0.08, 0.35);
                    const zIndex = 20 - absOffset;
                    const rotateY = offset * -5;
                    const blur = offset === 0 ? 0 : Math.min(absOffset * 0.5, 1.5);

                    card.style.left = `${centerX - 140 + translateX}px`;
                    card.style.transform = `scale(${scale}) rotateY(${rotateY}deg)`;
                    card.style.opacity = opacity;
                    card.style.zIndex = zIndex;
                    card.style.filter = blur > 0 ? `blur(${blur}px)` : 'none';
                    card.style.pointerEvents = 'auto';
                    card.style.cursor = offset === 0 ? 'default' : 'pointer';

                    if (offset === 0) {
                        card.classList.add('active');
                    } else {
                        card.classList.remove('active');
                    }
                });

                // Update dots
                const dots = dotsContainer.querySelectorAll('.carousel-dot');
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === currentIndex);
                });
            }

            function goTo(index) {
                currentIndex = ((index % totalCards) + totalCards) % totalCards;
                positionCards();
                resetAutoplay();
            }

            window.carouselNext = function () { goTo(currentIndex + 1); };
            window.carouselPrev = function () { goTo(currentIndex - 1); };

            function resetAutoplay() {
                clearInterval(autoplayTimer);
                autoplayTimer = setInterval(() => goTo(currentIndex + 1), 4000);
            }

            // Drag / Swipe support
            track.addEventListener('mousedown', (e) => { isDragging = true; startX = e.clientX; });
            track.addEventListener('touchstart', (e) => { isDragging = true; startX = e.touches[0].clientX; }, { passive: true });

            document.addEventListener('mouseup', (e) => {
                if (!isDragging) return;
                isDragging = false;
                const diff = e.clientX - startX;
                if (Math.abs(diff) > dragThreshold) {
                    diff > 0 ? carouselPrev() : carouselNext();
                }
            });
            document.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                isDragging = false;
                const diff = (e.changedTouches[0]?.clientX || 0) - startX;
                if (Math.abs(diff) > dragThreshold) {
                    diff > 0 ? carouselPrev() : carouselNext();
                }
            });

            // Pause autoplay on hover
            track.addEventListener('mouseenter', () => clearInterval(autoplayTimer));
            track.addEventListener('mouseleave', () => resetAutoplay());

            // Init
            positionCards();
            resetAutoplay();

            // Reposition on resize
            window.addEventListener('resize', positionCards);
        })();
    </script>

    {{-- ===== SCROLL REVEAL & INTERACTIVE COUNTERS ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Scroll Reveal Intersection Observer
            const revealElements = document.querySelectorAll('.reveal');

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Trigger stats counter if this is the stats section
                        if (entry.target.contains(document.querySelector('.stat-count'))) {
                            startCounters();
                        }
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            });

            revealElements.forEach(el => revealObserver.observe(el));

            // 2. Count-Up Stats Animation
            let countersStarted = false;
            function startCounters() {
                if (countersStarted) return;
                countersStarted = true;

                const counts = document.querySelectorAll('.stat-count');
                counts.forEach(count => {
                    const target = parseInt(count.getAttribute('data-target'));
                    const duration = 2000; // 2 seconds
                    const startTime = performance.now();

                    function updateCount(currentTime) {
                        const elapsedTime = currentTime - startTime;
                        const progress = Math.min(elapsedTime / duration, 1);

                        // EaseOutQuad function for natural decelerating count
                        const easeProgress = progress * (2 - progress);
                        const currentVal = Math.floor(easeProgress * target);

                        // Format numbers over 1000 with dots (e.g. 2400 -> 2.400)
                        if (currentVal >= 1000) {
                            count.textContent = currentVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        } else {
                            count.textContent = currentVal;
                        }

                        if (progress < 1) {
                            requestAnimationFrame(updateCount);
                        } else {
                            // Ensure precise end value
                            if (target >= 1000) {
                                count.textContent = target.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                count.textContent = target;
                            }
                        }
                    }

                    requestAnimationFrame(updateCount);
                });
            }

            // 3. Mouse Parallax for Floating Decor
            const parallaxItems = document.querySelectorAll('.parallax-item');
            document.addEventListener('mousemove', (e) => {
                const mouseX = e.clientX / window.innerWidth - 0.5;
                const mouseY = e.clientY / window.innerHeight - 0.5;

                parallaxItems.forEach(item => {
                    const depth = parseFloat(item.getAttribute('data-depth')) || 0.2;
                    const moveX = mouseX * depth * 80;
                    const moveY = mouseY * depth * 80;
                    item.style.transform = `translate(${moveX}px, ${moveY}px)`;
                });
            });

            // 4. Cursor Leaf Particle Trail Effect on Whole Body
            let lastSpawn = 0;
            document.body.addEventListener('mousemove', (e) => {
                const now = Date.now();
                if (now - lastSpawn < 60) return; // limit rate
                lastSpawn = now;

                const leafEmoji = ['🍃', '🍁', '🍂', '🌱'][Math.floor(Math.random() * 4)];
                const span = document.createElement('span');
                span.textContent = leafEmoji;
                span.style.position = 'absolute';
                span.style.left = `${e.pageX}px`;
                span.style.top = `${e.pageY}px`;
                span.style.pointerEvents = 'none';
                span.style.fontSize = `${Math.random() * 12 + 12}px`;
                span.style.opacity = '0.7';
                span.style.transition = 'all 1.2s ease-out';
                span.style.transform = 'translate(-50%, -50%) rotate(0deg)';
                span.style.zIndex = '9999'; // ensure it's on top of everything

                document.body.appendChild(span);

                setTimeout(() => {
                    span.style.transform = `translate(-50%, -150%) rotate(${Math.random() * 360 - 180}deg) scale(0.3)`;
                    span.style.opacity = '0';
                }, 50);

                setTimeout(() => {
                    span.remove();
                }, 1250);
            });

            // 5. Dynamic Capsule Underline Navigation Indicator & ScrollSpy
            const navContainer = document.getElementById('nav-container');
            const navIndicator = document.getElementById('nav-indicator');
            const navItems = document.querySelectorAll('.nav-item');
            const sections = document.querySelectorAll('section[id]');

            function updateNavIndicator(activeItem) {
                if (!activeItem || !navIndicator || !navContainer) return;
                const rect = activeItem.getBoundingClientRect();
                const containerRect = navContainer.getBoundingClientRect();

                // Align indicator to item text bounds
                navIndicator.style.left = `${rect.left - containerRect.left}px`;
                navIndicator.style.width = `${rect.width}px`;
                navIndicator.style.opacity = '1';
            }

            function scrollSpy() {
                let currentSectionId = 'beranda';
                const scrollPosition = window.scrollY + 140;

                sections.forEach(section => {
                    const top = section.offsetTop;
                    const height = section.offsetHeight;
                    if (scrollPosition >= top && scrollPosition < top + height) {
                        currentSectionId = section.getAttribute('id');
                    }
                });

                navItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('href') === `#${currentSectionId}`) {
                        item.classList.add('active');
                        updateNavIndicator(item);
                    }
                });
            }

            // Bind events for hover and scroll
            navItems.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    updateNavIndicator(item);
                });
                item.addEventListener('mouseleave', () => {
                    const active = document.querySelector('.nav-item.active');
                    if (active) updateNavIndicator(active);
                });
            });

            window.addEventListener('scroll', scrollSpy);
            window.addEventListener('resize', () => {
                const active = document.querySelector('.nav-item.active');
                if (active) updateNavIndicator(active);
            });

            // Initial check
            setTimeout(() => {
                const active = document.querySelector('.nav-item.active');
                if (active) updateNavIndicator(active);
            }, 250);
        });
    </script>


</body>

</html>

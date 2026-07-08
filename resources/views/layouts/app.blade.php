<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TIECO - Platform Digital Bank Sampah Terintegrasi.">
    <title>@yield('title', 'TIECO - Bank Sampah Digital')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (window.tailwind) {
            tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fbf4',
                            100: '#dcf7e7',
                            200: '#bbf0ce',
                            500: '#00b14f',
                            600: '#009440',
                            700: '#007532',
                        },
                        emerald: {
                            50: '#f0fbf4',
                            100: '#dcf7e7',
                            200: '#bbf0ce',
                            300: '#86e2aa',
                            400: '#47cf7e',
                            500: '#00b14f', // Primary Grab Green
                            600: '#009440',
                            700: '#007532',
                            800: '#055c29',
                            900: '#064c24',
                            950: '#022b14',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }
        
        /* Sidebar transition */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, padding 0.3s ease;
        }
        #sidebar.collapsed {
            width: 0 !important;
            min-width: 0 !important;
            opacity: 0;
            overflow: hidden;
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    @if(auth()->check() && ! request()->routeIs('home'))
        @php 
            $currentRole = auth()->user()->role; 
            $initials = auth()->user()->initials();
            $profilePhotoUrl = auth()->user()->profilePhotoUrl();
        @endphp
        
        {{-- SIDEBAR --}}
        <aside id="sidebar" class="w-full md:w-64 bg-white border-r border-slate-100 flex flex-col justify-between shrink-0 h-screen sticky top-0">
            <div class="p-6 overflow-y-auto">
                <div class="flex items-center gap-2 mb-8 whitespace-nowrap select-none">
                    <span class="text-2xl shrink-0 select-none">♻️</span>
                    <span class="text-xl font-extrabold tracking-tight text-emerald-600">TIECO</span>
                </div>

                <nav class="space-y-1 w-full">
                    @if($currentRole === 'warga')
                        <a href="{{ route('warga.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('warga.dashboard') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-speedometer2 text-lg"></i>
                            <span>Dasbor</span>
                        </a>
                        <a href="{{ route('warga.setor') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('warga.setor') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-recycle text-lg"></i>
                            <span>Setor Sampah</span>
                        </a>
                        <a href="{{ route('warga.bills') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('warga.bills') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-phone-vibrate text-lg"></i>
                            <span>Pulsa & Tagihan</span>
                        </a>
                        <a href="{{ route('warga.umkm') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('warga.umkm') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-shop text-lg"></i>
                            <span>Katalog & Peta UMKM</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('settings') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-gear text-lg"></i>
                            <span>Pengaturan</span>
                        </a>
                    @elseif($currentRole === 'bank_sampah')
                        <a href="{{ route('bank-sampah.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('bank-sampah.dashboard') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-speedometer2 text-lg"></i>
                            <span>Dasbor</span>
                        </a>
                        <a href="{{ route('bank-sampah.verifikasi') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('bank-sampah.verifikasi') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-patch-check text-lg"></i>
                            <span>Verifikasi Setoran</span>
                        </a>
                        <a href="{{ route('bank-sampah.stok') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('bank-sampah.stok') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-archive text-lg"></i>
                            <span>Manajemen Stok</span>
                        </a>
                        <a href="{{ route('bank-sampah.settlement') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('bank-sampah.settlement') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-wallet2 text-lg"></i>
                            <span>Settlement & Kas</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('settings') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-gear text-lg"></i>
                            <span>Pengaturan</span>
                        </a>
                    @elseif($currentRole === 'umkm')
                        <a href="{{ route('umkm.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('umkm.dashboard') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-qr-code-scan text-lg"></i>
                            <span>Dasbor & Validasi</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('settings') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-gear text-lg"></i>
                            <span>Pengaturan</span>
                        </a>
                    @elseif($currentRole === 'pembeli')
                        <a href="{{ route('pembeli.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('pembeli.dashboard') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-cart3 text-lg"></i>
                            <span>Lapak B2B</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('settings') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <i class="bi bi-gear text-lg"></i>
                            <span>Pengaturan</span>
                        </a>
                    @endif

                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap text-slate-600 hover:bg-slate-50">
                        <i class="bi bi-house-door text-lg text-slate-400"></i>
                        <span>Landing Page</span>
                    </a>

                    <a href="{{ route('dampak.realtime') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('dampak.realtime') ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-l-none' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="bi bi-bar-chart-line text-lg {{ request()->routeIs('dampak.realtime') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        <span>Laporan Dampak</span>
                    </a>
                </nav>
            </div>
        </aside>
    @endif

    {{-- MAIN CONTENT AREA --}}
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        @if(auth()->check() && ! request()->routeIs('home'))
            {{-- TOP NAVBAR --}}
            <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between sticky top-0 z-40 shadow-sm">
                
                {{-- Left: Hamburger & Clock --}}
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="p-2 -ml-2 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-emerald-600 transition-colors focus:outline-none" title="Toggle Sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    <div class="hidden sm:block">
                        <div id="realtimeClock" class="text-sm font-semibold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 flex items-center gap-2">
                            <i class="bi bi-clock text-emerald-600"></i> <span id="clockText">00:00:00</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Profile Dropdown --}}
                <div class="relative" id="profileDropdownContainer">
                    <button id="profileDropdownBtn" class="flex items-center gap-3 p-1 pr-3 rounded-full border border-slate-100 hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-100">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs overflow-hidden shrink-0">
                            @if($profilePhotoUrl)
                                <span class="hidden">{{ $initials ?? 'U' }}</span>
                                <img src="{{ $profilePhotoUrl }}" alt="Foto profil" class="w-full h-full object-cover" onerror="this.style.display='none'; this.previousElementSibling.classList.remove('hidden')">
                            @else
                                {{ $initials ?? 'U' }}
                            @endif
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wider">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="profileDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-2 opacity-0 invisible transform scale-95 transition-all origin-top-right z-50">
                        <a href="{{ route('settings', ['tab' => 'profile']) }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-emerald-600 transition-colors font-medium">
                            <i class="bi bi-person mr-2 text-slate-400"></i> Edit Profile
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-emerald-600 transition-colors font-medium">
                            <i class="bi bi-gear mr-2 text-slate-400"></i> Setting
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form action="{{ route('demo.logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-bold">
                                <i class="bi bi-box-arrow-right mr-2"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>
        @endif

        {{-- ALERTS --}}
        @if(session('success') || session('error'))
            <div class="p-6 pb-0">
                @if(session('success') && !session('modal_message'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                {{-- Jika ada session error tetapi tidak ada modal_message, tampilkan sebagai modal di tengah layar --}}
                @if(session('error') && !session('modal_message'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            showModal({
                                title: {!! json_encode(session('modal_title', 'Kesalahan')) !!},
                                message: {!! json_encode(session('error')) !!},
                                confirmText: {!! json_encode(session('modal_confirm_text', 'OK')) !!}
                            });
                        });
                    </script>
                @endif
            </div>
        @endif

        {{-- PAGE CONTENT --}}
        <div class="pt-3 px-6 pb-6 md:pt-4 md:px-8 md:pb-8 flex-1">
            @yield('content')
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Sidebar Toggle Logic
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                // Determine initial state based on window size
                if (window.innerWidth < 768) {
                    sidebar.classList.add('collapsed');
                }

                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }

            // 2. Real-time Clock Logic
            const clockText = document.getElementById('clockText');
            if (clockText) {
                function updateClock() {
                    const now = new Date();
                    let h = String(now.getHours()).padStart(2, '0');
                    let m = String(now.getMinutes()).padStart(2, '0');
                    let s = String(now.getSeconds()).padStart(2, '0');
                    clockText.textContent = `${h}:${m}:${s}`;
                }
                setInterval(updateClock, 1000);
                updateClock(); // initial call
            }

            // 3. Profile Dropdown Logic
            const profileDropdownBtn = document.getElementById('profileDropdownBtn');
            const profileDropdownMenu = document.getElementById('profileDropdownMenu');
            let isDropdownOpen = false;

            if (profileDropdownBtn && profileDropdownMenu) {
                profileDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    isDropdownOpen = !isDropdownOpen;
                    if (isDropdownOpen) {
                        profileDropdownMenu.classList.remove('invisible', 'opacity-0', 'scale-95');
                        profileDropdownMenu.classList.add('opacity-100', 'scale-100');
                    } else {
                        profileDropdownMenu.classList.add('invisible', 'opacity-0', 'scale-95');
                        profileDropdownMenu.classList.remove('opacity-100', 'scale-100');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (isDropdownOpen && !profileDropdownMenu.contains(e.target) && !profileDropdownBtn.contains(e.target)) {
                        isDropdownOpen = false;
                        profileDropdownMenu.classList.add('invisible', 'opacity-0', 'scale-95');
                        profileDropdownMenu.classList.remove('opacity-100', 'scale-100');
                    }
                });
            }
        });
    </script>

    @include('components.global-modal')

    <script>
        // Global modal controls
        (function(){
            function el(id){ return document.getElementById(id); }

            window.showModal = function(opts){
                opts = opts || {};
                var title = opts.title || 'Informasi';
                var message = opts.message || '';
                var confirmText = opts.confirmText || 'OK';
                var onConfirm = typeof opts.onConfirm === 'function' ? opts.onConfirm : null;

                var overlay = el('globalModalOverlay');
                var titleEl = el('globalModalTitle');
                var bodyEl = el('globalModalBody');
                var confirmBtn = el('globalModalConfirm');
                var cancelBtn = el('globalModalCancel');
                var closeX = el('globalModalCloseX');

                if(!overlay) return;

                titleEl.textContent = title;
                bodyEl.textContent = message;
                confirmBtn.textContent = confirmText;

                overlay.classList.remove('hidden');

                function cleanup(){
                    overlay.classList.add('hidden');
                    confirmBtn.removeEventListener('click', onClick);
                    cancelBtn.removeEventListener('click', onCancel);
                    closeX.removeEventListener('click', onCancel);
                    document.removeEventListener('keydown', onKey);
                }

                function onClick(e){ cleanup(); if(onConfirm) onConfirm(); }
                function onCancel(e){ cleanup(); }
                function onKey(e){ if(e.key === 'Escape'){ cleanup(); } }

                confirmBtn.addEventListener('click', onClick);
                cancelBtn.addEventListener('click', onCancel);
                closeX.addEventListener('click', onCancel);
                document.addEventListener('keydown', onKey);
            };

            window.closeModal = function(){ var o = document.getElementById('globalModalOverlay'); if(o) o.classList.add('hidden'); };

            window.showInsufficientPoints = function(amount){
                showModal({
                    title: 'Saldo tidak mencukupi',
                    message: 'Saldo poin Anda tidak mencukupi untuk nominal ini. Dibutuhkan ' + amount + ' Poin.',
                    confirmText: 'Top up'
                });
            };

            window.showSuccessModal = function(message){
                showModal({ title: 'Berhasil', message: message || 'Saldo berhasil masuk.', confirmText: 'OK' });
            };
        })();
    </script>

    @if(session('modal_message'))
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                showModal({
                    title: {!! json_encode(session('modal_title', 'Informasi')) !!},
                    message: {!! json_encode(session('modal_message')) !!},
                    confirmText: {!! json_encode(session('modal_confirm_text', 'OK')) !!}
                });
            });
        </script>
    @endif

    @yield('scripts')
</body>
</html>

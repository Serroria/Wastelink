<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TIECO - Platform Digital Bank Sampah Terintegrasi.">
    <title>@yield('title', 'TIECO - Bank Sampah Digital')</title>
    <link rel="icon" type="image/png" href="{{ asset('logoTieco.png') }}">
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
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Lucide Icons & Alpine.js -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary-green: #00b14f;
            --page-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--page-bg);
            color: #1e293b;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-green); }

        #sidebar-container {
            transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .floating-sidebar {
            height: 100%;
            margin: 0;
            background-color: #ffffff;
            border-radius: 0; /* Flat rectangular panel to touch edges */
            border-right: 1px solid #f1f5f9;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Nav links and states */
        .sidebar-link-new {
            display: flex;
            align-items: center;
            gap: 16px;
            height: 54px;
            padding: 0 18px;
            margin: 5px 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            border-radius: 16px;
            position: relative;
            cursor: pointer;
            transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hover interaction */
        .sidebar-link-new:hover {
            color: #0f172a;
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        /* Icon Container styling (42px circle) - transparent by default */
        .sidebar-icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: transparent;
            color: #64748b;
            flex-shrink: 0;
            transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link-new:hover .sidebar-icon-circle {
            background-color: transparent;
            color: #0f172a;
            transform: scale(1.08) rotate(3deg);
        }

        /* Active Menu State */
        .sidebar-link-new.active-item {
            background-color: #f0fbf4; /* Light green brand-50 */
            color: var(--primary-green) !important;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0, 177, 79, 0.05);
            border: 1px solid #dcf7e7; /* brand-100 */
        }

        .sidebar-link-new.active-item .sidebar-icon-circle {
            background-color: var(--primary-green);
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(0, 177, 79, 0.3);
            transform: scale(1.05);
        }

        /* Sidebar profile and headers styling */
        .sidebar-header-new {
            padding: 32px 24px 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Unified footer container without border-t or split background */
        .sidebar-footer-new {
            padding: 24px 0 100px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .user-profile-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            margin: 0 12px;
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 250ms ease;
        }
        .user-profile-badge:hover {
            border-color: #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        /* Collapsed mode hover blob element (bubble popping out) */
        .sidebar-hover-blob {
            position: absolute;
            left: 52px;
            top: 50%;
            transform: translateY(-50%) scale(0.6);
            width: 54px;
            height: 54px;
            background-color: var(--primary-green);
            color: #ffffff !important;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: all 250ms cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 50;
            box-shadow: 0 8px 24px rgba(0, 177, 79, 0.3);
        }

        .sidebar-link-new:hover .sidebar-hover-blob {
            opacity: 1;
            transform: translateY(-50%) scale(1);
            pointer-events: auto;
        }

        .active-item .sidebar-hover-blob {
            display: none !important;
        }

        /* Concave curves styling for bubble attachment */
        .sidebar-hover-blob::before {
            content: '';
            position: absolute;
            left: -2px;
            top: -18px;
            width: 20px;
            height: 20px;
            background-color: transparent;
            pointer-events: none;
            border-bottom-left-radius: 20px;
            box-shadow: -8px 8px 0 0 var(--primary-green);
        }

        .sidebar-hover-blob::after {
            content: '';
            position: absolute;
            left: -2px;
            bottom: -18px;
            width: 20px;
            height: 20px;
            background-color: transparent;
            pointer-events: none;
            border-top-left-radius: 20px;
            box-shadow: -8px -8px 0 0 var(--primary-green);
        }
    </style>
    @yield('styles')
<body class="h-screen overflow-hidden flex bg-[#f8fafc] text-slate-800" x-data="{ sidebarExpanded: localStorage.getItem('sidebar_expanded') === null ? (window.innerWidth >= 1024) : (localStorage.getItem('sidebar_expanded') === 'true'), mobileOpen: false }" @resize.window="if (window.innerWidth >= 1024) { sidebarExpanded = true; } else if (window.innerWidth >= 768) { sidebarExpanded = false; }" x-init="lucide.createIcons(); $watch('sidebarExpanded', val => { localStorage.setItem('sidebar_expanded', val); $nextTick(() => lucide.createIcons()); }); $watch('mobileOpen', () => $nextTick(() => lucide.createIcons()))">

    @if(auth()->check() && ! request()->routeIs('home'))
        @php
            $currentRole = auth()->user()->role;
            $initials = auth()->user()->initials();
            $profilePhotoUrl = auth()->user()->profilePhotoUrl();
        @endphp

        {{-- SIDEBAR --}}
        <!-- Desktop / Tablet Floating Sidebar Container -->
        <aside
            id="sidebar-container"
            class="hidden md:flex flex-col shrink-0 transition-all duration-300 ease-in-out h-screen sticky top-0 z-40"
            :class="sidebarExpanded ? 'w-[270px]' : 'w-[76px]'"
        >
            <div class="floating-sidebar">
                <!-- Sidebar Header -->
                <div class="sidebar-header-new" :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <span class="text-xl">♻️</span>
                    </div>
                    <div class="flex flex-col transition-all duration-300" x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <span class="text-lg font-black tracking-tight text-slate-800 leading-none">TIECO</span>
                    </div>
                </div>

                <!-- Navigation (No scroll) -->
                <div class="flex-1 py-6 flex flex-col justify-start">
                    <nav class="space-y-1">
                        @if($currentRole === 'warga')
                            <a href="{{ route('warga.dashboard') }}"
                               class="sidebar-link-new {{ request()->routeIs('warga.dashboard') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Dasbor">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Dasbor</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('warga.setor') }}"
                               class="sidebar-link-new {{ request()->routeIs('warga.setor') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Setor Sampah">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="recycle" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Setor Sampah</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="recycle" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('warga.bills') }}"
                               class="sidebar-link-new {{ request()->routeIs('warga.bills') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Pulsa & Tagihan">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Pulsa & Tagihan</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('warga.umkm') }}"
                               class="sidebar-link-new {{ request()->routeIs('warga.umkm') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Katalog & Peta UMKM">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="store" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Katalog & Peta UMKM</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="store" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('settings', ['tab' => 'security']) }}"
                               class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Pengaturan">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Pengaturan</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                            </a>
                        @elseif($currentRole === 'bank_sampah')
                            <a href="{{ route('bank-sampah.dashboard') }}"
                               class="sidebar-link-new {{ request()->routeIs('bank-sampah.dashboard') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Dasbor">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Dasbor</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('bank-sampah.verifikasi') }}"
                               class="sidebar-link-new {{ request()->routeIs('bank-sampah.verifikasi') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Verifikasi Setoran">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Verifikasi Setoran</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('bank-sampah.stok') }}"
                               class="sidebar-link-new {{ request()->routeIs('bank-sampah.stok') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Manajemen Stok">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="archive" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Manajemen Stok</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="archive" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('bank-sampah.settlement') }}"
                               class="sidebar-link-new {{ request()->routeIs('bank-sampah.settlement') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Settlement & Kas">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="wallet" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Settlement & Kas</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="wallet" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('settings', ['tab' => 'security']) }}"
                               class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Pengaturan">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Pengaturan</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                            </a>
                        @elseif($currentRole === 'umkm')
                            <a href="{{ route('umkm.dashboard') }}"
                               class="sidebar-link-new {{ request()->routeIs('umkm.dashboard') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Dasbor & Validasi">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="qr-code" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Dasbor & Validasi</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="qr-code" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('settings', ['tab' => 'security']) }}"
                               class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Pengaturan">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Pengaturan</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                            </a>
                        @elseif($currentRole === 'pembeli')
                            <a href="{{ route('pembeli.dashboard') }}"
                               class="sidebar-link-new {{ request()->routeIs('pembeli.dashboard') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Lapak B2B">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Lapak B2B</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                </div>
                            </a>
                            <a href="{{ route('settings', ['tab' => 'security']) }}"
                               class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }}"
                               :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                               title="Pengaturan">
                                <div class="sidebar-icon-circle">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                                <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Pengaturan</span>
                                <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                            </a>
                        @endif

                        <a href="{{ route('dampak.realtime') }}"
                           class="sidebar-link-new {{ request()->routeIs('dampak.realtime') ? 'active-item' : '' }}"
                           :class="sidebarExpanded ? 'justify-start' : 'justify-center'"
                           title="Laporan Dampak">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </div>
                            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Laporan Dampak</span>
                            <div class="sidebar-hover-blob" x-show="!sidebarExpanded">
                                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            </div>
                        </a>
                    </nav>
                </div>

                <!-- Sidebar Footer (Integrated into sidebar container) -->
                <div class="sidebar-footer-new">
                    <div class="user-profile-badge" :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center font-bold text-sm overflow-hidden shrink-0 shadow-sm">
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" alt="Foto profil" class="w-full h-full object-cover">
                            @else
                                {{ $initials ?? 'U' }}
                            @endif
                        </div>
                        <div class="text-left min-w-0 flex-1 transition-all duration-300" x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider truncate mt-0.5">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                        </div>
                    </div>

                    <form action="{{ route('demo.logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="sidebar-link-new text-red-600 hover:bg-red-50/70 hover:text-red-700" :class="sidebarExpanded ? 'justify-start' : 'justify-center'" title="Keluar Aplikasi">
                            <div class="sidebar-icon-circle bg-red-50/50 text-red-500">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </div>
                            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="font-semibold text-sm whitespace-nowrap">Keluar Aplikasi</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Drawer Sidebar Backdrop -->
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileOpen = false"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 md:hidden"
        ></div>

        <!-- Mobile Drawer Sidebar Container -->
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed top-0 bottom-0 left-0 w-72 bg-white shadow-2xl z-50 md:hidden flex flex-col justify-between"
        >
            <!-- Mobile Header -->
            <div class="p-5 flex items-center justify-between border-b border-slate-100/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <span class="text-xl">♻️</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-slate-800 leading-none">TIECO</span>
                    </div>
                </div>
                <button @click="mobileOpen = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Mobile Navigation Content -->
            <div class="flex-1 overflow-y-auto py-6">
                <nav class="space-y-1 w-full">
                    @if($currentRole === 'warga')
                        <a href="{{ route('warga.dashboard') }}"
                           class="sidebar-link-new {{ request()->routeIs('warga.dashboard') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Dasbor</span>
                        </a>
                        <a href="{{ route('warga.setor') }}"
                           class="sidebar-link-new {{ request()->routeIs('warga.setor') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="recycle" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Setor Sampah</span>
                        </a>
                        <a href="{{ route('warga.bills') }}"
                           class="sidebar-link-new {{ request()->routeIs('warga.bills') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="smartphone" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Pulsa & Tagihan</span>
                        </a>
                        <a href="{{ route('warga.umkm') }}"
                           class="sidebar-link-new {{ request()->routeIs('warga.umkm') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="store" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Katalog & Peta UMKM</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}"
                           class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="settings" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Pengaturan</span>
                        </a>
                    @elseif($currentRole === 'bank_sampah')
                        <a href="{{ route('bank-sampah.dashboard') }}"
                           class="sidebar-link-new {{ request()->routeIs('bank-sampah.dashboard') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Dasbor</span>
                        </a>
                        <a href="{{ route('bank-sampah.verifikasi') }}"
                           class="sidebar-link-new {{ request()->routeIs('bank-sampah.verifikasi') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Verifikasi Setoran</span>
                        </a>
                        <a href="{{ route('bank-sampah.stok') }}"
                           class="sidebar-link-new {{ request()->routeIs('bank-sampah.stok') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="archive" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Manajemen Stok</span>
                        </a>
                        <a href="{{ route('bank-sampah.settlement') }}"
                           class="sidebar-link-new {{ request()->routeIs('bank-sampah.settlement') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="wallet" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Settlement & Kas</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}"
                           class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="settings" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Pengaturan</span>
                        </a>
                    @elseif($currentRole === 'umkm')
                        <a href="{{ route('umkm.dashboard') }}"
                           class="sidebar-link-new {{ request()->routeIs('umkm.dashboard') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="qr-code" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Dasbor & Validasi</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}"
                           class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="settings" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Pengaturan</span>
                        </a>
                    @elseif($currentRole === 'pembeli')
                        <a href="{{ route('pembeli.dashboard') }}"
                           class="sidebar-link-new {{ request()->routeIs('pembeli.dashboard') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Lapak B2B</span>
                        </a>
                        <a href="{{ route('settings', ['tab' => 'security']) }}"
                           class="sidebar-link-new {{ request()->routeIs('settings') ? 'active-item' : '' }} justify-start px-6">
                            <div class="sidebar-icon-circle">
                                <i data-lucide="settings" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-sm">Pengaturan</span>
                        </a>
                    @endif

                    <!-- Common Links -->

                    <a href="{{ route('dampak.realtime') }}"
                       class="sidebar-link-new {{ request()->routeIs('dampak.realtime') ? 'active-item' : '' }} justify-start px-6">
                        <div class="sidebar-icon-circle">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        </div>
                        <span class="font-semibold text-sm">Laporan Dampak</span>
                    </a>
                </nav>
            </div>

            <!-- Mobile Footer (Integrated into mobile container) -->
            <div class="p-4 bg-white flex flex-col gap-2">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-white border border-slate-100 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center font-bold text-sm overflow-hidden shrink-0 shadow-sm">
                        @if($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" alt="Foto profil" class="w-full h-full object-cover">
                        @else
                            {{ $initials ?? 'U' }}
                        @endif
                    </div>
                    <div class="text-left min-w-0 flex-1">
                        <div class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider truncate mt-0.5">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                    </div>
                </div>

                <form action="{{ route('demo.logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl border border-dashed border-red-200 hover:border-red-300 transition-all cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                        <span>Keluar Aplikasi</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- MAIN CONTENT AREA --}}
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 h-screen overflow-y-auto">

        @if(auth()->check() && ! request()->routeIs('home'))
            {{-- TOP NAVBAR --}}
            <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between sticky top-0 z-40 shadow-sm">

                {{-- Left: Hamburger & Clock --}}
                <div class="flex items-center gap-4">
                    <button @click="window.innerWidth >= 768 ? sidebarExpanded = !sidebarExpanded : mobileOpen = !mobileOpen" class="p-2 -ml-2 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-emerald-600 transition-colors focus:outline-none" title="Toggle Sidebar">
                        <i data-lucide="menu" class="w-6 h-6"></i>
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

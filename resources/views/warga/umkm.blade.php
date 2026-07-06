@extends('layouts.app')
@section('title', 'Katalog & Peta Mitra UMKM — TIECO')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Custom Leaflet overrides to remove default messy borders and background */
    .custom-leaflet-icon {
        background: transparent !important;
        border: none !important;
    }

    /* Custom circular markers */
    .map-badge {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        color: white;
        font-size: 15px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
        border: 2px solid white;
        transition: transform 0.2s ease-in-out;
        box-sizing: border-box;
    }
    .map-badge:hover {
        transform: scale(1.15);
    }
    .badge-user {
        background-color: #3b82f6; /* Blue for Citizen */
    }
    .badge-bank {
        background-color: #10b981; /* Green for Waste Bank */
    }
    .badge-umkm {
        background-color: #f97316; /* Premium Orange for Merchant */
    }

    /* Pulsing user location ring */
    .pulse-ring {
        border: 3px solid #3b82f6;
        border-radius: 50%;
        height: 38px;
        width: 38px;
        position: absolute;
        left: -2px;
        top: -2px;
        animation: pulsate 1.8s ease-out infinite;
        opacity: 0.0;
        pointer-events: none;
        box-sizing: border-box;
    }
    @keyframes pulsate {
        0% { transform: scale(0.1, 0.1); opacity: 0.0; }
        50% { opacity: 1.0; }
        100% { transform: scale(1.6, 1.6); opacity: 0.0; }
    }

    /* Leaflet Popup customization */
    .leaflet-popup-content-wrapper {
        border-radius: 1.25rem !important; /* rounded-2xl */
        padding: 0.25rem !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        border: 1px solid #f1f5f9;
    }
    .leaflet-popup-content {
        margin: 12px !important;
        font-family: inherit !important;
    }
    .leaflet-popup-tip {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
    }

    /* Custom thin scrollbar for product list */
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="w-full space-y-6">
    <!-- Header & Point Balance Card -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-2">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-100/50">
                <i class="bi bi-tag-fill"></i> Kemitraan Ekosistem
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                Katalog & Peta Mitra UMKM
            </h1>
            <p class="text-xs text-slate-500 max-w-xl leading-relaxed">
                Tukarkan poin hasil setor sampah Anda dengan produk berkualitas dari Mitra UMKM terdekat. Cari merchant, filter berdasarkan kota, dan dapatkan rute navigasi pada peta.
            </p>
        </div>

        <div class="bg-emerald-600 rounded-2xl p-5 text-white shadow-md relative overflow-hidden flex items-center justify-between w-full md:w-80 shrink-0 select-none">
            <div class="space-y-0.5">
                <p class="text-[9px] font-bold text-emerald-100 uppercase tracking-widest flex items-center gap-1"><i class="bi bi-wallet2"></i> Saldo Poin Anda</p>
                <h2 class="text-2xl font-black tracking-tight">
                    {{ number_format($user->point_balance, 0, ',', '.') }} <span class="text-xs font-medium text-emerald-100">Poin</span>
                </h2>
                <div class="text-[9px] text-emerald-100 font-semibold">Setara Rp {{ number_format($user->point_balance * 10, 0, ',', '.') }}</div>
            </div>
            <div class="p-3 bg-white/10 border border-white/20 rounded-xl shadow-inner">
                <i class="bi bi-coin text-xl text-yellow-300"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Search Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="md:col-span-6 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <i class="bi bi-search text-xs"></i>
                </span>
                <input id="searchInput" type="search" placeholder="Cari nama toko, produk, atau alamat mitra..."
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-medium text-slate-700 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" />
            </div>

            <!-- Location Picker -->
            <div class="md:col-span-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <i class="bi bi-geo-alt-fill text-rose-500 text-xs"></i>
                </span>
                @php
                    // Predefined list of major cities/regencies in Indonesia
                    $predefinedCities = [
                        'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Utara', 'Jakarta Timur',
                        'Bogor', 'Depok', 'Tangerang', 'Tangerang Selatan', 'Bekasi', 'Karawang', 'Bandung', 'Cirebon',
                        'Sukabumi', 'Semarang', 'Surakarta', 'Yogyakarta', 'Surabaya', 'Malang', 'Madiun',
                        'Denpasar', 'Mataram', 'Kupang', 'Medan', 'Binjai', 'Banda Aceh', 'Padang',
                        'Pekanbaru', 'Palembang', 'Jambi', 'Bengkulu', 'Bandar Lampung', 'Pangkalpinang',
                        'Pontianak', 'Singkawang', 'Banjarmasin', 'Banjarbaru', 'Palangkaraya',
                        'Balikpapan', 'Samarinda', 'Tarakan', 'Makassar', 'Manado', 'Palu',
                        'Kendari', 'Gorontalo', 'Ambon', 'Ternate', 'Jayapura', 'Sorong'
                    ];

                    // Dynamically get any additional cities from partners that are not in the predefined list
                    $partnerCities = collect($partners)->map(function($p){
                        $parts = explode(',', $p->address ?? '');
                        return trim(last($parts));
                    })->unique()->filter()->values()->toArray();

                    // Merge, unique, and sort alphabetically
                    $allCities = collect(array_merge($predefinedCities, $partnerCities))
                        ->unique()
                        ->filter()
                        ->sort()
                        ->values();
                @endphp
                <select id="cityFilter" class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer transition-all appearance-none">
                    <option value="">Semua Kota</option>
                    @foreach($allCities as $c)
                        <option value="{{ strtolower($c) }}">{{ $c }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                    <i class="bi bi-chevron-down text-[10px]"></i>
                </span>
            </div>

            <!-- Reset Button -->
            <div class="md:col-span-2">
                <button id="resetFilters" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                </button>
            </div>
        </div>

        <!-- Category Chips -->
        <div class="flex flex-col gap-2.5 pt-2 border-t border-slate-50">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori Kemitraan</span>
            @php
                $categories = collect($partners)->pluck('category')->unique()->filter()->values();
                $emojiMap = [
                    'sembako' => '🌾',
                    'kerajinan daur ulang' => '🎨',
                    'kuliner' => '🍔',
                    'makanan' => '🍱',
                    'minuman' => '🥤',
                    'jasa' => '🛠️',
                ];
            @endphp
            <div class="flex flex-wrap gap-2" id="categoryChips">
                <button data-category="" class="category-chip px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all duration-200 bg-emerald-600 text-white border border-emerald-600 flex items-center gap-1.5">
                    🍽️ Semua
                </button>
                @foreach($categories as $cat)
                    @php
                        $lowerCat = strtolower($cat);
                        $emoji = $emojiMap[$lowerCat] ?? '🏪';
                    @endphp
                    <button data-category="{{ $cat }}" class="category-chip px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200/50 hover:bg-slate-100 hover:border-slate-200 transition-all duration-200 flex items-center gap-1.5">
                        <span>{{ $emoji }}</span> <span>{{ $cat }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Map & Merchant Feed (Split Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left: GoFood-style Merchant Feed (7 cols on large screens, redesigned as 2-column Grid) -->
        <div id="partnersList" class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($partners as $partner)
            @php
                $city = trim(last(explode(',', $partner->address ?? '')));
                $lowerCat = strtolower($partner->category);
                $bgClass = 'bg-emerald-600';
                if ($lowerCat === 'sembako') {
                    $bgClass = 'bg-amber-600';
                } elseif ($lowerCat === 'kerajinan daur ulang') {
                    $bgClass = 'bg-indigo-600';
                }
            @endphp
            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 partner-card flex flex-col justify-between"
                 data-partner-id="{{ $partner->id }}"
                 data-name="{{ strtolower($partner->store_name) }}"
                 data-address="{{ strtolower($partner->address) }}"
                 data-city="{{ strtolower($city) }}"
                 data-category="{{ strtolower($partner->category) }}">

                <!-- Merchant Banner header (Compact & Clean Design) -->
                <div class="relative h-24 {{ $bgClass }} p-4 flex flex-col justify-between text-white">
                    <div class="flex justify-between items-start gap-2 flex-wrap z-10">
                        <span class="px-2 py-0.5 bg-white/20 backdrop-blur-md text-white text-[8px] font-bold rounded-md uppercase tracking-wider border border-white/10">
                            🏪 {{ $partner->category }}
                        </span>
                        <span class="px-2 py-0.5 bg-white/20 backdrop-blur-md text-white border border-white/10 text-[8px] font-bold rounded-md uppercase tracking-wider">
                            Mitra Terverifikasi
                        </span>
                    </div>

                    <div class="z-10">
                        <h2 class="text-sm font-extrabold tracking-tight text-white line-clamp-1">
                            {{ $partner->store_name }}
                        </h2>
                    </div>
                </div>

                <!-- Merchant Main Body -->
                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-[11px] text-slate-500 leading-normal line-clamp-2 min-h-[32px]">{{ $partner->description }}</p>

                        <!-- Real-data indicators -->
                        <div class="flex flex-wrap items-center gap-2 text-[10px] text-slate-500 font-semibold mt-2.5 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100/50 w-full justify-between">
                            <div class="flex items-center gap-1 text-emerald-600">
                                <i class="bi bi-gift-fill text-[9px]"></i>
                                <span>{{ $partner->products->count() }} Pilihan Produk</span>
                            </div>
                            <div class="flex items-center gap-1 text-slate-600">
                                <i class="bi bi-geo-alt-fill text-rose-500 text-[9px]"></i>
                                <span>{{ $city }}</span>
                            </div>
                        </div>
                    </div>


                 <!-- Products Exchange Menu Action (Tampilan Ringkas) -->
                    <div class="border-t border-slate-100 pt-3 mt-1 flex justify-between items-center">
                        <div class="text-[10px] font-bold text-slate-500 flex items-center gap-1.5">
                            <i class="bi bi-gift text-emerald-600"></i> {{ $partner->products->count() }} Pilihan Voucher
                        </div>
                        <button type="button" onclick="openPartnerModal({{ $partner->id }})" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold rounded-xl text-[10px] transition-all shadow-sm">
                            Lihat Daftar Voucher
                        </button>
                    </div>
                </div>

                <!-- Footer Card Action: Locate Partner -->
                @if($partner->latitude && $partner->longitude)
                    <div class="px-4 pb-4 border-t border-slate-50 pt-2.5">
                        <button onclick="zoomToPartner({{ $partner->id }}, {{ $partner->latitude }}, {{ $partner->longitude }})"
                                class="w-full py-2 bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 font-bold rounded-xl text-[10px] transition-all flex items-center justify-center gap-1.5 border border-slate-100 hover:border-emerald-100">
                            <i class="bi bi-geo-alt-fill"></i> Temukan di Peta & Navigasi
                        </button>
                    </div>
                @endif
            </div>
            @endforeach

            @if($partners->isEmpty())
                <div class="col-span-full bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400 text-sm shadow-sm">
                    🏪 Belum ada mitra UMKM terdaftar saat ini.
                </div>
            @endif
        </div>

        <!-- Right: Sticky Ecosystem Map (5 cols on large screens, fixed offset for sticky top navbar) -->
        <div class="lg:col-span-5 lg:sticky lg:top-24">
            <div class="relative bg-white rounded-2xl p-3 border border-slate-100 shadow-sm">

                <!-- Floating GPS Status Overlay -->
                <div class="absolute top-6 left-6 z-[400] bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full shadow-sm border border-slate-100/80 flex items-center gap-1.5 text-[9px] font-extrabold text-slate-600">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <span id="geoStatus">GPS Siap</span>
                </div>

                <!-- Leaflet Map Container -->
                <div id="catalogMap" class="w-full h-[320px] md:h-[450px] lg:h-[550px] rounded-xl border border-slate-100 relative z-10"></div>

                <!-- Floating Map Legend Overlay (Compact Flexwrap Design) -->
                <div class="absolute bottom-6 inset-x-6 z-[400] bg-white/95 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-slate-100/80 flex flex-wrap gap-2 justify-between items-center text-[9px] font-bold text-slate-600 shadow-sm">
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500 border border-white shadow-sm"></span>
                        <span>Anda</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded bg-emerald-500 border border-white shadow-sm"></span>
                        <span>Bank Sampah</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-orange-500 border border-white shadow-sm"></span>
                        <span>Mitra UMKM</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- KUMPULAN MODAL VOUCHER PER TOKO --}}
    @foreach($partners as $partner)
    <div id="modal-partner-{{ $partner->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[500] hidden items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in">

            {{-- Header Modal --}}
            <div class="p-5 bg-slate-50 border-b border-slate-100 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="bi bi-shop text-emerald-600"></i> {{ $partner->store_name }}
                    </h3>
                    <p class="text-[10px] text-slate-500 mt-1"><i class="bi bi-geo-alt"></i> {{ $partner->address }}</p>
                </div>
                <button type="button" onclick="closePartnerModal({{ $partner->id }})" class="text-slate-400 hover:text-slate-600 text-lg shrink-0">✕</button>
            </div>

            {{-- List Produk/Voucher --}}
            <div class="p-5 overflow-y-auto bg-slate-50/50 flex-1">
                @if($partner->products->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs border border-dashed border-slate-200 rounded-2xl">
                        Toko ini belum memiliki voucher aktif.
                    </div>
                @else
                    <div class="space-y-3">
                        @php $isFirst = true; @endphp
                        @foreach($partner->products as $product)
                            <div class="p-4 bg-white border border-slate-100 rounded-2xl flex justify-between items-center gap-3 shadow-sm hover:shadow-md transition-all">
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-800">{{ $product->name }}</span>
                                        @if($isFirst)
                                            <span class="bg-amber-50 text-amber-700 text-[8px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider border border-amber-100/50 shrink-0">Best</span>
                                            @php $isFirst = false; @endphp
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-500 leading-relaxed">{{ $product->description }}</div>
                                </div>

                                <div class="text-right flex flex-col items-end gap-2 flex-shrink-0 border-l border-slate-100 pl-3">
                                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100/50 shadow-inner">
                                        {{ number_format($product->points_cost, 0, ',', '.') }} Poin
                                    </span>
                                    <form action="{{ route('warga.redeem', $product->id) }}" method="POST" class="mb-0 w-full">
                                        @csrf
                                        @if($user->point_balance >= $product->points_cost && $product->stock > 0)
                                            <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all shadow-sm">
                                                Tukar
                                            </button>
                                        @elseif($product->stock <= 0)
                                            <button type="button" class="w-full px-3 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-[10px] cursor-not-allowed" disabled>
                                                Habis
                                            </button>
                                        @else
                                            <button type="button" onclick="alert('Saldo Poin Anda tidak mencukupi untuk menukar produk ini.')" class="w-full px-3 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-[10px] hover:bg-slate-200 transition-colors">
                                                Poin Kurang
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    function openPartnerModal(id) {
        const modal = document.getElementById('modal-partner-' + id);
        if(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closePartnerModal(id) {
        const modal = document.getElementById('modal-partner-' + id);
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, markers = {};

    document.addEventListener('DOMContentLoaded', function(){
        // === INISIALISASI PETA ===
        const bankLat = {{ $bankSampahLat }};
        const bankLng = {{ $bankSampahLon }};

        map = L.map('catalogMap', { zoomControl: false }).setView([bankLat, bankLng], 14);

        // Add zoom control at bottom-right for clean top overlays
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // CartoDB Positron Tile Layer (Premium minimalist light grey style)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // === ICON KUSTOM ===
        const bankIcon = L.divIcon({
            html: '<div class="map-badge badge-bank"><i class="bi bi-bank"></i></div>',
            className: 'custom-leaflet-icon',
            iconSize: [38, 38],
            iconAnchor: [19, 19]
        });

        const umkmIcon = L.divIcon({
            html: '<div class="map-badge badge-umkm"><i class="bi bi-shop"></i></div>',
            className: 'custom-leaflet-icon',
            iconSize: [38, 38],
            iconAnchor: [19, 19]
        });

        const userIcon = L.divIcon({
            html: '<div class="map-badge badge-user"><i class="bi bi-person-fill"></i><div class="pulse-ring"></div></div>',
            className: 'custom-leaflet-icon',
            iconSize: [38, 38],
            iconAnchor: [19, 19]
        });

        // === TAMBAHKAN MARKER BANK SAMPAH PUSAT ===
        const bankMarker = L.marker([bankLat, bankLng], { icon: bankIcon }).addTo(map);
        bankMarker.bindPopup(`
            <div class="p-2 space-y-1.5" style="min-width: 180px;">
                <div class="text-xs font-bold text-emerald-800 uppercase tracking-wide">🏢 ${'{{ $bankSampahName }}'}</div>
                <div class="text-[10px] text-slate-500 font-medium">${'{{ $bankSampahAddress }}'}</div>
                <div class="text-[9px] text-slate-400 bg-slate-50 border border-slate-100 p-1 rounded font-bold uppercase mt-1">Pusat Penimbangan & Drop-Off</div>
                <a href="https://www.google.com/maps/dir/?api=1&destination=${bankLat},${bankLng}" target="_blank" class="block w-full text-center py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-bold mt-2 shadow-sm transition-all hover:bg-emerald-700">
                    <i class="bi bi-send-fill text-[8px]"></i> Petunjuk Arah
                </a>
            </div>
        `);

        // === TAMBAHKAN MARKER UMKM MITRA ===
        @foreach($partners as $partner)
            @if($partner->latitude && $partner->longitude)
                (function(){
                    const id = {{ $partner->id }};
                    const lat = {{ $partner->latitude }};
                    const lng = {{ $partner->longitude }};
                    const name = {!! json_encode($partner->store_name) !!};
                    const address = {!! json_encode($partner->address) !!};
                    const desc = {!! json_encode($partner->description) !!};
                    const category = {!! json_encode($partner->category) !!};
                    let productsList = '';

                    @foreach($partner->products as $p)
                        productsList += '<li class="flex justify-between py-1 border-b border-slate-50 text-[10px]"><span>' + {!! json_encode($p->name) !!} + '</span><span class="font-bold text-emerald-600">' + {!! json_encode(number_format($p->points_cost,0,",",".")) !!} + ' Poin</span></li>';
                    @endforeach

                    if (!productsList) {
                        productsList = '<div class="text-slate-400 italic text-[9px]">Belum ada produk.</div>';
                    } else {
                        productsList = `<ul class="divide-y divide-slate-50 max-h-24 overflow-y-auto mt-1">${productsList}</ul>`;
                    }

                    const marker = L.marker([lat, lng], { icon: umkmIcon }).addTo(map);
                    marker.bindPopup(`
                        <div class="p-2 space-y-2" style="min-width: 200px;">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-xs font-bold text-slate-800">${name}</span>
                                <span class="px-1.5 py-0.5 text-[8px] bg-amber-50 text-amber-700 border border-amber-100 rounded font-bold uppercase">${category}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 leading-snug">${desc}</div>
                            <div class="text-[9px] text-slate-500"><i class="bi bi-geo-alt"></i> ${address}</div>
                            <div class="border-t border-slate-100 pt-1.5 mt-1">
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Katalog Tukar Poin:</div>
                                ${productsList}
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="block w-full text-center py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-[9px] font-bold mt-2 shadow-sm transition-all">
                                <i class="bi bi-send-fill text-[8px]"></i> Petunjuk Arah
                            </a>
                        </div>
                    `);
                    markers[id] = marker;
                })();
            @endif
        @endforeach

        // === DETEKSI LOKASI USER (GEOLOCATION GPS) ===
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const userLat = pos.coords.latitude;
                    const userLng = pos.coords.longitude;

                    const userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
                    userMarker.bindPopup('<div class="text-xs font-bold p-1 text-blue-700"><i class="bi bi-geo-alt-fill"></i> Anda di sini</div>');

                    document.getElementById('geoStatus').textContent = "GPS Aktif";

                    // Fit bounds to show user and central Bank Sampah
                    const group = new L.featureGroup([userMarker, bankMarker]);
                    map.fitBounds(group.pad(0.3));
                },
                function(err) {
                    document.getElementById('geoStatus').textContent = "Peta Hub & Mitra";
                    console.warn("Geolocation warning: " + err.message);
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        } else {
            document.getElementById('geoStatus').textContent = "GPS Tidak Didukung";
        }

        // === LOGIKA FILTER & PENCARIAN ===
        const searchInput = document.getElementById('searchInput');
        const cityFilter = document.getElementById('cityFilter');
        const resetBtn = document.getElementById('resetFilters');
        let activeCategoryChip = document.querySelector('.category-chip[data-category=""]');

        function applyFilters() {
            const q = (searchInput.value || '').toLowerCase().trim();
            const city = (cityFilter.value || '').toLowerCase().trim();
            const selectedCategory = activeCategoryChip ? activeCategoryChip.dataset.category.toLowerCase().trim() : '';

            document.querySelectorAll('.partner-card').forEach(function(card) {
                const name = card.dataset.name || '';
                const address = card.dataset.address || '';
                const c = card.dataset.city || '';
                const cat = card.dataset.category || '';
                const id = card.dataset.partnerId;

                const matchesQ = q === '' || name.includes(q) || address.includes(q);
                const matchesCity = city === '' || c === city;
                const matchesCategory = selectedCategory === '' || cat === selectedCategory;

                if (matchesQ && matchesCity && matchesCategory) {
                    card.style.display = '';
                    if (markers[id]) {
                        map.addLayer(markers[id]);
                    }
                } else {
                    card.style.display = 'none';
                    if (markers[id]) {
                        map.removeLayer(markers[id]);
                    }
                }
            });
        }

        // Category Chips Click handlers
        document.querySelectorAll('.category-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                if (activeCategoryChip) {
                    activeCategoryChip.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                    activeCategoryChip.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200/50', 'hover:bg-slate-100');
                    activeCategoryChip.classList.remove('font-bold');
                    activeCategoryChip.classList.add('font-semibold');
                }

                chip.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200/50', 'hover:bg-slate-100');
                chip.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                chip.classList.remove('font-semibold');
                chip.classList.add('font-bold');

                activeCategoryChip = chip;
                applyFilters();
            });
        });

        searchInput.addEventListener('input', applyFilters);
        cityFilter.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            cityFilter.value = '';

            if (activeCategoryChip) {
                activeCategoryChip.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                activeCategoryChip.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200/50', 'hover:bg-slate-100');
                activeCategoryChip.classList.remove('font-bold');
                activeCategoryChip.classList.add('font-semibold');
            }

            const defaultChip = document.querySelector('.category-chip[data-category=""]');
            if (defaultChip) {
                defaultChip.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200/50', 'hover:bg-slate-100');
                defaultChip.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                defaultChip.classList.remove('font-semibold');
                defaultChip.classList.add('font-bold');
                activeCategoryChip = defaultChip;
            }

            applyFilters();
            map.setView([bankLat, bankLng], 14);
        });

        // Zoom map to partner handler
        window.zoomToPartner = function(id, lat, lng) {
            const marker = markers[id];
            if (marker) {
                map.setView([lat, lng], 16);
                setTimeout(() => marker.openPopup(), 300);

                // On mobile, scroll map into view
                const mapContainer = document.getElementById('catalogMap');
                if (window.innerWidth < 1024) {
                    mapContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        };
    });
</script>
@endsection

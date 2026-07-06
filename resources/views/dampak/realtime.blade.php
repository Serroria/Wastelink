@extends('layouts.app')
@section('title', 'Laporan Dampak Real-Time — TIECO')

@section('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    @keyframes pulse-soft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(0.98); }
    }
    .pulse-animation {
        animation: pulse-soft 3s ease-in-out infinite;
    }
</style>
@endsection

@section('content')
<div class="w-full space-y-8 animate-fade-in">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                <span class="text-3xl">📊</span> Laporan Dampak & Ekonomi Sirkular
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pantau tren pengolahan sampah dan perputaran manfaat sosial di RT kita secara real-time.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 pulse-animation"></span>
                Sistem Online & Real-time
            </span>
        </div>
    </div>

    {{-- Filter Rentang Waktu --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
        <form action="{{ route('dampak.realtime') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <span class="text-xs font-bold text-slate-500">Filter Periode:</span>
            
            {{-- Unified Date Range Container --}}
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-4 py-2 rounded-xl">
                <input type="date" name="start_date" id="start_date" value="{{ $startDateInput }}" class="bg-transparent border-none text-slate-700 text-xs focus:outline-none focus:ring-0 w-32 cursor-pointer" required>
                <span class="text-xs text-slate-400 font-semibold px-1 select-none">s/d</span>
                <input type="date" name="end_date" id="end_date" value="{{ $endDateInput }}" class="bg-transparent border-none text-slate-700 text-xs focus:outline-none focus:ring-0 w-32 cursor-pointer" required>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-emerald-600/10 hover:shadow-lg hover:shadow-emerald-600/20 hover:-translate-y-0.5 transform flex items-center justify-center gap-1.5">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if($startDateInput || $endDateInput)
                    <a href="{{ route('dampak.realtime') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 text-xs font-semibold rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Role Specific Contribution Section --}}
    @if(auth()->user()->role === 'warga')
        {{-- Warga Personal Contribution Card --}}
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-emerald-800 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-person-heart"></i> Kontribusi Personal Anda</h3>
            <p class="text-xs text-slate-500 mt-1">Berikut adalah dampak nyata yang berhasil Anda kontribusikan secara pribadi selama periode ini.</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Sampah Saya Setor</span>
                    <span class="text-lg font-black text-slate-800 mt-1 block">{{ number_format($roleData['personal_weight'], 1, ',', '.') }} kg</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Estimasi Reduksi CO2</span>
                    <span class="text-lg font-black text-sky-700 mt-1 block">{{ number_format($roleData['personal_weight'] * 1.2, 1, ',', '.') }} kg CO2</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Penyetoran Saya</span>
                    <span class="text-lg font-black text-slate-800 mt-1 block">{{ $roleData['personal_deposits_count'] }} Kali</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Voucher Saya Tukar</span>
                    <span class="text-lg font-black text-emerald-700 mt-1 block">{{ $roleData['personal_vouchers_count'] }} Pcs</span>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->role === 'bank_sampah')
        {{-- Operator Operational Overview Card --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-blue-800 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-shield-check"></i> Ikhtisar Operasional Pengelola</h3>
            <p class="text-xs text-slate-500 mt-1">Status antrean operasional dan neraca kas bank sampah untuk tindakan pengelolaan.</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Kas Bank Sampah</span>
                    <span class="text-lg font-black text-slate-800 mt-1 block">Rp {{ number_format($roleData['cash_balance'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Setoran Menunggu Verifikasi</span>
                    <span class="text-lg font-black text-amber-700 mt-1 block">{{ $roleData['pending_deposits'] }} Antrean</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Klaim Settlement UMKM</span>
                    <span class="text-lg font-black text-slate-800 mt-1 block">{{ $roleData['pending_settlements'] }} Pengajuan</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Unpaid Settlements</span>
                    <span class="text-lg font-black text-red-600 mt-1 block">Rp {{ number_format($roleData['unpaid_settlement_amount'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->role === 'umkm')
        {{-- UMKM Merchant Performance Card --}}
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-amber-800 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-shop"></i> Metrik Penjualan & Kemitraan Toko</h3>
            @if($roleData['has_shop'])
                <p class="text-xs text-slate-500 mt-1">Data penjualan produk melalui penukaran voucher sembako di toko **{{ $roleData['store_name'] }}**.</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div class="bg-white/80 p-4 rounded-2xl border border-white">
                        <span class="text-xs text-slate-400 block">Voucher Ditukarkan</span>
                        <span class="text-lg font-black text-slate-800 mt-1 block">{{ $roleData['claimed_vouchers'] }} Transaksi</span>
                    </div>
                    <div class="bg-white/80 p-4 rounded-2xl border border-white">
                        <span class="text-xs text-slate-400 block">Total Pendapatan Toko</span>
                        <span class="text-lg font-black text-emerald-700 mt-1 block">Rp {{ number_format($roleData['total_revenue'], 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-white/80 p-4 rounded-2xl border border-white">
                        <span class="text-xs text-slate-400 block">Produk Katalog Aktif</span>
                        <span class="text-lg font-black text-slate-800 mt-1 block">{{ $roleData['active_products'] }} Produk</span>
                    </div>
                    <div class="bg-white/80 p-4 rounded-2xl border border-white">
                        <span class="text-xs text-slate-400 block">Settlement Menunggu Cair</span>
                        <span class="text-lg font-black text-amber-700 mt-1 block">Rp {{ number_format($roleData['pending_settlement_amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-500 mt-1">Anda belum terdaftar sebagai Mitra UMKM atau pendaftaran Anda masih diproses. Silakan ajukan pendaftaran toko di dasbor utama.</p>
            @endif
        </div>
    @elseif(auth()->user()->role === 'pembeli')
        {{-- Pembeli Procurement Card --}}
        <div class="bg-gradient-to-r from-purple-50 to-fuchsia-50 border border-purple-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-purple-800 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-cart-check"></i> Metrik Pengadaan & Bahan Baku Industri</h3>
            <p class="text-xs text-slate-500 mt-1">Ringkasan transaksi pembelian bahan baku sampah daur ulang B2B yang Anda lakukan.</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Bahan Baku Diamankan</span>
                    <span class="text-lg font-black text-slate-800 mt-1 block">{{ number_format($roleData['buyer_weight'], 1, ',', '.') }} kg</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Pembelian B2B Saya</span>
                    <span class="text-lg font-black text-purple-700 mt-1 block">{{ $roleData['buyer_purchases_count'] }} Transaksi</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Total Pengeluaran B2B</span>
                    <span class="text-lg font-black text-slate-800 mt-1 block">Rp {{ number_format($roleData['buyer_spent'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/80 p-4 rounded-2xl border border-white">
                    <span class="text-xs text-slate-400 block">Saldo Deposit Bisnis</span>
                    <span class="text-lg font-black text-emerald-700 mt-1 block">Rp {{ number_format($roleData['buyer_balance'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        
        {{-- Card 1: Sampah Terkelola --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-6xl opacity-5 group-hover:scale-110 transition-transform duration-300">🚛</div>
            <div>
                <span class="text-2xl">🚛</span>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-3">Sampah Terkelola</p>
                <h3 class="text-xl md:text-2xl font-black text-slate-800 mt-1">
                    {{ number_format($totalWeight, 1, ',', '.') }} <span class="text-xs font-semibold text-slate-500">kg</span>
                </h3>
            </div>
            <div class="text-[9px] text-emerald-600 font-semibold mt-3 flex items-center gap-1">
                <i class="bi bi-arrow-up-short"></i> Seluruh setoran disetujui
            </div>
        </div>

        {{-- Card 2: Penyelamatan Karbon --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-6xl opacity-5 group-hover:scale-110 transition-transform duration-300">🌍</div>
            <div>
                <span class="text-2xl">🌍</span>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-3">Reduksi Emisi CO2</p>
                <h3 class="text-xl md:text-2xl font-black text-slate-800 mt-1">
                    {{ number_format($totalWeight * 1.2, 1, ',', '.') }} <span class="text-xs font-semibold text-slate-500">kg CO2</span>
                </h3>
            </div>
            <div class="text-[9px] text-slate-400 mt-3">
                Asumsi 1 kg sampah ≈ 1.2 kg CO₂
            </div>
        </div>

        {{-- Card 3: Voucher Terdistribusi --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-6xl opacity-5 group-hover:scale-110 transition-transform duration-300">🎫</div>
            <div>
                <span class="text-2xl">🎫</span>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-3">Voucher Sembako</p>
                <h3 class="text-xl md:text-2xl font-black text-slate-800 mt-1">
                    {{ number_format($totalVouchers, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">Keping</span>
                </h3>
            </div>
            <div class="text-[9px] text-emerald-600 font-semibold mt-3 flex items-center gap-1">
                Telah ditukarkan oleh warga
            </div>
        </div>

        {{-- Card 4: Aliran Uang Sirkular --}}
        <div class="bg-emerald-600 rounded-3xl p-5 text-white shadow-md hover:shadow-lg transition-all flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-6xl opacity-10 group-hover:scale-110 transition-transform duration-300 text-white">💰</div>
            <div>
                <span class="text-2xl">💰</span>
                <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest mt-3">Dana Terdistribusi</p>
                <h3 class="text-xl md:text-2xl font-black mt-1">
                    Rp {{ number_format($totalCashDistributed, 0, ',', '.') }}
                </h3>
            </div>
            <div class="text-[9px] text-emerald-100 mt-3 font-semibold">
                Tunai cair + nominal voucher
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Chart 1: Sampah & CO2 --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-5 md:p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="text-emerald-500">●</span> Tren Pengolahan Sampah & Karbon
                </h3>
                <p class="text-[11px] text-slate-400 mt-1">Grafik bulanan jumlah berat sampah terkelola (kg) dan emisi CO2 yang terselamatkan (kg CO2).</p>
            </div>
            <div class="mt-6 min-h-[300px] flex items-center justify-center">
                <canvas id="wasteChart" class="w-full max-h-[320px]"></canvas>
            </div>
        </div>

        {{-- Chart 2: Ekonomi Sirkular --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-5 md:p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="text-emerald-500">●</span> Tren Penyaluran Manfaat Ekonomi
                </h3>
                <p class="text-[11px] text-slate-400 mt-1">Visualisasi jumlah voucher sembako yang tersalurkan (keping) bersama pencairan saldo tunai warga (Rupiah).</p>
            </div>
            <div class="mt-6 min-h-[300px] flex items-center justify-center">
                <canvas id="economyChart" class="w-full max-h-[320px]"></canvas>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="glass-card border border-emerald-100 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <span class="text-4xl shrink-0">💡</span>
            <div class="space-y-1">
                <h4 class="text-sm font-bold text-slate-800">Bagaimana Dampak Ini Dihitung?</h4>
                <p class="text-xs text-slate-500 leading-relaxed max-w-2xl">
                    Setiap sampah plastik, logam, dan kertas yang disetorkan warga ditimbang di operator Bank Sampah TIECO. Berat sampah dikonversi menjadi poin tabungan warga.
                    Seluruh sampah terpilah dijual ke B2B Pabrik Daur Ulang mitra. Keuntungan penjualan tersebut disalurkan kembali kepada warga dalam bentuk subsidi pangan (Voucher Sembako di Warung UMKM) dan pencairan saldo tunai langsung ke rekening bank nasabah.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Data dari backend PHP
        const months = {!! json_encode($months) !!};
        const weightData = {!! json_encode($weightTrend) !!};
        const co2Data = {!! json_encode($co2Trend) !!};
        const vouchersData = {!! json_encode($vouchersTrend) !!};
        const cashData = {!! json_encode($cashTrend) !!};

        // ===== 1. WASTE & CO2 CHART =====
        const wasteCtx = document.getElementById('wasteChart').getContext('2d');
        new Chart(wasteCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sampah Terkelola (kg)',
                        data: weightData,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.05)',
                        borderWidth: 3,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 7,
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'CO2 Terselamatkan (kg CO2)',
                        data: co2Data,
                        borderColor: '#0284c7',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#0284c7',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 10, weight: 'bold', family: 'Inter' },
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 11, weight: 'bold', family: 'Inter' },
                        bodyFont: { size: 12, family: 'Inter' }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, family: 'Inter' } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            font: { size: 10, family: 'Inter' },
                            callback: function(value) { return value + ' kg'; }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { 
                            font: { size: 10, family: 'Inter' },
                            callback: function(value) { return value + ' kg'; }
                        }
                    }
                }
            }
        });

        // ===== 2. ECONOMY CHART =====
        const economyCtx = document.getElementById('economyChart').getContext('2d');
        new Chart(economyCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Voucher Sembako (Keping)',
                        data: vouchersData,
                        backgroundColor: '#fbbf24',
                        hoverBackgroundColor: '#f59e0b',
                        borderRadius: 8,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Dana Tunai Warga (Rupiah)',
                        data: cashData,
                        type: 'line',
                        borderColor: '#059669',
                        borderWidth: 3,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 10, weight: 'bold', family: 'Inter' },
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 11, weight: 'bold', family: 'Inter' },
                        bodyFont: { size: 12, family: 'Inter' },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.type === 'line') {
                                    label += 'Rp ' + context.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                } else {
                                    label += context.raw + ' keping';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, family: 'Inter' } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            font: { size: 10, family: 'Inter' },
                            callback: function(value) { return value + ' pcs'; }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { 
                            font: { size: 10, family: 'Inter' },
                            callback: function(value) { return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

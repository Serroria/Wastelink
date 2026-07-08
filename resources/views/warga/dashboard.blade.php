@extends('layouts.app')
@section('title', 'Dasbor Warga — TIECO')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Premium visual styles for the voucher ticket cut-out notches */
    .ticket-notch-top {
        position: absolute;
        top: 0;
        right: 25%;
        width: 16px;
        height: 8px;
        background-color: #f8fafc; /* Matches main page background */
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        border: 1px solid #f1f5f9;
        border-top: none;
        transform: translateY(-1px);
    }
    .ticket-notch-bottom {
        position: absolute;
        bottom: 0;
        right: 25%;
        width: 16px;
        height: 8px;
        background-color: #f8fafc;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        border: 1px solid #f1f5f9;
        border-bottom: none;
        transform: translateY(1px);
    }
</style>
@endsection

@section('content')
@php
    // Warm dynamic greeting based on current local time
    $hour = date('H');
    if ($hour < 11) {
        $greeting = 'Selamat pagi';
    } elseif ($hour < 15) {
        $greeting = 'Selamat siang';
    } elseif ($hour < 19) {
        $greeting = 'Selamat sore';
    } else {
        $greeting = 'Selamat malam';
    }
@endphp

<div class="w-full space-y-8 font-sans">

    {{-- Welcome header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 font-display tracking-tight">{{ $greeting }}, {{ $user->name }}!</h1>
            <p class="text-sm text-slate-500 mt-1">Mari lanjutkan langkah baikmu hari ini untuk bumi kita.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs px-3.5 py-1.5 bg-emerald-50 text-emerald-800 font-bold rounded-full border border-emerald-100 shadow-sm flex items-center gap-1.5 shrink-0"><i class="bi bi-patch-check-fill text-emerald-600"></i> Warga Aktif</span>
        </div>
    </div>

    {{-- ASYMMETRICAL 2-COLUMN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT COLUMN (2/3 width) --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Hero Balance Card (Deep dark green, amber highlights, Outfit font) --}}
            <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-950 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden border border-emerald-800/20">
                <!-- Abstract organic vector decorations for handcrafted look -->
                <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-48 h-48 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-8">
                    <div>
                        <p class="text-xs font-semibold text-emerald-200/80 uppercase tracking-widest flex items-center gap-1.5">
                            Saldo Poin Tabungan
                            <i class="bi bi-info-circle text-[10px] text-emerald-300" title="1 Poin bernilai Rp 10. Dapatkan dari menyetorkan sampah terpilah."></i>
                        </p>
                        <h2 class="text-5xl font-black mt-2 font-display tracking-tight flex items-baseline gap-2">
                            {{ number_format($user->point_balance, 0, ',', '.') }}
                            <span class="text-lg font-medium text-emerald-200">Poin</span>
                        </h2>
                    </div>
                    <div class="sm:text-right shrink-0">
                        <p class="text-xs font-semibold text-emerald-200/80 uppercase tracking-widest">Setara Rupiah</p>
                        <div class="mt-2">
                            <span class="bg-amber-100/90 text-amber-950 border border-amber-200/30 px-3.5 py-1.5 rounded-full font-black text-sm inline-flex items-center gap-1.5 shadow-sm">
                                Rp {{ number_format($user->point_balance * 10, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-[9px] text-emerald-300/75 mt-2">Kurs konversi: 1 Poin = Rp 10</p>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-5 flex flex-wrap gap-x-8 gap-y-2 text-xs text-emerald-200">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] text-emerald-300"><i class="bi bi-scales"></i></div>
                        <span>Berat Sampah: <strong class="text-white font-bold">{{ number_format($totalWeight, 1, ',', '.') }} kg</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] text-emerald-300"><i class="bi bi-journal-check"></i></div>
                        <span>Penyetoran: <strong class="text-white font-bold">{{ $totalDeposits }} Kali</strong></span>
                    </div>
                </div>
            </div>

            {{-- Layanan Grid (Custom card layout using service-card blade component) --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest font-display">Layanan Utama</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-service-card
                        :href="route('warga.setor')"
                        title="Setor Sampah"
                        description="Kirim atau jemput sampah terpilahmu"
                        icon="recycle"
                        color="emerald"
                        badge="Populer" />

                    <x-service-card
                        :href="route('warga.bills')"
                        title="Pulsa & Tagihan"
                        description="Tukar poin ke token PLN & pulsa HP"
                        icon="phone-vibrate"
                        color="sky" />

                    <x-service-card
                        :href="route('warga.umkm')"
                        title="Katalog UMKM"
                        description="Belanja produk lokal dengan voucher"
                        icon="shop"
                        color="indigo" />

                    <x-service-card
                        :isButton="true"
                        onclick="document.getElementById('withdrawModal').classList.remove('hidden')"
                        title="Tarik Tunai"
                        description="Cairkan tabungan langsung ke rekening"
                        icon="cash-coin"
                        color="amber"
                        badge="Instan" />
                </div>
            </div>

            {{-- Riwayat Penyetoran Sampah --}}
            <div class="bg-white border border-stone-100 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest font-display flex items-center gap-2">
                        <i class="bi bi-clock-history text-emerald-600"></i> Riwayat Penyetoran
                    </h3>
                </div>

                @if($deposits->isEmpty())
                    <div class="text-center py-10 border border-dashed border-stone-100 rounded-2xl bg-stone-50/30">
                        <div class="text-3xl mb-2">📦</div>
                        <p class="text-xs text-slate-400 font-semibold">Belum ada pengajuan setoran sampah.</p>
                        <a href="{{ route('warga.setor') }}" class="text-emerald-600 font-bold text-xs mt-1 inline-block hover:underline">Mulai setoran pertama</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-stone-100 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="pb-3 pr-4">Tanggal</th>
                                    <th class="pb-3 px-4">Metode</th>
                                    <th class="pb-3 px-4">Status</th>
                                    <th class="pb-3 px-4 text-center">Lacak</th>
                                    <th class="pb-3 pl-4 text-right">Hasil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-50">
                                @foreach($deposits as $deposit)
                                    <tr class="text-slate-600 hover:bg-stone-50/30 transition-colors">
                                        <td class="py-3.5 pr-4 font-bold text-slate-700">{{ $deposit->created_at->format('d M Y') }}</td>
                                        <td class="py-3.5 px-4">
                                            @if($deposit->collection_method === 'jemput')
                                                <span class="flex items-center gap-1.5"><i class="bi bi-truck text-emerald-600 text-sm"></i> Jemput</span>
                                            @else
                                                <span class="flex items-center gap-1.5"><i class="bi bi-person-walking text-slate-500 text-sm"></i> Antar</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                                @if($deposit->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-100
                                                @elseif($deposit->status === 'pending') bg-amber-50 text-amber-700 border border-amber-100
                                                @else bg-red-50 text-red-700 border border-red-100 @endif">
                                                {{ $deposit->status }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <button data-deposit="{{ json_encode($deposit) }}" onclick="openTrackingModal(this)" class="px-2.5 py-1 bg-stone-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 font-bold rounded-lg text-[10px] transition-all flex items-center gap-1 mx-auto border border-stone-200/50 shadow-sm cursor-pointer">
                                                <i class="bi bi-geo-alt"></i> Lacak
                                            </button>
                                        </td>
                                        <td class="py-3.5 pl-4 text-right font-black text-emerald-700 font-display">+{{ number_format($deposit->total_points, 0, ',', '.') }} Poin</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN (1/3 width - secondary info) --}}
        <div class="lg:col-span-1 space-y-8">

            {{-- Dampak Sosial Infographic Card --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100/50 rounded-3xl p-6 shadow-sm flex flex-col justify-between gap-6 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-24 h-24 rounded-full bg-emerald-400/5 pointer-events-none"></div>
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-slate-800 font-display">Dampak Sosial & Lingkungan</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lihat kontribusi nyata langkah ramah lingkunganmu dalam menyumbangkan reduksi karbon CO2 dan dukungan sirkular secara real-time.
                    </p>
                </div>
                <a href="{{ route('dampak.realtime') }}" class="w-full text-center px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-emerald-700/10 hover:shadow-md hover:shadow-emerald-700/20 hover:-translate-y-0.5 transform">
                    Buka Laporan Dampak
                </a>
            </div>

            {{-- Voucher Saya (Ticket styled coupon cards) --}}
            <div class="bg-white border border-stone-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest font-display mb-5 flex items-center gap-1.5">
                    <i class="bi bi-ticket-perforated text-emerald-600 text-sm"></i> Voucher Belanja Saya
                </h3>

                @if($vouchers->isEmpty())
                    <div class="text-center py-8 border border-dashed border-stone-100 rounded-2xl bg-stone-50/20">
                        <p class="text-xs text-slate-400 font-semibold mb-2">Belum ada voucher belanja.</p>
                        <a href="{{ route('warga.umkm') }}" class="text-emerald-600 font-bold text-xs hover:underline">Tukar Poin Sekarang</a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($vouchers as $voucher)
                            {{-- Paper ticket styled container --}}
                            <div class="relative flex justify-between items-center bg-stone-50/50 border border-slate-100 rounded-2xl p-4 overflow-hidden">
                                <!-- Notches -->
                                <div class="ticket-notch-top"></div>
                                <div class="ticket-notch-bottom"></div>

                                <!-- Left side of the ticket -->
                                <div class="min-w-0 pr-4 flex-1 border-r border-dashed border-slate-200">
                                    <div class="text-xs font-extrabold text-slate-800 truncate">{{ $voucher->product->name ?? 'Produk UMKM' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1 truncate">UMKM: {{ $voucher->product->partner->store_name ?? '-' }}</div>
                                    <div class="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded mt-2 inline-block">
                                        {{ $voucher->code }}
                                    </div>
                                </div>

                                <!-- Right side of the ticket (claim / status button) -->
                                <div class="pl-4 shrink-0 w-24 text-center">
                                    @if($voucher->status === 'unused')
                                        <button onclick="openVoucherQR('{{ $voucher->code }}', '{{ $voucher->product->name }}')" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-[10px] transition-all shadow-sm hover:shadow-md cursor-pointer">
                                            Scan QR
                                        </button>
                                    @else
                                        <span class="w-full block py-1.5 bg-slate-100 text-slate-400 rounded-lg text-[9px] font-bold uppercase tracking-wider">
                                            {{ $voucher->status }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Transaksi Pulsa & Tagihan --}}
            <div class="bg-white border border-stone-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest font-display mb-5 flex items-center gap-1.5">
                    <i class="bi bi-receipt text-emerald-600 text-sm"></i> Feed Transaksi
                </h3>

                @if($walletTransactions->isEmpty())
                    <div class="text-center py-8 border border-dashed border-stone-100 rounded-2xl bg-stone-50/20 text-xs text-slate-400 font-semibold">
                        Belum ada transaksi top-up atau tagihan.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($walletTransactions as $tx)
                            <div class="flex justify-between items-start gap-4 p-3 hover:bg-stone-50/40 rounded-xl transition-colors">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $tx->biller_name }}</div>
                                    <div class="text-[9px] text-slate-400 mt-1 uppercase tracking-wider">No: {{ $tx->account_number }}</div>
                                    <div class="text-[8px] text-slate-400 mt-0.5">Ref: {{ $tx->ref_number }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-xs font-black text-red-500 font-display">-{{ number_format($tx->points_spent, 0, ',', '.') }} Poin</div>
                                    <span class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-800 rounded-full text-[9px] font-bold mt-1">SUKSES</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL TARIK TUNAI --}}
<div id="withdrawModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-in">
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-cash-stack text-emerald-600"></i> Ajukan Tarik Tunai</h3>
            <button onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>
        <form action="{{ route('warga.withdraw') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Jumlah Poin Dicairkan</label>
                <input type="number" name="points_amount" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" placeholder="Min. 100" min="100" max="{{ $user->point_balance }}" required>
                <p class="text-[10px] text-slate-400 mt-1">1 Poin = Rp 10. Saldo poin Anda: {{ number_format($user->point_balance, 0, ',', '.') }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Nama Bank</label>
                <input type="text" name="bank_name" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" value="BCA" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Nomor Rekening</label>
                <input type="text" name="account_number" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" placeholder="Contoh: 1234567890" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Nama Pemilik Rekening</label>
                <input type="text" name="account_name" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" value="{{ $user->name }}" required>
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all shadow-md">
                Kirim Pengajuan
            </button>
        </form>
    </div>
</div>

{{-- MODAL BUKTI QR VOUCHER --}}
<div id="voucherModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-in">
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-qr-code text-emerald-600"></i> Kode QR Voucher Belanja</h3>
            <button onclick="closeVoucherQR()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>
        <div class="p-8 flex flex-col items-center text-center space-y-6">
            <div>
                <h4 id="modalVoucherName" class="font-bold text-slate-800 text-sm">Beras Premium 1 kg</h4>
                <p class="text-xs text-slate-400 mt-1">Tunjukkan kode QR di bawah ini ke Kasir UMKM Mitra</p>
            </div>

            {{-- Target QR Code --}}
            <div id="qrcode" class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex justify-center items-center"></div>

            <div class="space-y-1">
                <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">KODE KLAIM MANUAL</div>
                <div id="modalVoucherCode" class="text-lg font-mono font-bold text-emerald-600 bg-emerald-50 px-4 py-2 border border-emerald-100 rounded-xl tracking-wider">WL-XXXXXX</div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL LACAK LOKASI SAMPAH (SHIPMENT STYLE ROUTING MAP) --}}
<div id="trackingModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden animate-in">
        <div class="p-5 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-1.5">
                <i class="bi bi-geo-alt text-emerald-600"></i> Lacak Status & Aliran Sampah
            </h3>
            <button onclick="closeTrackingModal()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[75vh] overflow-y-auto">
            {{-- Kiri: Timeline Progress --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Aliran Pengiriman</h4>

                {{-- Vertical Timeline Stepper --}}
                <div class="relative pl-6 border-l-2 border-slate-100 space-y-5 ml-3" id="timelineContainer">
                    {{-- Injected dynamically via JS --}}
                </div>
            </div>

            {{-- Rute Perjalanan Sampah --}}
            <div class="space-y-4 flex flex-col">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rute Perjalanan Sampah</h4>
                <div id="trackingMap" class="w-full h-64 rounded-2xl border border-slate-100 relative z-10 shadow-inner"></div>
                <div class="text-[10px] text-slate-400 leading-relaxed font-semibold mt-1 flex items-center gap-1">
                    <i class="bi bi-info-circle text-emerald-600"></i>
                    <span id="trackingDistanceText">Menghitung rute...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// === LOGIKA GENERATE QR CODE NYATA ===
var qrInstance = null;
function openVoucherQR(code, name) {
    document.getElementById('modalVoucherName').textContent = name;
    document.getElementById('modalVoucherCode').textContent = code;

    document.getElementById('qrcode').innerHTML = '';

    qrInstance = new QRCode(document.getElementById('qrcode'), {
        text: code,
        width: 160,
        height: 160,
        colorDark: "#047857",
        colorLight: "#f8fafc",
        correctLevel: QRCode.CorrectLevel.H
    });

    document.getElementById('voucherModal').classList.remove('hidden');
}

function closeVoucherQR() {
    document.getElementById('voucherModal').classList.add('hidden');
}

// === LOGIKA TRACKING MAP & TIMELINE ===
let trackingMap = null;
let courierMarker = null;
let citizenMarker = null;
let bankMarker = null;
let routePolyline = null;

function openTrackingModal(button) {
    const depositJson = button.getAttribute('data-deposit');
    const deposit = JSON.parse(depositJson);

    document.getElementById('trackingModal').classList.remove('hidden');

    const timeline = document.getElementById('timelineContainer');
    timeline.innerHTML = '';

    const createdAt = new Date(deposit.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const verifiedAt = deposit.updated_at ? new Date(deposit.updated_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

    const isJemput = deposit.collection_method === 'jemput';
    const status = deposit.status;

    // Logika tahapan hierarki status
    const passedOtw = ['menuju_lokasi', 'ditimbang', 'approved', 'didistribusikan'].includes(status);
    const passedDitimbang = ['ditimbang', 'approved', 'didistribusikan'].includes(status);
    const passedApproved = ['approved', 'didistribusikan'].includes(status);
    const passedDistribusi = ['didistribusikan'].includes(status);
    const isRejected = status === 'rejected';

    let steps = [
        {
            title: 'Formulir Setoran Terkirim',
            desc: `Warga mengajukan setoran sampah via ${isJemput ? 'Jemput Kolektif' : 'Antar Mandiri'}.`,
            time: createdAt,
            completed: true,
            active: status === 'pending'
        },
        {
            title: isJemput ? 'Petugas Menuju Lokasi' : 'Warga Membawa Sampah',
            desc: isJemput ? 'Kurir bank sampah sedang berangkat menjemput ke titik koordinat Anda.' : 'Silakan antarkan sampah Anda ke drop-off point Bank Sampah Lestari.',
            time: passedOtw ? verifiedAt : (status === 'pending' ? 'Menunggu...' : ''),
            completed: passedOtw,
            active: status === 'menuju_lokasi' || (status === 'pending' && !isJemput)
        },
        {
            title: 'Tiba & Ditimbang Aktual',
            desc: 'Sampah berhasil diverifikasi, dipilah, dan ditimbang riil oleh operator bank sampah.',
            time: passedDitimbang ? verifiedAt : '',
            completed: passedDitimbang,
            active: status === 'ditimbang'
        },
        {
            title: 'Poin Dompet Dikreditkan',
            desc: `Tabungan poin +${deposit.total_points ? deposit.total_points.toLocaleString('id-ID') : '0'} Poin berhasil masuk ke dompet elektronik warga.`,
            time: passedApproved ? verifiedAt : '',
            completed: passedApproved,
            active: status === 'approved'
        },
        {
            title: 'Didistribusikan ke Industri Daur Ulang',
            desc: 'Sampah diangkut oleh mitra industri peleburan untuk dilebur menjadi bahan baku produk baru!',
            time: passedDistribusi ? 'Selesai' : '',
            completed: passedDistribusi,
            active: status === 'didistribusikan'
        }
    ];

    if (isRejected) {
        steps[2] = {
            title: 'Pengajuan Ditolak',
            desc: 'Pengajuan ditolak oleh petugas karena berat timbangan tidak sesuai atau bukan sampah terpilah.',
            time: verifiedAt,
            completed: true,
            active: true,
            failed: true
        };
        steps = steps.slice(0, 3);
    }

    steps.forEach((step, idx) => {
        let stepHtml = `
            <div class="relative">
                <div class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full flex items-center justify-center border-2 text-[8px] font-bold
                    ${step.failed ? 'bg-red-500 border-red-500 text-white' :
                      (step.completed ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-200')}">
                    ${step.failed ? '✕' : (step.completed ? '✓' : '')}
                </div>
                <div>
                    <h5 class="text-xs font-bold ${step.active ? 'text-slate-800' : 'text-slate-500'}">${step.title}</h5>
                    <p class="text-[10px] text-slate-400 mt-0.5 leading-relaxed">${step.desc}</p>
                    ${step.time ? `<span class="text-[9px] font-bold text-emerald-600 block mt-1">${step.time}</span>` : ''}
                </div>
            </div>
        `;
        timeline.innerHTML += stepHtml;
    });

    setTimeout(() => {
        initTrackingMap(deposit);
    }, 150);
}

function initTrackingMap(deposit) {
    var bankLat = -6.3024;
    var bankLng = 107.3065;

    var citizenLat = parseFloat(deposit.latitude) || -6.3000;
    var citizenLng = parseFloat(deposit.longitude) || 107.3000;

    if (!trackingMap) {
        trackingMap = L.map('trackingMap').setView([bankLat, bankLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(trackingMap);
    } else {
        if (citizenMarker) trackingMap.removeLayer(citizenMarker);
        if (bankMarker) trackingMap.removeLayer(bankMarker);
        if (courierMarker) trackingMap.removeLayer(courierMarker);
        if (routePolyline) trackingMap.removeLayer(routePolyline);
    }

    var citizenIcon = L.divIcon({
        html: '<div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white border-2 border-white shadow-md text-xs font-bold">🏠</div>',
        className: 'custom-div-icon',
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    var bankIcon = L.divIcon({
        html: '<div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white border-2 border-white shadow-md text-xs font-bold">🏢</div>',
        className: 'custom-div-icon',
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    citizenMarker = L.marker([citizenLat, citizenLng], { icon: citizenIcon }).addTo(trackingMap)
        .bindPopup("<b>Rumah Anda (Titik Jemput)</b>");

    bankMarker = L.marker([bankLat, bankLng], { icon: bankIcon }).addTo(trackingMap)
        .bindPopup("<b>Bank Sampah Lestari (Pusat)</b>");

    var latlngs = [
        [citizenLat, citizenLng],
        [bankLat, bankLng]
    ];
    routePolyline = L.polyline(latlngs, { color: '#10b981', weight: 4, dashArray: '6, 6' }).addTo(trackingMap);

    var distance = (trackingMap.distance([citizenLat, citizenLng], [bankLat, bankLng]) / 1000).toFixed(2);
    document.getElementById('trackingDistanceText').innerHTML = `<i class="bi bi-truck text-emerald-600"></i> Jarak ke Bank Sampah: <strong>${distance} km</strong>`;

    if (['pending', 'menuju_lokasi'].includes(deposit.status) && deposit.collection_method === 'jemput') {
        var courierIcon = L.divIcon({
            html: '<div class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-500 text-white border-2 border-white shadow-md text-sm">🚚</div>',
            className: 'custom-div-icon',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        var courierLat = (citizenLat + bankLat) / 2;
        var courierLng = (citizenLng + bankLng) / 2;

        courierMarker = L.marker([courierLat, courierLng], { icon: courierIcon }).addTo(trackingMap)
            .bindPopup("<b>Kurir Penjemput (Dalam Perjalanan)</b>");

        document.getElementById('trackingDistanceText').innerHTML += ` | Kurir sedang menuju lokasi Anda.`;
    }

    var group = new L.featureGroup([citizenMarker, bankMarker]);
    trackingMap.fitBounds(group.pad(0.3));
}

function closeTrackingModal() {
    document.getElementById('trackingModal').classList.add('hidden');
}
</script>
@endsection

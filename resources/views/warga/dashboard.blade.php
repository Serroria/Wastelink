@extends('layouts.app')
@section('title', 'Dasbor Warga — TIECO')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('content')
<div class="w-full space-y-6">

    {{-- Welcome header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Halo, {{ $user->name }}!</h1>
            <p class="text-xs text-slate-400">Selamat datang kembali di ekosistem sirkular TIECO.</p>
        </div>
        <span class="text-xs px-3 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-100 shadow-sm flex items-center gap-1.5"><i class="bi bi-patch-check-fill text-emerald-600"></i> Warga Aktif</span>
    </div>

    <div class="bg-emerald-600 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">

        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-[10px] font-semibold text-emerald-100 uppercase tracking-widest flex items-center gap-1">
                    Saldo Poin Tabungan
                    <i class="bi bi-info-circle text-[9px] text-emerald-200" title="1 Poin bernilai Rp 10. Dapatkan dari menyetorkan sampah terpilah."></i>
                </p>
                <h2 class="text-4xl font-black mt-1">{{ number_format($user->point_balance, 0, ',', '.') }} <span class="text-lg font-medium text-emerald-100">Poin</span></h2>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-semibold text-emerald-100 uppercase tracking-widest">Setara Rupiah</p>
                <p class="text-lg font-bold mt-1">Rp {{ number_format($user->point_balance * 10, 0, ',', '.') }}</p>
                <span class="text-[9px] text-emerald-100 bg-white/10 px-1.5 py-0.5 rounded">Kurs: 1 Poin = Rp 10</span>
            </div>
        </div>

        <div class="border-t border-white/10 pt-4 flex justify-between text-xs text-emerald-100">
            <div class="flex items-center gap-1.5"><i class="bi bi-scales"></i> Berat Sampah: <strong class="text-white">{{ number_format($totalWeight, 1, ',', '.') }} kg</strong></div>
            <div class="flex items-center gap-1.5"><i class="bi bi-journal-check"></i> Penyetoran: <strong class="text-white">{{ $totalDeposits }} Kali</strong></div>
        </div>
    </div>

    {{-- Grab-Style Services Grid --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Layanan Pengelolaan & Poin</h3>
        <div class="grid grid-cols-4 gap-4 text-center">
            <a href="{{ route('warga.setor') }}" class="group flex flex-col items-center">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white rounded-2xl flex items-center justify-center text-xl transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-emerald-600/20 mb-2">
                    <i class="bi bi-recycle"></i>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-emerald-600 transition-all">Setor Sampah</span>
                <span class="text-[9px] text-slate-400 mt-0.5 hidden sm:block">Kirim/jemput sampah</span>
            </a>

            <a href="{{ route('warga.bills') }}" class="group flex flex-col items-center">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white rounded-2xl flex items-center justify-center text-xl transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-emerald-600/20 mb-2">
                    <i class="bi bi-phone-vibrate"></i>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-emerald-600 transition-all">Pulsa & Tagihan</span>
                <span class="text-[9px] text-slate-400 mt-0.5 hidden sm:block">Token PLN & pulsa</span>
            </a>

            <a href="{{ route('warga.umkm') }}" class="group flex flex-col items-center">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white rounded-2xl flex items-center justify-center text-xl transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-emerald-600/20 mb-2">
                    <i class="bi bi-shop"></i>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-emerald-600 transition-all">Katalog UMKM</span>
                <span class="text-[9px] text-slate-400 mt-0.5 hidden sm:block">Tukar voucher belanja</span>
            </a>

            <button onclick="document.getElementById('withdrawModal').classList.remove('hidden')" class="group flex flex-col items-center w-full focus:outline-none">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white rounded-2xl flex items-center justify-center text-xl transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-emerald-600/20 mb-2">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-emerald-600 transition-all">Tarik Tunai</span>
                <span class="text-[9px] text-slate-400 mt-0.5 hidden sm:block">Cairkan saldo ke bank</span>
            </button>
        </div>
    </div>


    {{-- Vouchers & Transaction Feeds --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Vouchers List --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-ticket-perforated text-emerald-600 text-sm"></i> Voucher Saya</h3>
            @if($vouchers->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">
                    Belum ada voucher belanja. <a href="{{ route('warga.umkm') }}" class="text-emerald-600 font-semibold underline">Tukar sekarang</a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($vouchers as $voucher)
                        <div class="flex justify-between items-center p-3 border border-slate-50 rounded-xl hover:bg-slate-50/50 transition-all">
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ $voucher->product->name ?? 'Produk UMKM' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Toko: {{ $voucher->product->partner->store_name ?? '-' }}</div>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1 uppercase tracking-wider">Kode: <span class="text-emerald-600 font-mono">{{ $voucher->code }}</span></div>
                            </div>
                            <div class="text-right">
                                @if($voucher->status === 'unused')
                                    <button onclick="openVoucherQR('{{ $voucher->code }}', '{{ $voucher->product->name }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all shadow-sm shadow-emerald-600/10">
                                        Lihat QR Code
                                    </button>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-md text-[10px] font-semibold uppercase tracking-wider">{{ $voucher->status }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Wallet/Top Up Transactions Feed --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-receipt text-emerald-600 text-sm"></i> Transaksi Pulsa & Tagihan</h3>
            @if($walletTransactions->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">
                    Belum ada transaksi top-up atau tagihan.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($walletTransactions as $tx)
                        <div class="flex justify-between items-center p-3 border border-slate-50 rounded-xl">
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ $tx->biller_name }} ({{ ucfirst($tx->transaction_type) }})</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">No. Pelanggan: {{ $tx->account_number }}</div>
                                <div class="text-[9px] text-slate-400 mt-1">Ref: {{ $tx->ref_number }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-red-500">-{{ number_format($tx->points_spent, 0, ',', '.') }} Poin</div>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full text-[9px] font-bold">SUKSES</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Deposit History --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-clock-history text-emerald-600 text-sm"></i> Riwayat Penyetoran Sampah</h3>
        @if($deposits->isEmpty())
            <div class="text-center py-8 text-slate-400 text-sm">
                Belum ada pengajuan setoran sampah.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 pr-4">Tanggal</th>
                            <th class="pb-3 px-4">Metode</th>
                            <th class="pb-3 px-4">Status</th>
                            <th class="pb-3 px-4 text-center">Lacak Rute</th>
                            <th class="pb-3 pl-4 text-right">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($deposits as $deposit)
                            <tr class="text-slate-600">
                                <td class="py-3 pr-4 font-medium">{{ $deposit->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    @if($deposit->collection_method === 'jemput')
                                        <span class="flex items-center gap-1.5"><i class="bi bi-truck text-emerald-600"></i> Jemput Kolektif</span>
                                    @else
                                        <span class="flex items-center gap-1.5"><i class="bi bi-person-walking text-slate-500"></i> Antar Mandiri</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                        @if($deposit->status === 'approved') bg-emerald-50 text-emerald-700
                                        @elseif($deposit->status === 'pending') bg-amber-50 text-amber-700
                                        @else bg-red-50 text-red-700 @endif">
                                        {{ $deposit->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button data-deposit="{{ json_encode($deposit) }}" onclick="openTrackingModal(this)" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 font-bold rounded-lg text-[10px] transition-all flex items-center gap-1 mx-auto shadow-sm">
                                        <i class="bi bi-geo-alt"></i> Lacak
                                    </button>
                                </td>
                                <td class="py-3 pl-4 text-right font-bold text-emerald-600">+{{ number_format($deposit->total_points, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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

            {{-- Kanan: Peta Live Route --}}
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

// function openTrackingModal(button) {
//     const depositJson = button.getAttribute('data-deposit');
//     const deposit = JSON.parse(depositJson);
//     console.log("Tracking deposit:", deposit);

//     document.getElementById('trackingModal').classList.remove('hidden');

//     const timeline = document.getElementById('timelineContainer');
//     timeline.innerHTML = '';

//     const createdAt = new Date(deposit.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
//     const verifiedAt = deposit.updated_at ? new Date(deposit.updated_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

//     const isJemput = deposit.collection_method === 'jemput';
//     const isApproved = deposit.status === 'approved';
//     const isPending = deposit.status === 'pending';
//     const isRejected = deposit.status === 'rejected';

//     let steps = [
//         {
//             title: 'Formulir Setoran Terkirim',
//             desc: `Warga mengajukan setoran sampah via ${isJemput ? 'Jemput Kolektif' : 'Antar Mandiri'}.`,
//             time: createdAt,
//             completed: true,
//             active: isPending
//         },
//         {
//             title: isJemput ? 'Petugas Menuju Lokasi' : 'Warga Membawa Sampah',
//             desc: isJemput ? 'Kurir bank sampah sedang berangkat menjemput ke titik koordinat Anda.' : 'Silakan antarkan sampah Anda ke drop-off point Bank Sampah Lestari.',
//             time: isPending ? 'Sedang Berjalan' : verifiedAt,
//             completed: !isPending,
//             active: isPending
//         },
//         {
//             title: 'Tiba & Ditimbang Aktual',
//             desc: 'Sampah berhasil diverifikasi, dipilah, dan ditimbang riil oleh operator bank sampah.',
//             time: isApproved ? verifiedAt : '',
//             completed: isApproved,
//             active: isApproved
//         },
//         {
//             title: 'Poin Dompet Dikreditkan',
//             desc: `Tabungan poin +${deposit.total_points.toLocaleString('id-ID')} Poin berhasil masuk ke dompet elektronik warga.`,
//             time: isApproved ? verifiedAt : '',
//             completed: isApproved,
//             active: isApproved
//         },
//         {
//             title: 'Didistribusikan ke Industri Daur Ulang',
//             desc: 'Sampah diangkut oleh mitra industri peleburan untuk dilebur menjadi bahan baku produk baru!',
//             time: isApproved ? 'Selesai' : '',
//             completed: isApproved,
//             active: isApproved
//         }
//     ];

//     if (isRejected) {
//         steps[2] = {
//             title: 'Pengajuan Ditolak',
//             desc: 'Pengajuan ditolak oleh petugas karena berat timbangan tidak sesuai atau bukan sampah terpilah.',
//             time: verifiedAt,
//             completed: true,
//             active: true,
//             failed: true
//         };
//     }

//     steps.forEach((step, idx) => {
//         let stepHtml = `
//             <div class="relative">
//                 <div class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full flex items-center justify-center border-2 text-[8px] font-bold
//                     ${step.failed ? 'bg-red-500 border-red-500 text-white' :
//                       (step.completed ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-200')}">
//                     ${step.failed ? '✕' : (step.completed ? '✓' : '')}
//                 </div>
//                 <div>
//                     <h5 class="text-xs font-bold ${step.active ? 'text-slate-800' : 'text-slate-500'}">${step.title}</h5>
//                     <p class="text-[10px] text-slate-400 mt-0.5 leading-relaxed">${step.desc}</p>
//                     ${step.time ? `<span class="text-[9px] font-bold text-emerald-600 block mt-1">${step.time}</span>` : ''}
//                 </div>
//             </div>
//         `;
//         timeline.innerHTML += stepHtml;
//     });

//     setTimeout(() => {
//         initTrackingMap(deposit);
//     }, 150);
// }

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
        // Potong array (buang poin dan distribusi karena gagal ditimbang)
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


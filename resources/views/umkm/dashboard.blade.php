?@extends('layouts.app')
@section('title', 'Dasbor UMKM Mitra — TIECO')

@section('content')
<div class="w-full space-y-6">

    {{-- Welcome header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-shop text-emerald-600"></i> Dasbor Toko Mitra UMKM</h1>
            <p class="text-xs text-slate-400">
                @if($partner)
                    Toko: <strong>{{ $partner->store_name }}</strong> — Validasi voucher belanja warga dan lakukan pencairan klaim dana.
                @else
                    Anda belum terdaftar sebagai mitra UMKM.
                @endif
            </p>
        </div>
        <span class="text-xs px-3 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-100">🏪 UMKM Mode</span>
    </div>

    @if($partner)
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        
        {{-- VALIDASI VOUCHER --}}
        <div class="md:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center gap-1.5"><i class="bi bi-qr-code-scan text-emerald-600 text-sm"></i> Validasi Voucher Warga</h3>
            <div class="text-[11px] text-slate-500 bg-slate-50 border border-slate-100 rounded-2xl p-3.5 space-y-1.5">
                <div class="font-bold text-slate-700">Panduan Validasi:</div>
                <div class="flex gap-1.5"><span>1.</span><span>Warga menunjukkan kode voucher belanja (via HP/kertas).</span></div>
                <div class="flex gap-1.5"><span>2.</span><span>Ketik 8 digit kode voucher tersebut pada kolom di bawah.</span></div>
                <div class="flex gap-1.5"><span>3.</span><span>Tekan tombol validasi, lalu berikan produk/sembako kepada warga.</span></div>
            </div>
            
            <form action="{{ route('umkm.validate-voucher') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Kode Voucher</label>
                    <input type="text" name="code" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-center font-mono text-lg font-bold text-slate-800 focus:outline-none focus:border-emerald-500 uppercase tracking-widest bg-slate-50" placeholder="WL-XXXXXX" required>
                </div>
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md">
                    Validasi & Tukar Barang
                </button>
            </form>

            {{-- KLAIM SETTLEMENT --}}
            <div class="border-t border-slate-100 pt-5 space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-cash-stack text-emerald-600 text-sm"></i> Klaim Pencairan Dana</h4>
                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Ajukan pencairan dana rupiah ke operator Bank Sampah dari semua voucher warga yang telah berhasil ditukarkan di toko Anda.
                </p>
                <form action="{{ route('umkm.claim-settlement') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-1.5">
                        <i class="bi bi-send-check"></i> Ajukan Klaim Settlement
                    </button>
                </form>
            </div>
        </div>

        {{-- PRODUK CATALOG --}}
        <div class="md:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center gap-1.5"><i class="bi bi-journal-text text-emerald-600 text-sm"></i> Katalog Penukaran Toko</h3>
            
            @if($partner->products->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">
                    Belum ada produk terdaftar.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($partner->products as $product)
                        <div class="p-3 border border-slate-50 rounded-xl flex justify-between items-center text-xs">
                            <div>
                                <div class="font-bold text-slate-800">{{ $product->name }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $product->description }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-600 flex items-center gap-1"><i class="bi bi-gem"></i> {{ number_format($product->points_cost, 0, ',', '.') }} Poin</div>
                                <div class="text-[9px] text-slate-400 mt-0.5">Stok: {{ $product->stock }} pcs | Rp {{ number_format($product->price_value, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- RIWAYAT VOUCHER MASUK --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-clock-history text-slate-400 text-sm"></i> Riwayat Penukuran Voucher</h3>
        @if($vouchers->isEmpty())
            <div class="text-center py-8 text-slate-400 text-sm">
                Belum ada voucher belanja yang digunakan di toko Anda.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 pr-4">Kode Voucher</th>
                            <th class="pb-3 px-4">Warga</th>
                            <th class="pb-3 px-4">Produk</th>
                            <th class="pb-3 px-4">Poin</th>
                            <th class="pb-3 px-4">Status</th>
                            <th class="pb-3 pl-4 text-right">Tanggal Digunakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($vouchers as $v)
                            <tr>
                                <td class="py-3 pr-4 font-mono font-bold text-emerald-600">{{ $v->code }}</td>
                                <td class="py-3 px-4">{{ $v->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $v->product->name ?? '-' }}</td>
                                <td class="py-3 px-4 font-bold">{{ number_format($v->points_spent, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                        @if($v->status === 'claimed') bg-sky-50 text-sky-700
                                        @elseif($v->status === 'used') bg-emerald-50 text-emerald-700
                                        @else bg-slate-100 text-slate-500 @endif">
                                        {{ $v->status }}
                                    </span>
                                </td>
                                <td class="py-3 pl-4 text-right text-slate-400">{{ $v->used_at ? $v->used_at->format('d M Y, H:i') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- RIWAYAT SETTLEMENT --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-clock-history text-slate-400 text-sm"></i> Riwayat Pengajuan Klaim</h3>
        @if($settlements->isEmpty())
            <div class="text-center py-8 text-slate-400 text-sm">
                Belum ada riwayat klaim pencairan dana.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 pr-4">ID Klaim</th>
                            <th class="pb-3 px-4">Jumlah Dana Dicairkan</th>
                            <th class="pb-3 px-4">Total Voucher</th>
                            <th class="pb-3 px-4">Status Klaim</th>
                            <th class="pb-3 pl-4 text-right">Tanggal Settlement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($settlements as $s)
                            <tr>
                                <td class="py-3 pr-4 font-bold">#{{ $s->id }}</td>
                                <td class="py-3 px-4 font-black text-emerald-600">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    @php $vids = is_array($s->voucher_ids) ? $s->voucher_ids : json_decode($s->voucher_ids, true); @endphp
                                    {{ count($vids ?? []) }} Voucher Belanja
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                        @if($s->status === 'paid') bg-emerald-50 text-emerald-700
                                        @else bg-amber-50 text-amber-700 @endif">
                                        {{ $s->status }}
                                    </span>
                                </td>
                                <td class="py-3 pl-4 text-right text-slate-400">{{ $s->paid_at ? $s->paid_at->format('d M Y') : 'Menunggu Payout' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @else
    <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400 text-sm">
        🏪 Akun Anda belum didaftarkan sebagai mitra UMKM. Silakan hubungi operator Bank Sampah.
    </div>
    @endif
</div>
@endsection


?@extends('layouts.app')
@section('title', 'Marketplace B2B — TIECO')

@section('content')
<div class="w-full space-y-6">

    {{-- Welcome header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-cart3 text-emerald-600"></i> Marketplace B2B — Penjualan Sampah Daur Ulang</h1>
            <p class="text-xs text-slate-400">Jelajahi dan beli paket sampah terpilah kering berkapasitas besar siap angkut langsung dari gudang Bank Sampah.</p>
        </div>
        <span class="text-xs px-3 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-100">🏭 Buyer Mode</span>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-wallet2"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Saldo Kas Perusahaan</div>
            <div class="text-xl font-black text-emerald-600">Rp {{ number_format($user->cash_balance, 0, ',', '.') }}</div>
            <p class="text-[10px] text-slate-400 mt-1">sumber dana B2B</p>
        </div>
        
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-box-seam"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Listing Tersedia</div>
            <div class="text-xl font-black text-slate-800">{{ $listings->where('status', 'available')->count() }} Paket</div>
            <p class="text-[10px] text-slate-400 mt-1">siap dipesan</p>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-receipt"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pembelian Anda</div>
            <div class="text-xl font-black text-slate-800">{{ $listings->where('status', 'sold')->where('buyer_id', $user->id)->count() }} Paket</div>
            <p class="text-[10px] text-slate-400 mt-1">berhasil ditransaksikan</p>
        </div>
    </div>

    {{-- LISTING TERSEDIA --}}
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-grid-3x3-gap text-emerald-600 text-sm"></i> Listing Sampah Tersedia</h3>
        @php $available = $listings->where('status', 'available'); @endphp

        @if($available->isEmpty())
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400 text-sm">
                📦 Belum ada listing sampah tersedia saat ini.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($available as $listing)
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-emerald-200 transition-all">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-bold text-slate-800 pr-12">{{ $listing->title }}</h4>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[8px] font-bold rounded uppercase tracking-wider">Tersedia</span>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $listing->description }}</p>

                            {{-- Weight breakdown --}}
                            @php
                                $details = is_array($listing->weight_details) ? $listing->weight_details : json_decode($listing->weight_details, true);
                            @endphp
                            @if($details)
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach($details as $typeId => $weight)
                                        @php $wt = $wasteTypes[$typeId] ?? null; @endphp
                                        <span class="text-[10px] text-slate-600 bg-slate-50 border border-slate-100 px-2 py-1 rounded-lg flex items-center gap-1">
                                            <i class="bi bi-tag text-emerald-600"></i> {{ $wt->name ?? 'Lainnya' }}: <strong>{{ $weight }} kg</strong>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- B2B Logistic and warehouse info --}}
                            <div class="text-[10px] text-slate-500 space-y-1 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mt-3">
                                <div class="flex justify-between"><span>📍 Lokasi Gudang:</span><span class="font-bold text-slate-700">Kel. Cempaka Putih Timur</span></div>
                                <div class="flex justify-between"><span>📦 Kondisi Fisik:</span><span class="font-bold text-slate-700">Kering, Bersih & Terpilah</span></div>
                            </div>
                        </div>

                        <div class="border-t border-slate-50 mt-6 pt-4 flex justify-between items-center text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Harga Penawaran</span>
                                <div class="text-lg font-black text-emerald-600">Rp {{ number_format($listing->total_price, 0, ',', '.') }}</div>
                            </div>
                            
                            <form action="{{ route('pembeli.buy', $listing->id) }}" method="POST">
                                @csrf
                                @if($user->cash_balance >= $listing->total_price)
                                    <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        🛒 Beli Sekarang
                                    </button>
                                @else
                                    <button type="button" class="px-4 py-2.5 bg-slate-100 text-slate-400 font-bold rounded-xl text-xs cursor-not-allowed" disabled>
                                        Saldo Kurang
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- RIWAYAT PEMBELIAN --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-clock-history text-slate-400 text-sm"></i> Riwayat Pembayaran & Pembelian Saya</h3>
        @php $bought = $listings->where('buyer_id', $user->id); @endphp
        
        @if($bought->isEmpty())
            <div class="text-center py-8 text-slate-400 text-sm">
                Belum ada transaksi pembelian.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 pr-4">Nama Paket Listing</th>
                            <th class="pb-3 px-4">Deskripsi</th>
                            <th class="pb-3 px-4">Harga Terbayar</th>
                            <th class="pb-3 pl-4 text-right">Tanggal Pembelian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($bought as $b)
                            <tr>
                                <td class="py-3 pr-4 font-bold text-slate-800">{{ $b->title }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ Str::limit($b->description, 60) }}</td>
                                <td class="py-3 px-4 font-black text-emerald-600">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                                <td class="py-3 pl-4 text-right text-slate-400">{{ $b->sold_at ? $b->sold_at->format('d M Y, H:i') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection


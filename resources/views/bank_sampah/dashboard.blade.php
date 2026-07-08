@extends('layouts.app')
@section('title', 'Dasbor Operator — TIECO')

@section('content')
<div class="w-full space-y-6">

    {{-- Welcome header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-bank text-emerald-600"></i> Dasbor Bank Sampah Lestari</h1>
            <p class="text-xs text-slate-400">Kelola penyetoran sampah warga, rekapitulasi timbangan, dan atur kas operasional.</p>
        </div>
        <span class="text-xs px-3 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-100">👤 Operator Mode</span>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-box-arrow-in-down"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Setoran Menunggu</div>
            <div class="text-2xl font-black text-slate-800">{{ $pendingDeposits }}</div>
            <p class="text-[10px] text-slate-400 mt-1">perlu verifikasi timbangan</p>
        </div>
        
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-check2-circle"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Setoran</div>
            <div class="text-2xl font-black text-slate-800">{{ $approvedDeposits }}</div>
            <p class="text-[10px] text-slate-400 mt-1">transaksi disetujui</p>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-wallet2"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kas Kasir Utama</div>
            <div class="text-xl font-black text-emerald-600">Rp {{ number_format($stats->bank_sampah_cash ?? 0, 0, ',', '.') }}</div>
            <p class="text-[10px] text-slate-400 mt-1">dari penjualan B2B</p>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="text-2xl mb-2 text-emerald-600"><i class="bi bi-people"></i></div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Warga Terdaftar</div>
            <div class="text-2xl font-black text-slate-800">{{ $totalWarga }} KK</div>
            <p class="text-[10px] text-slate-400 mt-1">anggota aktif</p>
        </div>
    </div>

    {{-- Pending Action Items and Shortcuts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Alerts --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-exclamation-triangle text-amber-500 text-sm"></i> Tugas Peninjauan</h3>
            <div class="space-y-3">
                @if($pendingDeposits > 0)
                    <a href="{{ route('bank-sampah.verifikasi') }}" class="block p-4 bg-amber-50/50 border border-amber-100 hover:border-amber-200 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-xl text-amber-600"><i class="bi bi-box-arrow-in-down"></i></span>
                            <div>
                                <div class="text-xs font-bold text-amber-800">{{ $pendingDeposits }} Penyetoran Sampah Pending</div>
                                <p class="text-[10px] text-amber-600 mt-0.5">Lakukan verifikasi berat timbangan sampah dan setujui penambahan poin warga.</p>
                            </div>
                        </div>
                    </a>
                @endif

                @if($pendingWithdrawals > 0)
                    <a href="{{ route('bank-sampah.settlement') }}" class="block p-4 bg-sky-50/50 border border-sky-100 hover:border-sky-200 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-xl text-sky-600"><i class="bi bi-cash-stack"></i></span>
                            <div>
                                <div class="text-xs font-bold text-sky-800">{{ $pendingWithdrawals }} Pengajuan Cair Tunai</div>
                                <p class="text-[10px] text-sky-600 mt-0.5">Konfirmasi dan setujui transfer uang tunai untuk pencairan poin warga.</p>
                            </div>
                        </div>
                    </a>
                @endif

                @if($pendingSettlements > 0)
                    <a href="{{ route('bank-sampah.settlement') }}" class="block p-4 bg-purple-50/50 border border-purple-100 hover:border-purple-200 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-xl text-purple-600"><i class="bi bi-shop"></i></span>
                            <div>
                                <div class="text-xs font-bold text-purple-800">{{ $pendingSettlements }} Klaim Settlement UMKM</div>
                                <p class="text-[10px] text-purple-600 mt-0.5">Tinjau klaim dana dari UMKM mitra yang telah menerima voucher belanja warga.</p>
                            </div>
                        </div>
                    </a>
                @endif

                @if($pendingDeposits === 0 && $pendingWithdrawals === 0 && $pendingSettlements === 0)
                    <div class="text-center py-10 text-slate-400 text-xs flex flex-col items-center justify-center gap-2">
                        <i class="bi bi-check-circle-fill text-emerald-500 text-2xl"></i>
                        <span>Semua pekerjaan operasional telah selesai disetujui!</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Navigation Shortcuts --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-compass text-slate-400 text-sm"></i> Navigasi Menu</h3>
            <div class="space-y-3">
                <a href="{{ route('bank-sampah.verifikasi') }}" class="flex items-center justify-between p-3.5 border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/10 rounded-2xl transition-all text-xs font-semibold text-slate-700">
                    <span class="flex items-center gap-2"><i class="bi bi-patch-check text-emerald-600 text-sm"></i> Verifikasi Timbangan Sampah Warga</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('bank-sampah.stok') }}" class="flex items-center justify-between p-3.5 border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/10 rounded-2xl transition-all text-xs font-semibold text-slate-700">
                    <span class="flex items-center gap-2"><i class="bi bi-archive text-emerald-600 text-sm"></i> Kelola Stok Gudang & B2B Listing</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('bank-sampah.settlement') }}" class="flex items-center justify-between p-3.5 border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/10 rounded-2xl transition-all text-xs font-semibold text-slate-700">
                    <span class="flex items-center gap-2"><i class="bi bi-cash-stack text-emerald-600 text-sm"></i> Settlement Tagihan UMKM & Warga</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Feed --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-clock-history text-slate-400 text-sm"></i> Penyetoran Terbaru (Semua Warga)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="pb-3 pr-4">Warga</th>
                        <th class="pb-3 px-4">Tanggal</th>
                        <th class="pb-3 px-4">Metode</th>
                        <th class="pb-3 px-4">Detail Timbangan</th>
                        <th class="pb-3 px-4">Status</th>
                        <th class="pb-3 pl-4 text-right">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @foreach($recentDeposits as $deposit)
                        <tr>
                            <td class="py-3 pr-4 font-bold text-slate-800">{{ $deposit->user->name ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $deposit->created_at->format('d M Y') }}</td>
                            <td class="py-3 px-4">
                                @if($deposit->collection_method === 'jemput')
                                    <span class="flex items-center gap-1"><i class="bi bi-truck text-emerald-600"></i> Jemput</span>
                                @else
                                    <span class="flex items-center gap-1"><i class="bi bi-person-walking text-slate-500"></i> Antar</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $details = is_array($deposit->weight_details) ? $deposit->weight_details : json_decode($deposit->weight_details, true);
                                @endphp
                                @if($details)
                                    @foreach($details as $typeId => $weight)
                                        <span class="inline-block text-[10px] text-slate-500 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded-md mr-1 mb-1">
                                            {{ $wasteTypes[$typeId]->icon ?? '' }} {{ $wasteTypes[$typeId]->name ?? 'Lainnya' }}: {{ $weight }}kg
                                        </span>
                                    @endforeach
                                @else
                                    -
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
                            <td class="py-3 pl-4 text-right font-bold text-emerald-600">{{ number_format($deposit->total_points, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

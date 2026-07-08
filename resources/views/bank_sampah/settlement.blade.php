@extends('layouts.app')
@section('title', 'Settlement & Penarikan — TIECO')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Settlement Tagihan & Penarikan Dana</h1>
            <p class="text-xs text-slate-400">Bayar klaim penukaran saldo warga dan pencairan voucher sembako toko UMKM mitra.</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Saldo Kas Utama</p>
            <span class="text-sm font-black text-emerald-600">Rp {{ number_format($stats->bank_sampah_cash ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- PENARIKAN TUNAI WARGA --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Penarikan Tunai Warga</h3>

            @if($withdrawals->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">
                    Belum ada pengajuan penarikan tunai warga.
                </div>
            @else
                <div class="space-y-4 divide-y divide-slate-50">
                    @foreach($withdrawals as $w)
                        <div class="pt-4 {{ $loop->first ? 'pt-0' : '' }} flex justify-between items-center text-xs">
                            <div class="space-y-1">
                                <div class="font-bold text-slate-800">{{ $w->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-semibold">{{ $w->bank_name }} — {{ $w->account_number }}<br>A/N {{ $w->account_name }}</div>
                                <div class="text-[10px] text-emerald-600 font-bold">{{ number_format($w->points_amount, 0, ',', '.') }} Poin</div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-slate-800 mb-2">Rp {{ number_format($w->equivalent_rp, 0, ',', '.') }}</div>
                                @if($w->status === 'pending')
                                    <form action="{{ route('bank-sampah.settlement.withdraw', $w->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all">
                                             Bayar Klaim
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[9px] font-bold uppercase tracking-wider">LUNAS</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- KLAIM SETTLEMENT UMKM --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Klaim Settlement UMKM Mitra</h3>

            @if($settlements->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">
                    Belum ada pengajuan settlement dari mitra UMKM.
                </div>
            @else
                <div class="space-y-4 divide-y divide-slate-50">
                    @foreach($settlements as $s)
                        <div class="pt-4 {{ $loop->first ? 'pt-0' : '' }} flex justify-between items-center text-xs">
                            <div class="space-y-1">
                                <div class="font-bold text-slate-800">{{ $s->partner->store_name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">
                                    Kategori: {{ $s->partner->category ?? '-' }}
                                    @php $vids = is_array($s->voucher_ids) ? $s->voucher_ids : json_decode($s->voucher_ids, true); @endphp
                                    <br>Rekap: {{ count($vids ?? []) }} voucher voucher belanja
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-emerald-600 text-sm mb-2">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</div>
                                @if($s->status === 'pending')
                                    <form action="{{ route('bank-sampah.settlement.pay', $s->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all">
                                             Bayar Klaim
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-md text-[9px] font-bold uppercase tracking-wider">Dibayar</span>
                                    <p class="text-[8px] text-slate-400 mt-1">{{ $s->paid_at ? $s->paid_at->format('d/m/Y') : '' }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- </div> Ini adalah div penutup dari <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> --}}

    {{-- PANEL VERIFIKASI MITRA UMKM BARU --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 mt-5">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Verifikasi Kemitraan UMKM Baru</h3>

        @if(!isset($pendingPartners) || $pendingPartners->isEmpty())
            <div class="text-center py-12 text-slate-400 text-sm">
                Belum ada pengajuan kemitraan UMKM baru saat ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 pr-4">Nama Toko & Pemilik</th>
                            <th class="pb-3 px-4">Kategori</th>
                            <th class="pb-3 px-4">Alamat & Deskripsi</th>
                            <th class="pb-3 pl-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($pendingPartners as $p)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 pr-4">
                                    <div class="font-bold text-slate-800">{{ $p->store_name }}</div>
                                    <div class="text-[10px] text-slate-400">Pemilik: {{ $p->user->name ?? 'Tidak diketahui' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md font-bold uppercase tracking-wider text-[9px] border border-slate-200">
                                        {{ $p->category }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 max-w-xs">
                                    <div class="font-semibold text-slate-700 truncate" title="{{ $p->address }}"><i class="bi bi-geo-alt"></i> {{ $p->address }}</div>
                                    <div class="text-[10px] text-slate-400 truncate mt-0.5" title="{{ $p->description }}">{{ $p->description }}</div>
                                </td>
                                <td class="py-3 pl-4">
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('bank-sampah.umkm.approve', $p->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[10px] transition-all shadow-sm">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('bank-sampah.umkm.reject', $p->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-[10px] transition-all shadow-sm">
                                                ✕ Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div> {{-- Penutup tag <div class="w-full space-y-6"> yang paling atas --}}

@endsection

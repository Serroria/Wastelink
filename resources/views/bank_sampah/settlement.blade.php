?@extends('layouts.app')
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
                                            ✅ Bayar Klaim
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
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">🏪 Klaim Settlement UMKM Mitra</h3>

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
                                            ✅ Bayar Klaim
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
@endsection


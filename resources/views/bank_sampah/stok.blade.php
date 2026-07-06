?@extends('layouts.app')
@section('title', 'Stok & Marketplace B2B — TIECO')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">📦 Manajemen Stok Gudang & Penjualan B2B</h1>
            <p class="text-xs text-slate-400">Pantau akumulasi volume sampah terpilah di gudang dan pasang penawaran penjualan untuk industri daur ulang.</p>
        </div>
        <a href="{{ route('bank-sampah.dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-600">← Dasbor</a>
    </div>

    {{-- STOK TERKUMPUL --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">⚖️ Volume Stok di Gudang</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($wasteTypes as $type)
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-xl mb-1">{{ $type->icon }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $type->name }}</div>
                    <div class="text-sm font-black text-aslate-800">{{ number_format($stokPerType[$type->id] ?? 0, 1, ',', '.') }} kg</div>
                    <p class="text-[9px] text-emerald-600 font-semibold mt-1">Rp {{ number_format(($stokPerType[$type->id] ?? 0) * $type->price_per_kg, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        {{-- BUAT LISTING BARU --}}
        <div class="md:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">📤 Buat Penjualan Baru</h3>

            <form action="{{ route('bank-sampah.stok.listing') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Judul Listing</label>
                    <input type="text" name="title" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" placeholder="Contoh: Paket Plastik PET Bersih 50kg" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Deskripsi Detail</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" placeholder="Misal: Sudah dicuci, dipress rapi, siap angkut..."></textarea>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-semibold text-slate-400 uppercase">Rincian Berat Sampah (kg)</label>
                    @foreach($wasteTypes as $type)
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-600 w-28 truncate">{{ $type->icon }} {{ $type->name }}</span>
                            <input type="number" name="weights[{{ $type->id }}]" class="flex-1 px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 bg-white" placeholder="0" min="0" step="0.1" value="0">
                            <span class="text-[10px] text-slate-400 font-medium">kg</span>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md mt-4">
                    Posting ke B2B Marketplace
                </button>
            </form>
        </div>

        {{-- LISTING AKTIF & RIWAYAT --}}
        <div class="md:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">📋 Listing Jual & Riwayat</h3>

            @if($listings->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">
                    Belum ada postingan penjualan.
                </div>
            @else
                <div class="space-y-4 divide-y divide-slate-50">
                    @foreach($listings as $listing)
                        <div class="pt-4 {{ $loop->first ? 'pt-0' : '' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">{{ $listing->title }}</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $listing->description }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider
                                    @if($listing->status === 'available') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @else bg-slate-100 text-slate-500 @endif">
                                    {{ $listing->status }}
                                </span>
                            </div>

                            {{-- Weights breakdown --}}
                            @php
                                $details = is_array($listing->weight_details) ? $listing->weight_details : json_decode($listing->weight_details, true);
                            @endphp
                            @if($details)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($details as $typeId => $weight)
                                        @php $wt = $wasteTypes->firstWhere('id', $typeId); @endphp
                                        <span class="text-[9px] text-slate-500 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded">
                                            {{ $wt->icon ?? '' }} {{ $wt->name ?? 'Lainnya' }}: {{ $weight }} kg
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400">Total Harga Penjualan:</span>
                                    <div class="font-extrabold text-emerald-600 text-sm">Rp {{ number_format($listing->total_price, 0, ',', '.') }}</div>
                                </div>
                            {{-- Bagian Aksi & Status --}}
                                <div class="flex items-center gap-3">
                                    @if($listing->status === 'available')
                                        <form action="{{ route('bank-sampah.stok.cancel', $listing->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penjualan ini? Stok akan dikembalikan ke gudang.');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-bold rounded-lg text-[10px] transition-all">
                                                ✕ Batalkan
                                            </button>
                                        </form>
                                    @endif


                                @if($listing->sold_at)
                                    <span class="text-[9px] text-slate-400">Terjual pada {{ $listing->sold_at->format('d M Y') }}</span>
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


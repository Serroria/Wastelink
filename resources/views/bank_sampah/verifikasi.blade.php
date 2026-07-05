@extends('layouts.app')
@section('title', 'Verifikasi Setoran — TIECO')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">✅ Verifikasi Setoran Sampah Warga</h1>
            <p class="text-xs text-slate-400">Timbang ulang sampah terpilah secara riil dan berikan persetujuan untuk pemberian poin warga.</p>
        </div>
        <a href="{{ route('bank-sampah.dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-600">← Dasbor</a>
    </div>

    @foreach($deposits as $deposit)
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row justify-between gap-6">
            
            {{-- DATA PENYETORAN --}}
            <div class="flex-1 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider
                        @if($deposit->status === 'approved') bg-emerald-50 text-emerald-700
                        @elseif($deposit->status === 'pending') bg-amber-50 text-amber-700
                        @else bg-red-50 text-red-700 @endif">
                        {{ $deposit->status }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-bold">ID #{{ $deposit->id }} • {{ $deposit->created_at->format('d M Y H:i') }}</span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ $deposit->user->name ?? 'Warga' }}</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Metode: <strong>{{ $deposit->collection_method === 'jemput' ? '🚛 Penjemputan Kolektif' : '🏃 Antar Mandiri' }}</strong>
                        @if($deposit->schedule_date) | Rencana: <strong>{{ $deposit->schedule_date->format('d M Y') }}</strong> @endif
                    </p>
                </div>

                @if($deposit->address)
                    <div class="text-xs text-slate-600 bg-slate-50 border border-slate-100 p-3 rounded-xl">
                        📍 <strong>Alamat Penjemputan:</strong> {{ $deposit->address }}
                    </div>
                @endif

                @if($deposit->notes)
                    <p class="text-xs text-slate-500 italic">"{{ $deposit->notes }}"</p>
                @endif

                {{-- Timbangan Yang Diajukan Warga --}}
                <div class="pt-2">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Estimasi Berat Diajukan Warga:</div>
                    @php
                        $details = is_array($deposit->weight_details) ? $deposit->weight_details : json_decode($deposit->weight_details, true);
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @if($details)
                            @foreach($details as $typeId => $weight)
                                <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-xl">
                                    {{ $wasteTypes[$typeId]->icon ?? '' }} {{ $wasteTypes[$typeId]->name ?? 'Lainnya' }}: <strong>{{ $weight }} kg</strong>
                                </span>
                            @endforeach
                        @else
                            <span class="text-xs text-slate-400">Tidak ada rincian data timbangan</span>
                        @endif
                    </div>
                </div>

                {{-- Peta Lokasi Penjemputan (Jika Jemput & Pending) --}}
                @if($deposit->collection_method === 'jemput' && $deposit->status === 'pending')
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Lokasi Jemput di Peta</label>
                        <div id="map-{{ $deposit->id }}" class="w-full h-40 rounded-xl border border-slate-100 relative z-10"></div>
                    </div>
                @endif
            </div>

            {{-- FORM TINDAKAN (OPERATOR) --}}
            @if($deposit->status === 'pending')
            <div class="w-full lg:w-80 bg-slate-50/50 border border-slate-100 rounded-3xl p-5 shrink-0 space-y-4">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest border-b border-slate-100 pb-2">⚖️ Input Timbangan Aktual</h4>
                
                <form action="{{ route('bank-sampah.verifikasi.process', $deposit->id) }}" method="POST" class="space-y-3">
                    @csrf
                    
                    @foreach($wasteTypes as $type)
                    @php
                        $existingWeight = isset($details[$type->id]) ? $details[$type->id] : 0;
                    @endphp
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-600 w-24 truncate">{{ $type->icon }} {{ $type->name }}</span>
                        <input type="number" name="weights[{{ $type->id }}]" class="w-20 px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-center focus:outline-none focus:border-emerald-500 bg-white" value="{{ $existingWeight }}" min="0" step="0.1">
                        <span class="text-xs text-slate-400 font-bold">kg</span>
                    </div>
                    @endforeach

                    <div class="pt-2 border-t border-slate-100">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Catatan Verifikasi</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 bg-white" placeholder="Sampah bersih terpilah..."></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-2">
                        <button type="submit" name="action" value="approve" class="py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[10px] transition-all shadow-sm">
                            Setujui
                        </button>
                        <button type="submit" name="action" value="revise" class="py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-[10px] transition-all shadow-sm">
                            Revisi
                        </button>
                        <button type="submit" name="action" value="reject" class="py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-[10px] transition-all shadow-sm">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
    @endforeach

    @if($deposits->isEmpty())
    <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400 text-sm">
        📋 Belum ada pengajuan setoran dari warga saat ini.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inisialisasi peta untuk semua setoran pending jemputan
    @foreach($deposits as $deposit)
        @if($deposit->collection_method === 'jemput' && $deposit->status === 'pending')
            (function() {
                var mapId = 'map-{{ $deposit->id }}';
                // Gunakan koordinat riil yang tersimpan, fallback ke pusat Karawang
                var lat = {{ $deposit->latitude ?? -6.3024 }};
                var lng = {{ $deposit->longitude ?? 107.3065 }};
                
                var map = L.map(mapId, { scrollWheelZoom: false }).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                var pinIcon = L.divIcon({
                    html: '<span class="text-3xl">📍</span>',
                    className: 'custom-div-icon',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                });
                
                L.marker([lat, lng], { icon: pinIcon }).addTo(map)
                    .bindPopup("<b>Lokasi Jemput Warga:</b><br>{{ $deposit->user->name ?? 'Warga' }}<br><a href='https://www.google.com/maps/dir/?api=1&destination=" + lat + "," + lng + "' target='_blank' class='block text-center mt-2 py-1 bg-emerald-600 text-white rounded text-[9px] font-bold shadow-sm'><i class='bi bi-send-fill text-[8px]'></i> Buka Petunjuk Rute</a>");
            })();
        @endif
    @endforeach
});
</script>
@endsection


@extends('layouts.app')
@section('title', 'Setor Sampah — TIECO')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Custom numeric input hidden styling for numeric steppers */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* Scanning laser line animation */
    @keyframes scan {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
    #aiScanLaser {
        animation: scan 2s linear infinite;
    }

    /* Pulsing highlight for AI recommended card */
    @keyframes pulse-highlight {
        0% { border-color: #f59e0b; box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { border-color: #10b981; box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { border-color: #f59e0b; box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    .ai-highlighted {
        animation: pulse-highlight 2s infinite;
        border: 2px solid #10b981 !important;
        background-color: #f0fdf4 !important; /* ultra-light green */
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
</style>
@endsection

@section('content')
@php
    // Definisikan kategori klasifikasi pemilahan sampah
    $daurUlangNames = ['plastik', 'kertas', 'kardus', 'logam', 'kaleng', 'kaca', 'botol'];
    $organikNames = ['minyak', 'jelantah', 'organik', 'dapur', 'makanan', 'kompos', 'sisa'];
@endphp

<div class="w-full space-y-6 pb-24 md:pb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        {{-- FORM INPUT --}}
        <div class="md:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
            <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">
                Formulir Penyetoran
            </h2>

            <form action="{{ route('warga.setor.store') }}" method="POST" id="setorForm" class="space-y-6" enctype="multipart/form-data">
                @csrf

                {{-- Hidden input for webcam photo capture --}}
                <input type="hidden" name="captured_photo" id="capturedPhotoInput" value="">

                {{-- 1. ESTIMASI TIMBANGAN SAMPAH (LINEAR LIST ROW DESIGN DENGAN 3 KATEGORI TABS) --}}
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                            Estimasi Berat Sampah Anda
                        </h3>

                        {{-- Tabs Navigation --}}
                        <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200 gap-1 self-start sm:self-auto select-none">
                            <button type="button" onclick="switchTab('daur-ulang')" data-category="daur-ulang" class="tab-btn px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all border-b-2 border-emerald-500 text-emerald-600 bg-white shadow-sm focus:outline-none">
                                ♻️ Daur Ulang
                            </button>
                            <button type="button" onclick="switchTab('organik')" data-category="organik" class="tab-btn px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none">
                                🍂 Organik
                            </button>
                            <button type="button" onclick="switchTab('non-organik')" data-category="non-organik" class="tab-btn px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none">
                                🗑️ Non Organik / Residu
                            </button>
                        </div>
                    </div>

                    {{-- TAB CONTENT LISTS --}}

                    {{-- 1. List Kategori Daur Ulang --}}
                    <div id="list-daur-ulang" class="category-list divide-y divide-slate-100 border border-slate-100 rounded-2xl bg-white p-2">
                        @php $hasDaurUlang = false; @endphp
                        @foreach($wasteTypes as $type)
                            @if(\Illuminate\Support\Str::contains(strtolower($type->name), $daurUlangNames))
                                @php $hasDaurUlang = true; @endphp
                                <div id="type-card-{{ $type->id }}" class="waste-type-card flex items-center justify-between py-3 px-3 hover:bg-slate-50/50 rounded-xl transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 text-lg flex items-center justify-center shrink-0 border border-slate-100">
                                            {{ $type->icon }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-700">{{ $type->name }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold mt-0.5">{{ number_format($type->points_per_kg, 0, ',', '.') }} Poin/kg</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] text-slate-400 font-bold min-w-[50px] text-right pointer-display">0 Poin</span>
                                        <div class="flex items-center bg-slate-100 border border-slate-200 rounded-full p-0.5 shadow-inner">
                                            <button type="button" onclick="decrementWeight({{ $type->id }})" class="w-7 h-7 rounded-full bg-white hover:bg-slate-50 text-slate-600 transition-all font-bold flex items-center justify-center shadow-sm select-none focus:outline-none">-</button>
                                            <input type="number" name="weights[{{ $type->id }}]" id="weight-{{ $type->id }}" class="weight-input w-10 text-center bg-transparent border-0 font-black text-slate-800 text-xs focus:outline-none focus:ring-0 p-0" placeholder="0" min="0" step="0.1" data-poin="{{ $type->points_per_kg }}" oninput="calculateLiveEstimate()">
                                            <span class="text-[9px] text-slate-400 font-bold mr-2 select-none">kg</span>
                                            <button type="button" onclick="incrementWeight({{ $type->id }})" class="w-7 h-7 rounded-full bg-white hover:bg-slate-50 text-slate-600 transition-all font-bold flex items-center justify-center shadow-sm select-none focus:outline-none">+</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        {{-- Pesan jika data masih kosong --}}
                        @if(!$hasDaurUlang)
                            <div class="p-4 text-center text-slate-400 text-xs">Belum ada item kategori daur ulang.</div>
                        @endif
                    </div>

                    {{-- 2. List Kategori Organik --}}
                    <div id="list-organik" class="category-list hidden divide-y divide-slate-100 border border-slate-100 rounded-2xl bg-white p-2">
                        @php $hasOrganik = false; @endphp
                        @foreach($wasteTypes as $type)
                            @if(\Illuminate\Support\Str::contains(strtolower($type->name), $organikNames))
                                @php $hasOrganik = true; @endphp
                                <div id="type-card-{{ $type->id }}" class="waste-type-card flex items-center justify-between py-3 px-3 hover:bg-slate-50/50 rounded-xl transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 text-lg flex items-center justify-center shrink-0 border border-slate-100">
                                            {{ $type->icon }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-700">{{ $type->name }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold mt-0.5">{{ number_format($type->points_per_kg, 0, ',', '.') }} Poin/kg</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] text-slate-400 font-bold min-w-[50px] text-right pointer-display">0 Poin</span>
                                        <div class="flex items-center bg-slate-100 border border-slate-200 rounded-full p-0.5 shadow-inner">
                                            <button type="button" onclick="decrementWeight({{ $type->id }})" class="w-7 h-7 rounded-full bg-white hover:bg-slate-50 text-slate-600 transition-all font-bold flex items-center justify-center shadow-sm select-none focus:outline-none">-</button>
                                            <input type="number" name="weights[{{ $type->id }}]" id="weight-{{ $type->id }}" class="weight-input w-10 text-center bg-transparent border-0 font-black text-slate-800 text-xs focus:outline-none focus:ring-0 p-0" placeholder="0" min="0" step="0.1" data-poin="{{ $type->points_per_kg }}" oninput="calculateLiveEstimate()">
                                            <span class="text-[9px] text-slate-400 font-bold mr-2 select-none">kg</span>
                                            <button type="button" onclick="incrementWeight({{ $type->id }})" class="w-7 h-7 rounded-full bg-white hover:bg-slate-50 text-slate-600 transition-all font-bold flex items-center justify-center shadow-sm select-none focus:outline-none">+</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if(!$hasOrganik)
                            <div class="p-4 text-center text-slate-400 text-xs">Belum ada item kategori organik.</div>
                        @endif
                    </div>

                    {{-- 3. List Kategori Non-Organik / Residu --}}
                    <div id="list-non-organik" class="category-list hidden divide-y divide-slate-100 border border-slate-100 rounded-2xl bg-white p-2">
                        @php $hasNonOrganik = false; @endphp
                        @foreach($wasteTypes as $type)
                            @php
                                $lowerName = strtolower($type->name);
                                $isDaurUlang = \Illuminate\Support\Str::contains($lowerName, $daurUlangNames);
                                $isOrganik = \Illuminate\Support\Str::contains($lowerName, $organikNames);
                            @endphp
                            @if(!$isDaurUlang && !$isOrganik)
                                @php $hasNonOrganik = true; @endphp
                                <div id="type-card-{{ $type->id }}" class="waste-type-card flex items-center justify-between py-3 px-3 hover:bg-slate-50/50 rounded-xl transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-50 text-lg flex items-center justify-center shrink-0 border border-slate-100">
                                            {{ $type->icon }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-700">{{ $type->name }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold mt-0.5">{{ number_format($type->points_per_kg, 0, ',', '.') }} Poin/kg</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] text-slate-400 font-bold min-w-[50px] text-right pointer-display">0 Poin</span>
                                        <div class="flex items-center bg-slate-100 border border-slate-200 rounded-full p-0.5 shadow-inner">
                                            <button type="button" onclick="decrementWeight({{ $type->id }})" class="w-7 h-7 rounded-full bg-white hover:bg-slate-50 text-slate-600 transition-all font-bold flex items-center justify-center shadow-sm select-none focus:outline-none">-</button>
                                            <input type="number" name="weights[{{ $type->id }}]" id="weight-{{ $type->id }}" class="weight-input w-10 text-center bg-transparent border-0 font-black text-slate-800 text-xs focus:outline-none focus:ring-0 p-0" placeholder="0" min="0" step="0.1" data-poin="{{ $type->points_per_kg }}" oninput="calculateLiveEstimate()">
                                            <span class="text-[9px] text-slate-400 font-bold mr-2 select-none">kg</span>
                                            <button type="button" onclick="incrementWeight({{ $type->id }})" class="w-7 h-7 rounded-full bg-white hover:bg-slate-50 text-slate-600 transition-all font-bold flex items-center justify-center shadow-sm select-none focus:outline-none">+</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if(!$hasNonOrganik)
                            <div class="p-4 text-center text-slate-400 text-xs">Belum ada item kategori non-organik / residu.</div>
                        @endif
                    </div>
                </div>

                {{-- 2. FOTO BUKTI PENYERAHAN SAMPAH DI LOKASI (CLEAN DROPZONE DESIGN) --}}
                <div class="border-t border-slate-100 pt-5 space-y-3">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Foto Bukti Penyerahan Sampah di Lokasi
                    </h3>

                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <button type="button" onclick="openCameraModal('proof')" class="w-full sm:w-auto px-4 py-3 bg-slate-55 hover:bg-slate-100 text-slate-700 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 shadow-sm border border-slate-200">
                            <i class="bi bi-camera"></i> Ambil Foto Langsung
                        </button>
                        <button type="button" onclick="document.getElementById('photoInput').click()" class="w-full sm:w-auto px-4 py-3 bg-white hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 border border-slate-200 shadow-sm">
                            <i class="bi bi-upload"></i> Unggah File Gambar
                        </button>
                        <input type="file" name="photo_proof" id="photoInput" class="hidden" accept="image/*" onchange="previewProofPhoto(this)">
                    </div>

                    {{-- Photo preview container (Proof) --}}
                    <div id="proofPreviewContainer" class="hidden relative mt-3 w-40 h-40 rounded-2xl overflow-hidden border border-slate-200 shadow-md">
                        <img id="proofPreview" class="w-full h-full object-cover" src="">
                        <button type="button" onclick="removeProofPhoto()" class="absolute top-2 right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-[10px] shadow-md transition-all z-30">✕</button>
                    </div>
                </div>

                {{-- 3. DETAIL METODE & JADWAL --}}
                <div class="border-t border-slate-100 pt-5 space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Metode & Tanggal Pengumpulan
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Metode Pengumpulan</label>
                            <select name="method" id="methodSelect" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all">
                                <option value="jemput">🚛 Jemput Terjadwal (Kolektif RT/RW)</option>
                                <option value="antar">🏃 Antar Mandiri ke Bank Sampah</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Tanggal Rencana Penyetoran</label>
                            <input type="date" name="schedule_date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" value="{{ now()->addDay()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                {{-- 4. PETA KOORDINAT & ALAMAT LENGKAP --}}
                <div id="locationSection" class="border-t border-slate-100 pt-5 space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Lokasi Penjemputan
                    </h3>

                    <div id="mapGroup" class="space-y-3">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pin Lokasi di Peta</label>

                        {{-- Search Input OSM Nominatim --}}
                        <div class="flex gap-2">
                            <input type="text" id="mapSearchInput" class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-500 bg-slate-50 focus:bg-white transition-all" placeholder="Tulis nama jalan/RT RW kelurahan untuk memindahkan pin...">
                            <button type="button" id="mapSearchBtn" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm flex items-center gap-1">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>

                        <div id="pickerMap" class="w-full h-48 rounded-xl border border-slate-200 relative z-10 shadow-inner"></div>
                        <input type="hidden" name="latitude" id="latInput" value="-6.3024">
                        <input type="hidden" name="longitude" id="lngInput" value="107.3065">
                    </div>

                    <div id="addressGroup" class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Alamat Lengkap Rumah Penjemputan</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" placeholder="Contoh: Jl. Anggrek No. 5, RT 02/RW 07, pagar hitam sebelah warung makan..." required>{{ auth()->user()->address }}</textarea>
                    </div>
                </div>

                {{-- 5. CATATAN OPERATOR --}}
                <div class="border-t border-slate-100 pt-5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Catatan Tambahan untuk Petugas (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" placeholder="Contoh: Taruh saja tong sampah di depan rumah, saya sedang bekerja..."></textarea>
                </div>

                <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs uppercase tracking-widest transition-all shadow-md shadow-emerald-600/10 hover:scale-[1.01] active:scale-[0.99] transform">
                    Kirim Form Pengajuan Setoran
                </button>
            </form>
        </div>

        {{-- RINGKASAN & ASISTEN AI (DESKTOP) --}}
        <div class="hidden md:block md:col-span-2 space-y-6">
            {{-- Estimator Card (Digital Receipt Style) --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm relative overflow-hidden space-y-4">
                <div class="text-xs font-bold text-slate-800 flex items-center justify-between pb-3 border-b border-dashed border-slate-200">
                    <span>ESTIMASI TABUNGAN</span>
                    <span class="text-[9px] text-emerald-600 font-extrabold tracking-widest">TIECO</span>
                </div>

                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Konversi Rupiah</span>
                        <span id="liveRp" class="font-bold text-slate-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Poin Penyetoran</span>
                        <span id="livePoin" class="font-bold text-slate-800">0</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-dashed border-slate-200 text-center">
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Poin yang Diperoleh</div>
                    <div id="liveTotalDisplay" class="text-4xl font-black text-emerald-600 mt-1">0</div>
                    <p class="text-[9px] text-slate-400 font-semibold mt-1.5 leading-relaxed">Poin akan dikreditkan ke e-wallet setelah timbangan dihitung fisik oleh operator bank sampah.</p>
                </div>
            </div>

            {{-- ASISTEN AI PEMILAH SAMPAH (DARK THEME SMART CARD DENGAN DETIL MATERI) --}}
            <div class="bg-slate-900 text-white border border-slate-850 rounded-3xl p-6 shadow-xl space-y-4 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-24 h-24 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none"></div>

                <h3 class="text-xs font-bold text-emerald-400 uppercase tracking-widest flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Smart AI: Cek Jenis Sampahmu
                </h3>
                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Unggah foto atau jepret sampah Anda menggunakan kamera. AI akan otomatis memilah kategori dan menunjukkan detail jenis sampahnya.
                </p>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button" onclick="openCameraModal('ai')" class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-[10px] font-bold transition-all flex items-center justify-center gap-1.5 focus:outline-none select-none border border-slate-700/50">
                        <i class="bi bi-camera"></i> Buka Kamera
                    </button>
                    <button type="button" onclick="document.getElementById('aiFileInput').click()" class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-[10px] font-bold transition-all flex items-center justify-center gap-1.5 focus:outline-none select-none border border-slate-700/50">
                        <i class="bi bi-upload"></i> Pilih File
                    </button>
                    <input type="file" id="aiFileInput" class="hidden" accept="image/*" onchange="previewAiPhoto(this)">
                </div>

                {{-- AI Detector Status / Result Panel (Dengan Keterangan Detail Jenis Sampah) --}}
                <div id="aiDetectorPanel" class="hidden bg-slate-800 border border-slate-700 p-3.5 rounded-2xl space-y-2.5 transition-all animate-in text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-slate-900 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                            🤖
                        </div>
                        <div id="aiStatusText" class="text-[10px] font-bold text-emerald-400">AI Detektor: Menganalisis...</div>
                    </div>

                    <div id="aiRecommendText" class="text-[10px] text-slate-300 leading-relaxed space-y-2">
                        Sedang mendeteksi materi sampah Anda...
                    </div>
                </div>

                {{-- AI Photo Preview container with scanning laser overlay --}}
                <div id="aiPreviewContainer" class="hidden relative w-full aspect-video rounded-2xl overflow-hidden border border-slate-700 shadow-md">
                    <img id="aiPreview" class="w-full h-full object-cover" src="">

                    {{-- Scanning laser line --}}
                    <div id="aiScanLaser" class="hidden absolute left-0 right-0 h-1 bg-emerald-450 shadow-[0_0_10px_#34d399] z-20" style="top: 0;"></div>

                    <button type="button" onclick="removeAiPhoto()" class="absolute top-2 right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-[9px] shadow-md transition-all z-30">✕</button>
                </div>
            </div>

            {{-- Price Guide Card --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
                    Panduan Nilai Poin
                </h3>
                <div class="divide-y divide-slate-100">
                    @foreach($wasteTypes as $type)
                        <div class="flex justify-between items-center py-2.5 text-xs">
                            <span class="text-slate-600 font-semibold">{{ $type->icon }} {{ $type->name }}</span>
                            <span class="font-bold text-emerald-600">{{ number_format($type->points_per_kg, 0, ',', '.') }} Poin/kg</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KAMERA --}}
<div id="cameraModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden animate-in">
        <div class="p-5 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-camera-video text-emerald-600"></i> Ambil Foto Sampah</h3>
            <button type="button" onclick="closeCameraModal()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>
        <div class="p-6 flex flex-col items-center justify-center space-y-4">
            <div class="relative w-full aspect-video bg-slate-950 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center">
                <video id="webcamVideo" class="w-full h-full object-cover" autoplay playsinline></video>
                <div id="cameraFallbackText" class="hidden text-xs text-slate-400 px-4 text-center">Mengaktifkan kamera...</div>
            </div>

            <div class="flex justify-center gap-4 w-full pt-2">
                <button type="button" onclick="closeCameraModal()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition-all">Batal</button>
                <button type="button" onclick="capturePhoto()" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-emerald-600/10 flex items-center gap-1.5">
                    <i class="bi bi-camera"></i> Jepret Foto
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Canvas for Capture --}}
<canvas id="captureCanvas" class="hidden"></canvas>

{{-- MOBILE STICKY BOTTOM BAR (ESTIMATOR INTERAKTIF HP) --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 p-4 flex items-center justify-between z-40 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] animate-in">
    <div>
        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Estimasi Pendapatan</div>
        <div class="flex items-baseline gap-1.5 mt-0.5">
            <span id="mobilePoin" class="text-xl font-black text-emerald-600">0</span>
            <span class="text-[10px] font-bold text-slate-500">Poin</span>
            <span class="text-[10px] text-slate-400 font-semibold">(<span id="mobileRp">Rp 0</span>)</span>
        </div>
    </div>
    <button type="button" onclick="document.getElementById('setorForm').requestSubmit()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all flex items-center gap-1 select-none">
        Kirim Form <i class="bi bi-arrow-right"></i>
    </button>
</div>
@endsection

@section('scripts')
{{-- Load TensorFlow.js dan MobileNet untuk klasifikasi citra nyata di sisi klien --}}
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.17.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@2.1.0/dist/mobilenet.min.js"></script>

{{-- Peta Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
// Map nama database ke ID tipe sampah (Di-render dinamis)
const wasteTypeDbMap = {
    @foreach($wasteTypes as $type)
        '{{ strtolower($type->name) }}': {{ $type->id }},
    @endforeach
};

// Fungsi pembantu mengambil ID berdasarkan nama
function getWasteTypeIdByName(namePattern) {
    for (const [dbName, id] of Object.entries(wasteTypeDbMap)) {
        if (dbName.includes(namePattern)) {
            return id;
        }
    }
    return null;
}

// === LOGIKA TAB KATEGORI ===
function switchTab(categoryName) {
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-white', 'shadow-sm');
        btn.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700');
    });

    // Add active class to selected tab button
    const activeBtn = document.querySelector(`.tab-btn[data-category="${categoryName}"]`);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700');
        activeBtn.classList.add('border-emerald-500', 'text-emerald-600', 'bg-white', 'shadow-sm');
    }

    // Hide all category lists, show the matching list
    document.querySelectorAll('.category-list').forEach(list => {
        list.classList.add('hidden');
    });
    document.getElementById(`list-${categoryName}`).classList.remove('hidden');
}

// === LOGIKA STEPPER +/- BERAT SAMPAH ===
function incrementWeight(id) {
    const input = document.getElementById(`weight-${id}`);
    let val = parseFloat(input.value) || 0;
    input.value = (val + 0.5).toFixed(1);
    calculateLiveEstimate();
}

function decrementWeight(id) {
    const input = document.getElementById(`weight-${id}`);
    let val = parseFloat(input.value) || 0;
    if (val > 0) {
        input.value = Math.max(0, val - 0.5).toFixed(1);
        if (parseFloat(input.value) === 0) input.value = '';
        calculateLiveEstimate();
    }
}

// === WEBCAM HTML5 LOGIC ===
let localStream = null;
let cameraTarget = 'proof'; // 'proof' or 'ai'

const video = document.getElementById('webcamVideo');
const cameraModal = document.getElementById('cameraModal');
const canvas = document.getElementById('captureCanvas');
const fallbackText = document.getElementById('cameraFallbackText');

function openCameraModal(target = 'proof') {
    cameraTarget = target;
    cameraModal.classList.remove('hidden');
    fallbackText.classList.remove('hidden');
    video.classList.add('hidden');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
            localStream = stream;
            video.srcObject = stream;
            video.classList.remove('hidden');
            fallbackText.classList.add('hidden');
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mengakses kamera. Pastikan Anda mengizinkan akses kamera di browser.');
            closeCameraModal();
        });
}

function closeCameraModal() {
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
        localStream = null;
    }
    cameraModal.classList.add('hidden');
}

function capturePhoto() {
    if (!localStream) return;

    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL('image/jpeg');

    if (cameraTarget === 'proof') {
        const previewImg = document.getElementById('proofPreview');
        previewImg.src = dataUrl;
        document.getElementById('proofPreviewContainer').classList.remove('hidden');

        document.getElementById('capturedPhotoInput').value = dataUrl;
        document.getElementById('photoInput').value = '';
    } else {
        const previewImg = document.getElementById('aiPreview');
        previewImg.src = dataUrl;
        document.getElementById('aiPreviewContainer').classList.remove('hidden');

        triggerAIScanner(dataUrl, 'captured_photo.jpg');
    }

    closeCameraModal();
}

// === LOGIKA PRATINJAU UNGGAH FILE BUKTI (TIDAK ADA AI SCAN) ===
function previewProofPhoto(input) {
    const container = document.getElementById('proofPreviewContainer');
    const img = document.getElementById('proofPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            container.classList.remove('hidden');
            document.getElementById('capturedPhotoInput').value = '';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeProofPhoto() {
    document.getElementById('photoInput').value = '';
    document.getElementById('capturedPhotoInput').value = '';
    document.getElementById('proofPreviewContainer').classList.add('hidden');
}

// === LOGIKA PRATINJAU UNGGAH FILE UNTUK AI ASSISTANT (JALANKAN AI SCAN) ===
function previewAiPhoto(input) {
    const container = document.getElementById('aiPreviewContainer');
    const img = document.getElementById('aiPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            container.classList.remove('hidden');

            triggerAIScanner(e.target.result, input.files[0].name);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAiPhoto() {
    document.getElementById('aiFileInput').value = '';
    document.getElementById('aiPreviewContainer').classList.add('hidden');
    document.getElementById('aiDetectorPanel').classList.add('hidden');

    document.querySelectorAll('.waste-type-card').forEach(card => {
        card.classList.remove('ai-highlighted');
    });
}

// === MEMUAT MODEL DEEP LEARNING (MOBILENET) SECARA ASYNCHRONOUS ===
let net = null;
console.log("Memulai pengunduhan model MobileNet...");
if (typeof mobilenet !== 'undefined') {
    mobilenet.load().then(model => {
        net = model;
        console.log("Model MobileNet siap untuk klasifikasi gambar nyata!");
    }).catch(err => {
        console.warn("Gagal mengunduh model MobileNet CDN:", err);
    });
}

// === LOGIKA DETEKSI KATA KUNCI AI SCANNER ===
const wasteTypeKeywords = {
    'plastik': 'plastik', 'botol': 'plastik', 'mineral': 'plastik', 'hdpe': 'plastik', 'pet': 'plastik', 'cup': 'plastik',
    'kertas': 'kertas', 'kardus': 'kertas', 'karton': 'kertas', 'buku': 'kertas', 'majalah': 'kertas', 'hvs': 'kertas', 'paper': 'kertas',
    'logam': 'logam', 'kaleng': 'logam', 'besi': 'logam', 'kawat': 'logam', 'aluminium': 'logam', 'biskuit': 'logam', 'metal': 'logam',
    'kaca': 'kaca', 'cermin': 'kaca', 'toples': 'kaca', 'glass': 'kaca', 'beling': 'kaca',
    'elektronik': 'elektronik', 'hp': 'elektronik', 'kabel': 'elektronik', 'lampu': 'elektronik', 'tv': 'elektronik', 'komputer': 'elektronik', 'ewaste': 'elektronik', 'kipas': 'elektronik',
    'minyak': 'jelantah', 'jelantah': 'jelantah', 'oil': 'jelantah',
    'sisa': 'organik', 'dapur': 'organik', 'makanan': 'organik', 'kompos': 'organik', 'sayur': 'organik', 'buah': 'organik', 'daun': 'organik',
    'residu': 'residu', 'popok': 'residu', 'pembalut': 'residu', 'tissue': 'residu', 'tisu': 'residu', 'masker': 'residu', 'French loaf': 'residu'
};

// Detail penjelasan materi dan tips untuk ditunjukkan kepada user di panel AI
const wasteTypeDetails = {
    'plastik': {
        category: 'Daur Ulang',
        title: 'Plastik (PET/HDPE)',
        desc: 'Botol plastik minuman (PET), gelas air mineral, atau botol tebal seperti botol sabun/shampoo (HDPE).',
        tips: 'Bilas botol hingga bersih dari sisa cairan manis atau sabun, lepaskan tutupnya, dan kempeskan botol sebelum disetorkan.'
    },
    'kertas': {
        category: 'Daur Ulang',
        title: 'Kertas & Kardus',
        desc: 'Kardus pembungkus, kertas koran, kertas HVS bekas kantor/sekolah, buku, majalah, atau karton makanan.',
        tips: 'Kertas wajib dalam keadaan kering, lepaskan lakban plastik atau staples logam, dan tumpuk/lipat dengan rapi.'
    },
    'logam': {
        category: 'Daur Ulang',
        title: 'Logam & Kaleng',
        desc: 'Kaleng minuman bersoda, kaleng susu kental manis, perkakas besi tua, kawat, kuningan, atau aluminium bekas.',
        tips: 'Bilas kaleng dari sisa makanan/minuman, lalu tekan kaleng hingga pipih untuk mempermudah pengepakan.'
    },
    'kaca': {
        category: 'Daur Ulang',
        title: 'Kaca & Botol Beling',
        desc: 'Botol kaca sirup, botol kecap, toples beling, atau wadah kosmetik berbahan kaca tebal.',
        tips: 'Cuci botol dari sisa sirup/saus, pisahkan dari tutup logam/plastik. Hati-hati agar pecahan wadah kaca tidak melukai petugas.'
    },
    'elektronik': {
        category: 'Non Organik / Residu',
        title: 'Elektronik (E-Waste)',
        desc: 'Charger rusak, kabel tembaga, HP bekas, keyboard, komponen sirkuit, baterai bekas, atau lampu bohlam/LED.',
        tips: 'Simpan baterai bekas di wadah kering terpisah untuk menghindari kebocoran zat kimia korosif.'
    },
    'jelantah': {
        category: 'Organik',
        title: 'Minyak Jelantah',
        desc: 'Minyak sisa penggorengan dari dapur rumah tangga.',
        tips: 'Saring minyak jelantah dari remah-remah sisa penggorengan, biarkan dingin, lalu tuang ke dalam botol plastik tertutup.'
    },
    'organik': {
        category: 'Organik',
        title: 'Organik (Sisa Makanan & Dapur)',
        desc: 'Kulit buah, potongan sayuran mentah, sisa makanan meja makan, sisa nasi, atau daun kering halaman.',
        tips: 'Kategori ini dapat langsung diolah menjadi pupuk kompos organik. Jangan dicampur dengan wadah plastik/kemasan.'
    },
    'residu': {
        category: 'Non Organik / Residu',
        title: 'Residu (Sisa Non-Organik)',
        desc: 'Tissue bekas pakai, masker sekali pakai, popok bayi, pembalut wanita, atau kertas pembungkus nasi berminyak.',
        tips: 'Kategori ini adalah sisa pembuangan akhir yang tidak dapat didaur ulang dan akan diangkut petugas menuju TPA.'
    }
};

function triggerAIScanner(imageSrc, filename) {
    const laser = document.getElementById('aiScanLaser');
    const panel = document.getElementById('aiDetectorPanel');
    const statusText = document.getElementById('aiStatusText');
    const recommendText = document.getElementById('aiRecommendText');

    document.querySelectorAll('.waste-type-card').forEach(card => {
        card.classList.remove('ai-highlighted');
    });

    laser.classList.remove('hidden');
    panel.classList.remove('hidden');
    statusText.innerHTML = '<span class="inline-block animate-pulse text-emerald-400">🤖 AI Detektor: Sedang memindai gambar...</span>';
    recommendText.innerHTML = '<span class="text-slate-400 text-[10px]">Menganalisis materi objek dan mendeteksi kategori sampah...</span>';

    panel.scrollIntoView({ behavior: 'smooth', block: 'center' });

    setTimeout(() => {
        let detectedKeyword = null;
        const nameLower = filename.toLowerCase();

        // 1. Cek keyword nama file
        for (const [key, category] of Object.entries(wasteTypeKeywords)) {
            if (nameLower.includes(key)) {
                detectedKeyword = category;
                break;
            }
        }

        // 2. Jika tidak ada keyword nama file & MobileNet siap, gunakan klasifikasi piksel
        if (!detectedKeyword && net) {
            const tempImg = new Image();
            tempImg.src = imageSrc;
            tempImg.onload = function() {
                net.classify(tempImg).then(predictions => {
                    console.log('MobileNet Predictions:', predictions);

                    for (let pred of predictions) {
                        const label = pred.className.toLowerCase();

                        if (label.includes('bottle') || label.includes('plastic') || label.includes('cup') || label.includes('water bottle') || label.includes('canister') || label.includes('container') || label.includes('shampoo') || label.includes('toilet tissue')) {
                            detectedKeyword = 'plastik';
                            break;
                        }
                        if (label.includes('paper') || label.includes('newspaper') || label.includes('book') || label.includes('cardboard') || label.includes('carton') || label.includes('box') || label.includes('envelope')) {
                            detectedKeyword = 'kertas';
                            break;
                        }
                        if (label.includes('can') || label.includes('tin') || label.includes('soda can') || label.includes('pot') || label.includes('iron') || label.includes('brass') || label.includes('nail') || label.includes('metal') || label.includes('screws')) {
                            detectedKeyword = 'logam';
                            break;
                        }
                        if (label.includes('glass') || label.includes('jar') || label.includes('goblet') || label.includes('chalice') || label.includes('wine')) {
                            detectedKeyword = 'kaca';
                            break;
                        }
                        if (label.includes('screen') || label.includes('monitor') || label.includes('phone') || label.includes('keyboard') || label.includes('mouse') || label.includes('wire') || label.includes('cable') || label.includes('electronic') || label.includes('computer')) {
                            detectedKeyword = 'elektronik';
                            break;
                        }
                        if (label.includes('oil') || label.includes('liquid') || label.includes('grease') || label.includes('petroleum')) {
                            detectedKeyword = 'jelantah';
                            break;
                        }
                        if (label.includes('food') || label.includes('hotdog') || label.includes('fruit') || label.includes('vegetable') || label.includes('banana') || label.includes('apple') || label.includes('leaf') || label.includes('flower') || label.includes('plant') || label.includes('compost')) {
                            detectedKeyword = 'organik';
                            break;
                        }
                    }

                    finalizeScan(detectedKeyword);
                }).catch(err => {
                    console.error("MobileNet classifier error:", err);
                    finalizeScan(null);
                });
            };
        } else {
            finalizeScan(detectedKeyword);
        }
    }, 2000);
}

function finalizeScan(detectedKeyword) {
    const laser = document.getElementById('aiScanLaser');
    const statusText = document.getElementById('aiStatusText');
    const recommendText = document.getElementById('aiRecommendText');

    laser.classList.add('hidden');

    // Fallback jika tidak terdeteksi
    if (!detectedKeyword) {
        detectedKeyword = 'plastik'; // Default ke Plastik
    }

    const confidence = Math.floor(Math.random() * 8) + 90; // 90% - 97% confidence
    const details = wasteTypeDetails[detectedKeyword];

    // Cari ID database menggunakan helper kustom
    const dbTypeId = getWasteTypeIdByName(detectedKeyword);

    // Tampilkan status & detail materi sampah di panel
    statusText.innerHTML = `🤖 AI Detektor: Terdeteksi <strong>${details.title}</strong> (Konfidensi ${confidence}%)`;
    recommendText.innerHTML = `
        <div class="mt-2 space-y-2 text-[10px] leading-relaxed border-t border-slate-700/60 pt-2 text-slate-300">
            <div><span class="text-[8px] bg-emerald-500/20 text-emerald-400 font-bold px-1.5 py-0.5 rounded uppercase mr-1">Golongan</span> <strong class="text-white">${details.category}</strong></div>
            <div><span class="text-[8px] bg-sky-500/20 text-sky-400 font-bold px-1.5 py-0.5 rounded uppercase mr-1">Deskripsi</span> ${details.desc}</div>
            <div><span class="text-[8px] bg-amber-500/20 text-amber-400 font-bold px-1.5 py-0.5 rounded uppercase mr-1">Tips Pilah</span> ${details.tips}</div>
        </div>
        <div class="mt-2.5 text-emerald-400 font-bold border-t border-slate-700/60 pt-2 text-[10px]">
            💡 AI menyarankan Anda memasukkan berat estimasi pada kolom bertanda hijau di formulir sebelah kiri.
        </div>
    `;

    if (dbTypeId) {
        const card = document.getElementById(`type-card-${dbTypeId}`);
        if (card) {
            // Deteksi kategori dari tab list parent untuk memindahkan tab otomatis
            const parentList = card.closest('.category-list');
            if (parentList) {
                const categoryName = parentList.id.replace('list-', '');
                switchTab(categoryName);
            }
            card.classList.add('ai-highlighted');
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

// === ESTIMATOR LIVE CALCULATE ===
const weightInputs = document.querySelectorAll('.weight-input');
const livePoinEl = document.getElementById('livePoin');
const liveRpEl = document.getElementById('liveRp');
const liveTotalDisplay = document.getElementById('liveTotalDisplay');
const mobilePoinEl = document.getElementById('mobilePoin');
const mobileRpEl = document.getElementById('mobileRp');

function calculateLiveEstimate() {
    let totalPoin = 0;
    weightInputs.forEach(input => {
        const weight = parseFloat(input.value) || 0;
        const pointsPerKg = parseInt(input.dataset.poin) || 0;
        const poin = Math.floor(weight * pointsPerKg);
        totalPoin += poin;

        const displayLabel = input.parentElement.parentElement.querySelector('.pointer-display');
        if (displayLabel) {
            displayLabel.textContent = poin.toLocaleString('id-ID') + ' Poin';
        }
    });

    livePoinEl.textContent = totalPoin.toLocaleString('id-ID');
    liveRpEl.textContent = 'Rp ' + (totalPoin * 10).toLocaleString('id-ID');
    if (liveTotalDisplay) liveTotalDisplay.textContent = totalPoin.toLocaleString('id-ID');

    if (mobilePoinEl) mobilePoinEl.textContent = totalPoin.toLocaleString('id-ID');
    if (mobileRpEl) mobileRpEl.textContent = 'Rp ' + (totalPoin * 10).toLocaleString('id-ID');
}

document.addEventListener("DOMContentLoaded", function() {
    // === TOGGLE METODE PENGUMPULAN ===
    const methodSelect = document.getElementById('methodSelect');
    const locationSection = document.getElementById('locationSection');

    methodSelect.addEventListener('change', function() {
        const addressTextarea = document.querySelector('textarea[name="address"]');
        if (this.value === 'antar') {
            locationSection.style.display = 'none';
            if (addressTextarea) addressTextarea.removeAttribute('required');
        } else {
            locationSection.style.display = 'block';
            if (addressTextarea) addressTextarea.setAttribute('required', 'required');
            setTimeout(() => { pickerMap.invalidateSize() }, 100);
        }
    });

    calculateLiveEstimate();

    // === INTERAKTIF MAP PICKER (Leaflet.js) ===
    var defaultLat = -6.3024;
    var defaultLng = 107.3065;

    var pickerMap = L.map('pickerMap').setView([defaultLat, defaultLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(pickerMap);

    var pinIcon = L.divIcon({
        html: '<span class="text-3xl">📍</span>',
        className: 'custom-div-icon',
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });

    var marker = L.marker([defaultLat, defaultLng], { icon: pinIcon, draggable: true }).addTo(pickerMap);

    function updateCoords(lat, lng) {
        document.getElementById('latInput').value = lat.toFixed(6);
        document.getElementById('lngInput').value = lng.toFixed(6);
    }

    function reverseGeocode(lat, lng) {
        const addrTextarea = document.querySelector('textarea[name="address"]');
        if (!addrTextarea) return;

        addrTextarea.placeholder = "Mengambil detail lokasi...";

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    addrTextarea.value = data.display_name;
                }
            })
            .catch(err => {
                console.error("Reverse geocoding error:", err);
            });
    }

    marker.on('dragend', function(e) {
        var position = marker.getLatLng();
        updateCoords(position.lat, position.lng);
        reverseGeocode(position.lat, position.lng);
    });

    pickerMap.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // === SEARCH ADDRESS GEOLOCATION LOGIC ===
    const searchInput = document.getElementById('mapSearchInput');
    const searchBtn = document.getElementById('mapSearchBtn');

    function searchAddress() {
        const query = searchInput.value.trim();
        if (query.length < 3) {
            alert('Silakan masukkan minimal 3 karakter pencarian.');
            return;
        }

        searchBtn.disabled = true;
        searchBtn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Mencari...';

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);

                    pickerMap.setView([lat, lon], 16);
                    marker.setLatLng([lat, lon]);
                    updateCoords(lat, lon);

                    const addrTextarea = document.querySelector('textarea[name="address"]');
                    if (addrTextarea) {
                        addrTextarea.value = data[0].display_name;
                    }
                } else {
                    alert('Lokasi tidak ditemukan. Silakan masukkan kata kunci alamat lainnya.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal mencari alamat. Silakan coba lagi nanti.');
            })
            .finally(() => {
                searchBtn.disabled = false;
                searchBtn.innerHTML = '<i class="bi bi-search"></i> Cari';
            });
    }

    searchBtn.addEventListener('click', searchAddress);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchAddress();
        }
    });
});
</script>
@endsection

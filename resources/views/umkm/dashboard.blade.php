@extends('layouts.app')
@section('title', 'Dasbor UMKM Mitra — TIECO')

@section('content')
<div class="w-full space-y-6">

    {{-- Welcome header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-shop text-emerald-600"></i> Dasbor Toko Mitra UMKM</h1>
            <p class="text-xs text-slate-400">
                @if($partner && $partner->status === 'approved')
                    Toko: <strong>{{ $partner->store_name }}</strong> — Kelola produk, validasi penukaran, dan cairkan dana.
                @elseif($partner && $partner->status === 'pending')
                    Toko: <strong>{{ $partner->store_name }}</strong> — Pengajuan kemitraan sedang diverifikasi.
                @else
                    Anda belum terdaftar sebagai mitra UMKM.
                @endif
            </p>
        </div>
        <span class="text-xs px-3 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-100">🏪 UMKM Mode</span>
    </div>

    {{-- KONDISI 1: JIKA SUDAH DISETUJUI (APPROVED) --}}
    @if($partner && $partner->status === 'approved')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        {{-- KOLOM KIRI: VALIDASI & KLAIM (5 Kolom) --}}
        <div class="md:col-span-5 space-y-6">
            {{-- VALIDASI VOUCHER --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center gap-1.5"><i class="bi bi-qr-code-scan text-emerald-600 text-sm"></i> Validasi Voucher Warga</h3>
                <div class="text-[11px] text-slate-500 bg-slate-50 border border-slate-100 rounded-2xl p-3.5 space-y-1.5">
                    <div class="font-bold text-slate-700">Panduan Validasi:</div>
                    <div class="flex gap-1.5"><span>1.</span><span>Warga menunjukkan kode voucher belanja.</span></div>
                    <div class="flex gap-1.5"><span>2.</span><span>Ketik kode voucher tersebut di kolom bawah.</span></div>
                    <div class="flex gap-1.5"><span>3.</span><span>Tekan validasi, lalu berikan produk kepada warga.</span></div>
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
            </div>

            {{-- KLAIM SETTLEMENT --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-cash-stack text-emerald-600 text-sm"></i> Pencairan Dana</h4>
                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Ajukan pencairan dana rupiah ke Bank Sampah dari semua voucher yang telah ditukarkan.
                </p>
                <form action="{{ route('umkm.claim-settlement') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-1.5 mt-2">
                        <i class="bi bi-send-check"></i> Ajukan Klaim Settlement
                    </button>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: MANAJEMEN PRODUK & KATALOG (7 Kolom) --}}
        <div class="md:col-span-7 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">

            {{-- FORM TAMBAH PRODUK BARU --}}
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center gap-1.5 mb-4"><i class="bi bi-plus-circle text-emerald-600 text-sm"></i> Tambah Penawaran Produk</h3>
                <form action="{{ route('umkm.product.store') }}" method="POST" class="space-y-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Nama Produk / Paket</label>
                        <input type="text" name="name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 bg-white" placeholder="Misal: Paket Sembako 5kg" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Deskripsi Singkat</label>
                        <input type="text" name="description" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 bg-white" placeholder="Misal: Berisi beras premium 5kg..." required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Harga (Poin)</label>
                            <input type="number" name="points_cost" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 bg-white" placeholder="Min. 50" min="10" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Stok Tersedia</label>
                            <input type="number" name="stock" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 bg-white" placeholder="Misal: 10" min="1" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-[10px] transition-all shadow-sm">
                        Simpan ke Katalog
                    </button>
                </form>
            </div>


         {{-- DAFTAR KATALOG AKTIF --}}
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center gap-1.5 mb-4"><i class="bi bi-journal-text text-emerald-600 text-sm"></i> Katalog Penukaran Toko Anda</h3>
                @if($partner->products->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        Belum ada produk/voucher yang Anda tawarkan.
                    </div>
                @else
                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
                        @foreach($partner->products as $product)
                            <div class="p-3 border border-slate-100 rounded-xl flex flex-col gap-2 hover:bg-slate-50 transition-colors shadow-sm bg-white">
                                <div class="flex justify-between items-center text-xs">
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $product->name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $product->description }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-emerald-600 flex items-center justify-end gap-1"><i class="bi bi-gem"></i> {{ number_format($product->points_cost, 0, ',', '.') }} Poin</div>
                                        <div class="text-[9px] text-slate-400 mt-0.5">Stok: <strong class="text-slate-600">{{ $product->stock }}</strong> | Rp {{ number_format($product->price_value, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                {{-- Tombol Aksi Edit & Hapus --}}
                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-2 mt-1">
                                    <button type="button" onclick="openEditModal({{ $product }})" class="px-3 py-1 bg-sky-50 hover:bg-sky-100 text-sky-600 font-bold rounded-md text-[9px] transition-colors">
                                        ✎ Edit
                                    </button>
                                    <form action="{{ route('umkm.product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-md text-[9px] transition-colors">
                                            ✕ Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- RIWAYAT BAWAH (VOUCHER & SETTLEMENT) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-clock-history text-slate-400 text-sm"></i> Riwayat Penukaran</h3>
            @if($vouchers->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">Belum ada penukaran.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="pb-3 px-2">Voucher & Produk</th>
                                <th class="pb-3 px-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @foreach($vouchers as $v)
                                <tr>
                                    <td class="py-3 px-2">
                                        <div class="font-mono font-bold text-emerald-600">{{ $v->code }}</div>
                                        <div class="text-[9px] text-slate-400">{{ $v->product->name ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                            @if($v->status === 'claimed') bg-sky-50 text-sky-700
                                            @elseif($v->status === 'used') bg-emerald-50 text-emerald-700
                                            @else bg-slate-100 text-slate-500 @endif">
                                            {{ $v->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="bi bi-cash-coin text-slate-400 text-sm"></i> Riwayat Klaim</h3>
            @if($settlements->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">Belum ada riwayat klaim.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="pb-3 px-2">Total Dana</th>
                                <th class="pb-3 px-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @foreach($settlements as $s)
                                <tr>
                                    <td class="py-3 px-2 font-black text-emerald-600">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-2 text-right">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                            @if($s->status === 'paid') bg-emerald-50 text-emerald-700
                                            @else bg-amber-50 text-amber-700 @endif">
                                            {{ $s->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- KONDISI 2: JIKA STATUS MASIH PENDING --}}
    @elseif($partner && $partner->status === 'pending')
    <div class="bg-amber-50 border border-amber-100 rounded-3xl p-12 text-center text-amber-700 shadow-sm max-w-2xl mx-auto mt-10">
        <i class="bi bi-hourglass-split text-5xl mb-4 block opacity-80"></i>
        <h3 class="text-xl font-bold mb-2">Pengajuan Sedang Diproses</h3>
        <p class="text-sm text-amber-700/80 leading-relaxed">Toko <strong>{{ $partner->store_name }}</strong> sedang dalam tahap verifikasi oleh Operator Bank Sampah. Mohon menunggu maksimal 1x24 jam kerja.</p>
    </div>

    {{-- KONDISI 3: JIKA BELUM MENDAFTAR SAMA SEKALI --}}
    @else
    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm max-w-2xl mx-auto mt-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm border border-emerald-100"><i class="bi bi-shop-window"></i></div>
            <h2 class="text-xl font-extrabold text-slate-800">Bergabung Menjadi Mitra UMKM</h2>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Dapatkan pelanggan baru dari warga yang ingin menukarkan poin sampahnya di toko Anda. Daftar gratis dan bantu majukan ekonomi sirkular lokal!</p>
        </div>

        <form action="{{ route('umkm.register') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Nama Toko / Usaha</label>
                <input type="text" name="store_name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Kategori Usaha</label>
                <select name="category" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all appearance-none cursor-pointer" required>
                    <option value="Sembako">Sembako & Kebutuhan Harian</option>
                    <option value="Kuliner">Kuliner (Makanan/Minuman)</option>
                    <option value="Jasa">Jasa & Layanan</option>
                    <option value="Kerajinan Daur Ulang">Kerajinan Daur Ulang</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Alamat Lengkap Toko</label>
                <textarea name="address" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" required></textarea>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Deskripsi Singkat Toko</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 focus:bg-white transition-all" required></textarea>
            </div>
            <button type="submit" class="w-full py-4 mt-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs uppercase tracking-widest transition-all shadow-md hover:-translate-y-0.5">
                Ajukan Kemitraan Sekarang
            </button>
        </form>
    </div>
    @endif

    {{-- MODAL EDIT PRODUK UMKM --}}
    <div id="editProductModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden animate-in">
            <div class="p-5 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-1.5"><i class="bi bi-pencil-square text-sky-600"></i> Edit Produk</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form id="editProductForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Nama Produk / Paket</label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 bg-white" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Deskripsi Singkat</label>
                    <input type="text" name="description" id="edit_description" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 bg-white" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Harga (Poin)</label>
                        <input type="number" name="points_cost" id="edit_points_cost" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 bg-white" min="10" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Stok Tersedia</label>
                        <input type="number" name="stock" id="edit_stock" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 bg-white" min="0" required>
                    </div>
                </div>
                <button type="submit" class="w-full py-3 mt-2 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-xs transition-all shadow-md">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    {{-- SCRIPT UNTUK MODAL EDIT --}}
    <script>
        function openEditModal(product) {
            // Isi form dengan data yang sudah ada
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_points_cost').value = product.points_cost;
            document.getElementById('edit_stock').value = product.stock;

            // Sesuaikan action URL form agar mengarah ke produk yang tepat
            const form = document.getElementById('editProductForm');
            form.action = `/umkm/product/${product.id}`;

            // Tampilkan modal
            document.getElementById('editProductModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editProductModal').classList.add('hidden');
        }
    </script>

</div>
@endsection

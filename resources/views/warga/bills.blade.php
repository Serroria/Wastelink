@extends('layouts.app')
@section('title', 'Pulsa & Tagihan — TIECO')

@section('content')
    <div class="w-full space-y-6">

        {{-- TIECO-Style GrabPay Balance Card --}}
        <div class="bg-emerald-600 rounded-2xl p-5 text-white shadow-md relative overflow-hidden flex justify-between items-center select-none">
            <div class="space-y-1">
                <span class="text-[9px] font-bold text-emerald-100 uppercase tracking-widest flex items-center gap-1.5">
                    <i class="bi bi-wallet2"></i> Dompet Poin TIECO
                </span>
                <div class="text-2xl font-black">{{ number_format($user->point_balance, 0, ',', '.') }} <span class="text-xs font-normal text-emerald-100">Poin</span></div>
                <div class="text-[10px] text-emerald-100 font-semibold">Setara Rp {{ number_format($user->point_balance * 10, 0, ',', '.') }}</div>
            </div>

            <div class="bg-white/10 border border-white/20 rounded-2xl px-3 py-2 text-center shrink-0">
                <div class="text-[8px] font-bold uppercase tracking-wider text-emerald-100">Nilai Tukar</div>
                <div class="text-xs font-bold mt-0.5">1 Poin = Rp 10</div>
            </div>
        </div>

        {{-- Sleek Search Bar --}}
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-xs">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchBiller"
                class="w-full pl-10 pr-4 py-3 bg-slate-100 border-0 rounded-full text-slate-800 text-xs focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder-slate-400"
                placeholder="Cari layanan, e-wallet, atau biller...">
        </div>

        {{-- Promotion Banner (TIECO Theme) --}}
        <div class="bg-emerald-50/50 border border-emerald-100/50 rounded-2xl p-4 flex items-center gap-3.5 select-none">
            <span
                class="text-xl p-2.5 bg-white border border-emerald-100 text-emerald-600 rounded-xl shrink-0 shadow-sm flex items-center justify-center animate-pulse">
                ♻️
            </span>
            <div class="min-w-0">
                <div class="text-[11px] font-bold text-slate-700 truncate">Sambil Selamatkan Bumi, Sambil Hemat Pengeluaran!</div>
                <p class="text-[9px] text-slate-500 mt-0.5 leading-relaxed">Bebaskan dompetmu dari tagihan listrik, air, dan pulsa bulanan. Cukup bayar pakai poin hasil pilah sampahmu!</p>
            </div>
        </div>

        {{-- CATEGORY: TOP UP (BORDERLESS SERVICES GRID) --}}
        <div class="space-y-4 pt-2">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Isi Ulang & E-Wallet
            </h3>

            <div class="grid grid-cols-4 gap-y-6 gap-x-2 text-center">
                {{-- DANA --}}
                <button onclick="openTransactionModal('topup', 'DANA', 'Nomor Handphone')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-sky-500/10 text-sky-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-sky-500 group-hover:text-white mb-2">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">DANA</span>
                </button>
                {{-- GOPAY --}}
                <button onclick="openTransactionModal('topup', 'GoPay', 'Nomor Handphone')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-emerald-500 group-hover:text-white mb-2">
                        <i class="bi bi-phone-vibrate"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">GoPay</span>
                </button>
                {{-- OVO --}}
                <button onclick="openTransactionModal('topup', 'OVO', 'Nomor Handphone')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-purple-500/10 text-purple-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-purple-500 group-hover:text-white mb-2">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">OVO</span>
                </button>
                {{-- ShopeePay --}}
                <button onclick="openTransactionModal('topup', 'ShopeePay', 'Nomor Handphone')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-orange-500/10 text-orange-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-orange-500 group-hover:text-white mb-2">
                        <i class="bi bi-bag-dash"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">ShopeePay</span>
                </button>

                {{-- PULSA --}}
                <button onclick="openTransactionModal('topup', 'Pulsa', 'Nomor Handphone')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div class="relative">
                        <div class="w-12 h-12 bg-rose-500/10 text-rose-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-rose-500 group-hover:text-white">
                            <i class="bi bi-phone"></i>
                        </div>
                        <span class="absolute -top-1.5 -right-2.5 bg-red-500 text-white text-[7px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm scale-90">Promo</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all mt-2">Pulsa</span>
                </button>
                {{-- DATA --}}
                <button onclick="openTransactionModal('topup', 'Paket Data', 'Nomor Handphone')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div class="relative">
                        <div class="w-12 h-12 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-amber-500 group-hover:text-white">
                            <i class="bi bi-wifi"></i>
                        </div>
                        <span class="absolute -top-1.5 -right-2.5 bg-orange-500 text-white text-[7px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm scale-90">Hemat</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all mt-2 font-semibold">Paket Data</span>
                </button>
                {{-- TOKEN --}}
                <button onclick="openTransactionModal('topup', 'Token Listrik', 'Nomor Meter / ID Pelanggan')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-yellow-500/10 text-yellow-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-yellow-500 group-hover:text-white mb-2">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">Token
                        PLN</span>
                </button>
                {{-- E-Money --}}
                <button onclick="openTransactionModal('topup', 'E-Money', 'Nomor Kartu E-Money')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-indigo-500/10 text-indigo-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-indigo-500 group-hover:text-white mb-2">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">E-Money</span>
                </button>
            </div>
        </div>

        {{-- CATEGORY: TAGIHAN (BORDERLESS SERVICES GRID) --}}
        <div class="space-y-4 pt-4 pb-20">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Tagihan Bulanan
            </h3>

            <div class="grid grid-cols-4 gap-y-6 gap-x-2 text-center">
                {{-- LISTRIK --}}
                <button onclick="openTransactionModal('bill', 'Listrik PLN', 'Nomor Meter / ID Pelanggan')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-yellow-500/10 text-yellow-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-yellow-500 group-hover:text-white mb-2">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">Listrik
                        PLN</span>
                </button>
                {{-- PDAM --}}
                <button onclick="openTransactionModal('bill', 'PDAM', 'Nomor Sambungan / ID Pelanggan')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-blue-500 group-hover:text-white mb-2">
                        <i class="bi bi-droplet"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">PDAM
                        Air</span>
                </button>
                {{-- BPJS --}}
                <button onclick="openTransactionModal('bill', 'BPJS Kesehatan', 'Nomor BPJS (13 digit)')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-teal-500/10 text-teal-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-teal-500 group-hover:text-white mb-2">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">BPJS</span>
                </button>
                {{-- Internet & TV --}}
                <button onclick="openTransactionModal('bill', 'Internet & TV Cable', 'Nomor Pelanggan')"
                    class="biller-item group flex flex-col items-center focus:outline-none">
                    <div
                        class="w-12 h-12 bg-cyan-500/10 text-cyan-600 rounded-2xl flex items-center justify-center text-lg transition-all shadow-inner group-hover:scale-105 group-hover:bg-cyan-500 group-hover:text-white mb-2">
                        <i class="bi bi-tv"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold text-slate-600 group-hover:text-emerald-600 leading-tight transition-all">Internet
                        & TV</span>
                </button>
            </div>
        </div>

    </div>

    {{-- MODAL FORM TRANSAKSI --}}
    <div id="txModal"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-in">
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                    <span id="modalBillerIcon">📱</span>
                    <span id="modalBillerName">Top-Up</span>
                </h3>
                <button onclick="document.getElementById('txModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form action="{{ route('warga.bills.pay') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="transaction_type" id="inputTxType">
                <input type="hidden" name="biller_name" id="inputBillerName">

                <div>
                    <label id="inputLabelAccount" class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Nomor
                        HP</label>
                    <input type="text" name="account_number"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition-all font-semibold"
                        placeholder="Contoh: 0812xxxxxxxx" required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Pilih Nominal</label>
                    <div class="grid grid-cols-2 gap-3 mb-3" id="nominalGrid">
                        {{-- Nominal Top Up Option 1 --}}
                        <label
                            class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none">
                            <input type="radio" name="nominal_rp" value="10000" class="sr-only" checked
                                onclick="selectNominal(1000, false)">
                            <span class="text-xs font-bold text-slate-700">Rp 10.000</span>
                            <span class="text-[9px] text-emerald-600 font-bold mt-1">1.000 Poin</span>
                        </label>
                        {{-- Nominal Top Up Option 2 --}}
                        <label
                            class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none">
                            <input type="radio" name="nominal_rp" value="25000" class="sr-only"
                                onclick="selectNominal(2500, false)">
                            <span class="text-xs font-bold text-slate-700">Rp 25.000</span>
                            <span class="text-[9px] text-emerald-600 font-bold mt-1">2.500 Poin</span>
                        </label>
                        {{-- Nominal Top Up Option 3 --}}
                        <label
                            class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none">
                            <input type="radio" name="nominal_rp" value="50000" class="sr-only"
                                onclick="selectNominal(5000, false)">
                            <span class="text-xs font-bold text-slate-700">Rp 50.000</span>
                            <span class="text-[9px] text-emerald-600 font-bold mt-1">5.000 Poin</span>
                        </label>
                        {{-- Nominal Top Up Option 4 --}}
                        <label
                            class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none">
                            <input type="radio" name="nominal_rp" value="100000" class="sr-only"
                                onclick="selectNominal(10000, false)">
                            <span class="text-xs font-bold text-slate-700">Rp 100.000</span>
                            <span class="text-[9px] text-emerald-600 font-bold mt-1">10.000 Poin</span>
                        </label>
                        {{-- Custom Option --}}
                        <label id="customNominalLabel"
                            class="border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none col-span-2">
                            <input type="radio" name="nominal_rp" id="radioCustom" value="custom" class="sr-only"
                                onclick="selectNominal(0, true)">
                            <span class="text-xs font-bold text-slate-700">Nominal Lainnya</span>
                            <span class="text-[9px] text-slate-400 font-semibold mt-1">Ketik nominal kustom Anda</span>
                        </label>
                    </div>

                    {{-- Custom Nominal Input Box --}}
                    <div id="customNominalGroup" class="hidden space-y-2 border-t border-slate-100 pt-3">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Jumlah Nominal Rupiah (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-xs font-black text-slate-400">Rp</span>
                            <input type="number" id="customNominalInput"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-emerald-500 focus:bg-white transition-all font-mono"
                                placeholder="Contoh: 15000" min="10000" step="1000" oninput="calculateCustomPointsCost()">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-3 text-xs flex justify-between">
                    <span class="text-slate-500 font-semibold">Biaya Poin:</span>
                    <span class="font-extrabold text-emerald-600" id="costPoinDisplay">1.000 Poin</span>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs uppercase tracking-widest transition-all shadow-md mt-4">
                    Bayar Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL STRUK DIGITAL PEMBAYARAN SUKSES --}}
    @if(session('receipt'))
        @php $receipt = session('receipt'); @endphp
        <div id="receiptModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-in">
                {{-- Header Struk --}}
                <div class="bg-emerald-600 p-6 text-white text-center relative">
                    <span class="text-3xl block mb-2">💸</span>
                    <h3 class="font-black text-lg tracking-tight">TRANSAKSI BERHASIL</h3>
                    <p class="text-emerald-100 text-[10px] mt-1 font-semibold uppercase tracking-widest">
                        {{ data_get($receipt, 'biller_name') }}</p>
                    <button onclick="document.getElementById('receiptModal').classList.add('hidden')"
                        class="absolute top-4 right-4 text-white/70 hover:text-white text-lg">✕</button>
                </div>

                {{-- Detail Struk --}}
                <div class="p-6 space-y-6">
                    <div class="border-b border-dashed border-slate-200 pb-4 text-center">
                        <div class="text-2xl font-black text-slate-800">Rp
                            {{ number_format(data_get($receipt, 'nominal_rp'), 0, ',', '.') }}</div>
                        <div class="text-[10px] font-bold text-red-500 mt-1 uppercase tracking-wider">
                            -{{ number_format(data_get($receipt, 'points_spent'), 0, ',', '.') }} POIN TABUNGAN</div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nomor Pelanggan:</span>
                            <span class="font-bold text-slate-800">{{ data_get($receipt, 'account_number') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Waktu Transaksi:</span>
                            <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse(data_get($receipt, 'created_at'))->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nomor Referensi:</span>
                            <span class="font-mono text-slate-500 font-semibold">{{ data_get($receipt, 'ref_number') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Metode Pembayaran:</span>
                            <span class="font-bold text-emerald-600">Tabungan Sampah</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Pembayaran:</span>
                            <span
                                class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full font-bold text-[9px]">SUKSES</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 text-center">
                        <p class="text-[10px] text-slate-400">Terima kasih telah berkontribusi menjaga lingkungan dan mendukung
                            ekonomi sirkular lokal bersama TIECO!</p>
                    </div>

                    <button onclick="document.getElementById('receiptModal').classList.add('hidden')"
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">
                        Tutup Struk
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- CUSTOM INTERACTIVE MODAL ALERT (WARNING/SUCCESS/ERROR POPUP) --}}
    <div id="alertModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 hidden">
        <div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform scale-95 opacity-0 transition-all duration-300 ease-out" id="alertModalContent">
            <div class="p-6 flex flex-col items-center text-center space-y-4">
                {{-- Icon --}}
                <div id="alertIconContainer" class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-sm">
                    ⚠️
                </div>
                
                {{-- Text Content --}}
                <div class="space-y-1">
                    <h4 id="alertTitle" class="font-black text-slate-800 text-sm">Peringatan</h4>
                    <p id="alertDescription" class="text-xs text-slate-500 leading-relaxed">Deskripsi peringatan...</p>
                </div>
                
                {{-- Button --}}
                <button type="button" onclick="closeAlertModal()" id="alertBtn" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all uppercase tracking-wider focus:outline-none shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function openTransactionModal(type, biller, accountLabel) {
            document.getElementById('inputTxType').value = type;
            document.getElementById('inputBillerName').value = biller;
            document.getElementById('modalBillerName').textContent = (type === 'topup' ? 'Top-Up ' : 'Bayar ') + biller;
            document.getElementById('inputLabelAccount').textContent = accountLabel;

            // Set default icon
            let icon = '📱';
            if (biller.includes('PLN') || biller.includes('Listrik')) icon = '💡';
            else if (biller.includes('PDAM')) icon = '💧';
            else if (biller.includes('BPJS')) icon = '🛡️';
            else if (biller.includes('Internet')) icon = '📺';
            document.getElementById('modalBillerIcon').textContent = icon;

            // Hide custom nominal input initially
            document.getElementById('customNominalGroup').classList.add('hidden');
            document.getElementById('customNominalInput').value = '';

            // Reset styles on radio options
            document.querySelectorAll('#nominalGrid label').forEach((label, idx) => {
                const isCustomLabel = label.id === 'customNominalLabel';
                const colSpanClass = isCustomLabel ? ' col-span-2' : '';
                
                if (idx === 0) {
                    label.querySelector('input').checked = true;
                    label.className = "border border-emerald-500 bg-emerald-50/20 text-emerald-800 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 transition-all select-none" + colSpanClass;
                    const val = label.querySelector('input').value;
                    document.getElementById('costPoinDisplay').textContent = (parseInt(val) / 10).toLocaleString('id-ID') + " Poin";
                } else {
                    label.className = "border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none" + colSpanClass;
                }
            });

            document.getElementById('txModal').classList.remove('hidden');
        }

        function selectNominal(poin, isCustom) {
            const customGroup = document.getElementById('customNominalGroup');
            
            if (isCustom) {
                customGroup.classList.remove('hidden');
                document.getElementById('customNominalInput').focus();
                calculateCustomPointsCost();
            } else {
                customGroup.classList.add('hidden');
                document.getElementById('costPoinDisplay').textContent = poin.toLocaleString('id-ID') + " Poin";
            }

            // Update border styles of selected label
            document.querySelectorAll('#nominalGrid label').forEach(label => {
                const input = label.querySelector('input');
                const isThisCustom = label.id === 'customNominalLabel';
                const colSpanClass = isThisCustom ? ' col-span-2' : '';
                
                if (input.checked) {
                    label.className = "border border-emerald-500 bg-emerald-50/20 text-emerald-800 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 transition-all select-none" + colSpanClass;
                } else {
                    label.className = "border border-slate-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer hover:bg-emerald-50/20 hover:border-emerald-250 transition-all select-none" + colSpanClass;
                }
            });
        }

        function calculateCustomPointsCost() {
            const input = document.getElementById('customNominalInput');
            const val = parseFloat(input.value) || 0;
            const pointsCost = Math.floor(val / 10);
            
            document.getElementById('costPoinDisplay').textContent = pointsCost.toLocaleString('id-ID') + " Poin";
        }

        // Intercept form submit to validate and pass the custom value
        document.querySelector('#txModal form').addEventListener('submit', function(e) {
            const radioCustom = document.getElementById('radioCustom');
            if (radioCustom.checked) {
                const customVal = document.getElementById('customNominalInput').value;
                if (!customVal || parseInt(customVal) < 10000) {
                    e.preventDefault();
                    alert('Minimal nominal kustom adalah Rp 10.000');
                    return;
                }
                
                const costPoin = parseInt(customVal) / 10;
                if (costPoin > {{ $user->point_balance }}) {
                    e.preventDefault();
                    alert('Saldo poin Anda tidak mencukupi untuk nominal ini. Dibutuhkan ' + costPoin.toLocaleString('id-ID') + ' Poin.');
                    return;
                }

                // Set value of custom radio to the custom value so the backend receives it
                radioCustom.value = customVal;
            }
        });

        // Search Filter
        document.getElementById('searchBiller').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.biller-item').forEach(btn => {
                const name = btn.querySelector('span').textContent.toLowerCase();
                if (name.includes(query)) {
                    btn.style.display = 'flex';
                } else {
                    btn.style.display = 'none';
                }
            });
        });
    </script>
@endsection
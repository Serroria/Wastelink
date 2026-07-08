<?php

namespace App\Http\Controllers;

use App\Models\Settlement;
use App\Models\SystemStat;
use App\Models\UmkmPartner;
use App\Models\User;
use App\Models\WasteDeposit;
use App\Models\WasteListing;
use App\Models\WasteType;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankSampahController extends Controller
{
    /**
     * Dasbor Operator Bank Sampah: ringkasan operasional.
     */
    public function dashboard()
    {
        $pendingDeposits = WasteDeposit::where('status', 'pending')->count();
        $approvedDeposits = WasteDeposit::where('status', 'approved')->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $pendingSettlements = Settlement::where('status', 'pending')->count();
        $stats = SystemStat::first();
        $recentDeposits = WasteDeposit::with('user')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        $wasteTypes = WasteType::all()->keyBy('id');
        $totalWarga = User::where('role', 'warga')->count();

        return view('bank_sampah.dashboard', compact(
            'pendingDeposits', 'approvedDeposits', 'pendingWithdrawals',
            'pendingSettlements', 'stats', 'recentDeposits', 'wasteTypes', 'totalWarga'
        ));
    }

    /**
     * Halaman verifikasi deposit warga.
     */
    // public function verifikasi()
    // {
    //     $deposits = WasteDeposit::with('user')
    //         ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
    //         ->orderByDesc('created_at')
    //         ->get();
    //     $wasteTypes = WasteType::all()->keyBy('id');

    //     return view('bank_sampah.verifikasi', compact('deposits', 'wasteTypes'));
    // }
    public function verifikasi()
    {
        // Menggunakan CASE WHEN agar kompatibel dengan PostgreSQL (Supabase)
        $deposits = WasteDeposit::with('user')
            ->orderByRaw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'menuju_lokasi' THEN 2
                    WHEN 'ditimbang' THEN 3
                    WHEN 'approved' THEN 4
                    WHEN 'revised' THEN 5
                    WHEN 'rejected' THEN 6
                    WHEN 'didistribusikan' THEN 7
                    ELSE 8
                END
            ")
            ->orderByDesc('created_at')
            ->get();

        $wasteTypes = WasteType::all()->keyBy('id');

        return view('bank_sampah.verifikasi', compact('deposits', 'wasteTypes'));
    }

    /**
     * Proses verifikasi (approve/reject/revise) deposit.
     */
    // public function processDeposit(Request $request, $id)
    // {
    //     $deposit = WasteDeposit::findOrFail($id);
    //     $action = $request->input('action'); // approve, reject, revise
    //     $operator = Auth::user();
    //     $wasteTypes = WasteType::all()->keyBy('id');

    //     if ($action === 'approve' || $action === 'revise') {
    //         // Hitung ulang berdasarkan berat aktual dari operator
    //         $weightDetails = [];
    //         $totalPoints = 0;

    //         foreach ($request->input('weights', []) as $typeId => $weight) {
    //             $weight = floatval($weight);
    //             if ($weight > 0) {
    //                 $weightDetails[$typeId] = $weight;
    //                 if (isset($wasteTypes[$typeId])) {
    //                     $totalPoints += (int)($weight * $wasteTypes[$typeId]->points_per_kg);
    //                 }
    //             }
    //         }

    //         $deposit->update([
    //             'collector_id' => $operator->id,
    //             'status' => $action === 'approve' ? 'approved' : 'revised',
    //             'weight_details' => json_encode($weightDetails),
    //             'total_points' => $totalPoints,
    //             'notes' => $request->input('notes', $deposit->notes),
    //         ]);

    //         // Tambah poin ke saldo warga
    //         $warga = $deposit->user;
    //         $warga->point_balance += $totalPoints;
    //         $warga->save();

    //         // Update statistik
    //         $totalWeight = array_sum($weightDetails);
    //         $stats = SystemStat::first();
    //         if ($stats) {
    //             $stats->total_landfill_saved += $totalWeight;
    //             $stats->total_co2_saved += $totalWeight * 2.5;
    //             $stats->save();
    //         }

    //     } elseif ($action === 'reject') {
    //         $deposit->update([
    //             'collector_id' => $operator->id,
    //             'status' => 'rejected',
    //             'notes' => $request->input('notes', 'Sampah tidak memenuhi syarat.'),
    //         ]);
    //     }

    //     return redirect()->route('bank-sampah.verifikasi')->with('success', 'Deposit berhasil diproses.');
    // }

    // /**
    //  * Halaman manajemen stok & listing B2B.
    //  */
    public function stok()
    {
        $wasteTypes = WasteType::all();
        $listings = WasteListing::orderByDesc('created_at')->get();

        $stokPerType = $this->availableStockByWasteType();

        return view('bank_sampah.stok', compact('wasteTypes', 'listings', 'stokPerType'));
    }

    /**
     * Batalkan atau hapus listing yang belum terjual
     */
    public function cancelListing($id)
    {
        $cancelled = DB::transaction(function () use ($id): bool {
            $listing = WasteListing::lockForUpdate()->findOrFail($id);

            if ($listing->status !== 'available') {
                return false;
            }

            $listing->update(['status' => 'cancelled']);

            return true;
        });

        if (! $cancelled) {
            return back()->with('error', 'Tidak dapat membatalkan listing yang sudah terjual.');
        }

        return redirect()->route('bank-sampah.stok')->with('success', 'Listing berhasil dibatalkan. Stok telah dikembalikan ke gudang.');
    }

    public function processDeposit(Request $request, $id)
    {
        $deposit = WasteDeposit::findOrFail($id);
        $action = $request->input('action');
        $operator = Auth::user();
        $wasteTypes = WasteType::all()->keyBy('id');

        // TAHAP 1: Kurir Menuju Lokasi
        if ($action === 'otw') {
            $deposit->update([
                'collector_id' => $operator->id,
                'status' => 'menuju_lokasi',
            ]);

            return back()->with('success', 'Status diperbarui: Kurir sedang menuju lokasi warga.');
        }

        // TAHAP 2: Simpan Timbangan Aktual (Belum kasih poin)
        if ($action === 'timbang' || $action === 'revise') {
            $weightDetails = [];
            $totalPoints = 0;

            foreach ($request->input('weights', []) as $typeId => $weight) {
                $weight = floatval($weight);
                if ($weight > 0) {
                    $weightDetails[$typeId] = $weight;
                    if (isset($wasteTypes[$typeId])) {
                        $totalPoints += (int) ($weight * $wasteTypes[$typeId]->points_per_kg);
                    }
                }
            }

            $deposit->update([
                'collector_id' => $operator->id,
                'status' => $action === 'timbang' ? 'ditimbang' : 'revised',
                'weight_details' => json_encode($weightDetails),
                'total_points' => $totalPoints,
                'notes' => $request->input('notes', $deposit->notes),
            ]);
            // Jika status direvisi dari ditimbang, kita kembalikan ke ditimbang agar form setujui tetap muncul
            if ($action === 'revise') {
                $deposit->update(['status' => 'ditimbang']);
            }

            return back()->with('success', 'Timbangan aktual berhasil disimpan.');
        }

        // TAHAP 3: Kreditkan Poin ke Warga (Approved)
        if ($action === 'approve') {
            $result = DB::transaction(function () use ($id): array {
                $deposit = WasteDeposit::with('user')->lockForUpdate()->findOrFail($id);

                if (in_array($deposit->status, ['approved', 'didistribusikan'], true)) {
                    return ['success' => 'Setoran ini sudah pernah disetujui.'];
                }

                if ($deposit->status !== 'ditimbang') {
                    return ['error' => 'Setoran harus ditimbang terlebih dahulu sebelum disetujui.'];
                }

                $warga = User::lockForUpdate()->findOrFail($deposit->user_id);
                $warga->point_balance += $deposit->total_points;
                $warga->save();

                $weightDetails = $deposit->weight_details ?: [];
                $totalWeight = array_sum($weightDetails);

                $stats = SystemStat::first();
                if ($stats) {
                    $stats->total_landfill_saved += $totalWeight;
                    $stats->total_co2_saved += $totalWeight * 2.5;
                    $stats->save();
                }

                $deposit->update(['status' => 'approved']);

                return ['success' => 'Poin berhasil dikreditkan ke dompet warga.'];
            });

            if (isset($result['error'])) {
                return back()->with('error', $result['error']);
            }

            return back()->with('success', $result['success']);
        }

        // TAHAP 4: Distribusi ke Industri
        if ($action === 'distribusi') {
            $deposit->update(['status' => 'didistribusikan']);

            return back()->with('success', 'Status diperbarui: Didistribusikan ke Industri.');
        }

        // TINDAKAN: Tolak Setoran
        if ($action === 'reject') {
            $deposit->update([
                'collector_id' => $operator->id,
                'status' => 'rejected',
                'notes' => $request->input('notes', 'Sampah tidak memenuhi syarat atau kotor.'),
            ]);

            return back()->with('success', 'Setoran telah ditolak.');
        }

        return back();
    }

    /**
     * Buat listing baru untuk dijual ke industri.
     */
    public function createListing(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'weights' => ['required', 'array'],
            'weights.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $wasteTypes = WasteType::all()->keyBy('id');
        $weightDetails = [];
        $totalPrice = 0;
        $availableStock = $this->availableStockByWasteType();

        foreach ($request->input('weights', []) as $typeId => $weight) {
            $weight = floatval($weight);
            if ($weight > 0) {
                if ($weight > ($availableStock[$typeId] ?? 0)) {
                    return back()->with('error', 'Berat listing melebihi stok gudang yang tersedia.');
                }

                $weightDetails[$typeId] = $weight;
                if (isset($wasteTypes[$typeId])) {
                    $totalPrice += $weight * $wasteTypes[$typeId]->price_per_kg;
                }
            }
        }

        if ($weightDetails === []) {
            return back()->with('error', 'Masukkan minimal satu jenis sampah untuk dijual.');
        }

        DB::transaction(function () use ($request, $weightDetails, $totalPrice): void {
            WasteListing::create([
                'title' => $request->input('title', 'Sampah Terpilah'),
                'description' => $request->input('description', ''),
                'weight_details' => $weightDetails,
                'total_price' => $totalPrice,
                'status' => 'available',
            ]);
        });

        return redirect()->route('bank-sampah.stok')->with('success', 'Listing berhasil dibuat.');
    }

    /**
     * @return array<int, float>
     */
    private function availableStockByWasteType(): array
    {
        $stockByType = [];

        WasteDeposit::where('status', 'approved')->get()->each(function (WasteDeposit $deposit) use (&$stockByType): void {
            foreach (($deposit->weight_details ?: []) as $typeId => $weight) {
                $stockByType[$typeId] = ($stockByType[$typeId] ?? 0) + (float) $weight;
            }
        });

        WasteListing::whereIn('status', ['available', 'sold'])->get()->each(function (WasteListing $listing) use (&$stockByType): void {
            foreach (($listing->weight_details ?: []) as $typeId => $weight) {
                $stockByType[$typeId] = max(0, ($stockByType[$typeId] ?? 0) - (float) $weight);
            }
        });

        return $stockByType;
    }

    /**
     * Halaman settlement: kelola penarikan & klaim UMKM.
     */
    public function settlement()
    {
        $withdrawals = Withdrawal::with('user')->orderByDesc('created_at')->get();
        $settlements = Settlement::with('partner')->orderByDesc('created_at')->get();
        $stats = SystemStat::first();

        $pendingPartners = UmkmPartner::with('user')->where('status', 'pending')->orderByDesc('created_at')->get();

        return view('bank_sampah.settlement', compact('withdrawals', 'settlements', 'stats', 'pendingPartners'));
    }

    public function approvePartner($id)
    {
        UmkmPartner::findOrFail($id)->update(['status' => 'approved']);

        return back()->with('success', 'Mitra UMKM berhasil disetujui!');
    }

    public function rejectPartner($id)
    {
        UmkmPartner::findOrFail($id)->update(['status' => 'rejected']);

        return back()->with('success', 'Pengajuan Mitra UMKM ditolak.');
    }

    /**
     * Approve penarikan tunai warga.
     */
    public function approveWithdrawal($id)
    {
        $result = DB::transaction(function () use ($id): array {
            $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);
            $stats = SystemStat::lockForUpdate()->first();

            if (! $stats || $stats->bank_sampah_cash < $withdrawal->equivalent_rp) {
                return ['error' => 'Kas bank sampah tidak mencukupi.'];
            }

            if ($withdrawal->status !== 'pending') {
                return ['success' => 'Penarikan tunai sudah pernah diproses.'];
            }

            $withdrawal->update(['status' => 'approved']);
            $stats->bank_sampah_cash -= $withdrawal->equivalent_rp;
            $stats->save();

            return ['success' => 'Penarikan tunai disetujui.'];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', $result['success']);
    }

    /**
     * Bayar settlement UMKM.
     */
    public function paySettlement($id)
    {
        $result = DB::transaction(function () use ($id): array {
            $settlement = Settlement::lockForUpdate()->findOrFail($id);
            $stats = SystemStat::lockForUpdate()->first();

            if (! $stats || $stats->bank_sampah_cash < $settlement->total_amount) {
                return ['error' => 'Kas bank sampah tidak mencukupi untuk membayar settlement ini.'];
            }

            if ($settlement->status !== 'pending') {
                return ['success' => 'Settlement UMKM sudah pernah diproses.'];
            }

            $settlement->update(['status' => 'paid', 'paid_at' => now()]);
            $stats->bank_sampah_cash -= $settlement->total_amount;
            $stats->save();

            return ['success' => 'Settlement UMKM berhasil dibayar.'];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', $result['success']);
    }
}

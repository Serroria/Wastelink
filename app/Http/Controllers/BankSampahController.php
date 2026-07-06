<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WasteType;
use App\Models\WasteDeposit;
use App\Models\WasteListing;
use App\Models\Voucher;
use App\Models\Withdrawal;
use App\Models\Settlement;
use App\Models\UmkmPartner;
use App\Models\SystemStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Hitung total stok dari deposit yang sudah approved
        $stokPerType = [];
        $approvedDeposits = WasteDeposit::where('status', 'approved')->get();
        foreach ($approvedDeposits as $dep) {
            $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
            if ($details) {
                foreach ($details as $typeId => $weight) {
                    $stokPerType[$typeId] = ($stokPerType[$typeId] ?? 0) + $weight;
                }
            }
        }
         $usedListings = WasteListing::whereIn('status', ['available', 'sold'])->get();
    foreach ($usedListings as $listing) {
        $details = is_array($listing->weight_details) ? $listing->weight_details : json_decode($listing->weight_details, true);
        if ($details) {
            foreach ($details as $typeId => $weight) {
                // Pastikan stok tidak minus
                if (isset($stokPerType[$typeId])) {
                    $stokPerType[$typeId] = max(0, $stokPerType[$typeId] - $weight);
                }
            }
        }
    }
    //  dd([
    //     'total_deposit' => $stokPerType, // Lihat stok dari setoran warga
    //     'used_listings' => $usedListings->pluck('title', 'status'), // Lihat listing apa yang mengurangi stok
    //     'stok_akhir' => $stokPerType // Lihat hasil akhir setelah dikurangi
    // ]);

     return view('bank_sampah.stok', compact('wasteTypes', 'listings', 'stokPerType'));
    }

    /**
 * Batalkan atau hapus listing yang belum terjual
 */
public function cancelListing($id)
{
    $listing = WasteListing::findOrFail($id);

    // Hanya izinkan pembatalan jika statusnya masih 'available'
    if ($listing->status !== 'available') {
        return back()->with('error', 'Tidak dapat membatalkan listing yang sudah terjual.');
    }

    $listing->update(['status' => 'cancelled']);


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
                'status' => 'menuju_lokasi'
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
                        $totalPoints += (int)($weight * $wasteTypes[$typeId]->points_per_kg);
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
            if ($deposit->status !== 'approved' && $deposit->status !== 'didistribusikan') {
                // 1. Tambah poin
                $warga = $deposit->user;
                $warga->point_balance += $deposit->total_points;
                $warga->save();

                // 2. Update statistik sistem
                $weightDetails = is_array($deposit->weight_details) ? $deposit->weight_details : json_decode($deposit->weight_details, true);
                $totalWeight = array_sum($weightDetails ?: []);

                $stats = SystemStat::first();
                if ($stats) {
                    $stats->total_landfill_saved += $totalWeight;
                    $stats->total_co2_saved += $totalWeight * 2.5;
                    $stats->save();
                }

                // 3. Ubah status
                $deposit->update(['status' => 'approved']);
            }
            return back()->with('success', 'Poin berhasil dikreditkan ke dompet warga.');
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
        $wasteTypes = WasteType::all()->keyBy('id');
        $weightDetails = [];
        $totalPrice = 0;

        foreach ($request->input('weights', []) as $typeId => $weight) {
            $weight = floatval($weight);
            if ($weight > 0) {
                $weightDetails[$typeId] = $weight;
                if (isset($wasteTypes[$typeId])) {
                    $totalPrice += $weight * $wasteTypes[$typeId]->price_per_kg;
                }
            }
        }

        WasteListing::create([
            'title' => $request->input('title', 'Sampah Terpilah'),
            'description' => $request->input('description', ''),
            'weight_details' => json_encode($weightDetails),
            'total_price' => $totalPrice,
            'status' => 'available',
        ]);

        return redirect()->route('bank-sampah.stok')->with('success', 'Listing berhasil dibuat.');
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
        $withdrawal = Withdrawal::findOrFail($id);
        $stats = SystemStat::first();

        if ($stats && $stats->bank_sampah_cash >= $withdrawal->equivalent_rp) {
            $withdrawal->update(['status' => 'approved']);
            $stats->bank_sampah_cash -= $withdrawal->equivalent_rp;
            $stats->save();
            return back()->with('success', 'Penarikan tunai disetujui.');
        }

        return back()->with('error', 'Kas bank sampah tidak mencukupi.');
    }

    /**
     * Bayar settlement UMKM.
     */
    public function paySettlement($id)
    {
        $settlement = Settlement::findOrFail($id);
        $stats = SystemStat::first();

        if ($stats && $stats->bank_sampah_cash >= $settlement->total_amount) {
            $settlement->update(['status' => 'paid', 'paid_at' => now()]);
            $stats->bank_sampah_cash -= $settlement->total_amount;
            $stats->save();
            return back()->with('success', 'Settlement UMKM berhasil dibayar.');
        }

        return back()->with('error', 'Kas bank sampah tidak mencukupi untuk membayar settlement ini.');
    }
}

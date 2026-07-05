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
    public function verifikasi()
    {
        $deposits = WasteDeposit::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();
        $wasteTypes = WasteType::all()->keyBy('id');

        return view('bank_sampah.verifikasi', compact('deposits', 'wasteTypes'));
    }

    /**
     * Proses verifikasi (approve/reject/revise) deposit.
     */
    public function processDeposit(Request $request, $id)
    {
        $deposit = WasteDeposit::findOrFail($id);
        $action = $request->input('action'); // approve, reject, revise
        $operator = Auth::user();
        $wasteTypes = WasteType::all()->keyBy('id');

        if ($action === 'approve' || $action === 'revise') {
            // Hitung ulang berdasarkan berat aktual dari operator
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
                'status' => $action === 'approve' ? 'approved' : 'revised',
                'weight_details' => json_encode($weightDetails),
                'total_points' => $totalPoints,
                'notes' => $request->input('notes', $deposit->notes),
            ]);

            // Tambah poin ke saldo warga
            $warga = $deposit->user;
            $warga->point_balance += $totalPoints;
            $warga->save();

            // Update statistik
            $totalWeight = array_sum($weightDetails);
            $stats = SystemStat::first();
            if ($stats) {
                $stats->total_landfill_saved += $totalWeight;
                $stats->total_co2_saved += $totalWeight * 2.5;
                $stats->save();
            }

        } elseif ($action === 'reject') {
            $deposit->update([
                'collector_id' => $operator->id,
                'status' => 'rejected',
                'notes' => $request->input('notes', 'Sampah tidak memenuhi syarat.'),
            ]);
        }

        return redirect()->route('bank-sampah.verifikasi')->with('success', 'Deposit berhasil diproses.');
    }

    /**
     * Halaman manajemen stok & listing B2B.
     */
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

        return view('bank_sampah.stok', compact('wasteTypes', 'listings', 'stokPerType'));
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

        return view('bank_sampah.settlement', compact('withdrawals', 'settlements', 'stats'));
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

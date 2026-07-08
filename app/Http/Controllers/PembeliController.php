<?php

namespace App\Http\Controllers;

use App\Models\SystemStat;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WasteListing;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembeliController extends Controller
{
    /**
     * Dashboard Pembeli Industri: lihat listing yang tersedia.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $listings = WasteListing::orderByDesc('created_at')->get();
        $wasteTypes = WasteType::all()->keyBy('id');
        $transactions = WalletTransaction::where('user_id', $user->id)->orderByDesc('created_at')->get();

        return view('pembeli.dashboard', compact('user', 'listings', 'wasteTypes', 'transactions'));
    }

    /**
     * Beli listing sampah.
     */
    public function buyListing($id)
    {
        $result = DB::transaction(function () use ($id): array {
            $listing = WasteListing::lockForUpdate()->findOrFail($id);
            $user = User::lockForUpdate()->findOrFail(Auth::id());

            if ($listing->status !== 'available') {
                return ['error' => 'Listing ini sudah terjual.'];
            }

            if ($user->cash_balance < $listing->total_price) {
                return ['error' => 'Saldo kas tidak mencukupi untuk membeli listing ini.'];
            }

            $stats = SystemStat::firstOrCreate(
                ['id' => 1],
                ['bank_sampah_cash' => 0]
            );

            $user->cash_balance -= $listing->total_price;
            $user->save();

            $listing->update([
                'status' => 'sold',
                'buyer_id' => $user->id,
                'sold_at' => now(),
            ]);

            $stats->bank_sampah_cash += $listing->total_price;
            $stats->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'pembelian_sampah',
                'biller_name' => 'Marketplace B2B (Pembelian Listing)',
                'account_number' => $listing->title,
                'points_spent' => 0,
                'nominal_rp' => $listing->total_price,
                'ref_number' => 'B2B-'.strtoupper(Str::random(8)),
                'status' => 'success',
            ]);

            return ['success' => true];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('pembeli.dashboard')->with('success', 'Pembelian berhasil! Saldo kas Bank Sampah telah diperbarui.');
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        DB::transaction(function () use ($request): void {
            $user = User::lockForUpdate()->findOrFail(Auth::id());
            $user->cash_balance += $request->amount;
            $user->save();
        });

        return back()->with('success', 'Top-up berhasil! Saldo kas perusahaan Anda telah ditambahkan sebesar Rp '.number_format($request->amount, 0, ',', '.').'.');
    }
}

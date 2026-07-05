<?php

namespace App\Http\Controllers;

use App\Models\WasteListing;
use App\Models\SystemStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WasteType;

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

        return view('pembeli.dashboard', compact('user', 'listings', 'wasteTypes'));
    }

    /**
     * Beli listing sampah.
     */
    public function buyListing($id)
    {
        $listing = WasteListing::findOrFail($id);
        $user = Auth::user();

        if ($listing->status !== 'available') {
            return back()->with('error', 'Listing ini sudah terjual.');
        }

        // Simulasi pembayaran: kurangi cash_balance pembeli, tambah kas bank sampah
        if ($user->cash_balance < $listing->total_price) {
            return back()->with('error', 'Saldo kas tidak mencukupi untuk membeli listing ini.');
        }

        $user->cash_balance -= $listing->total_price;
        $user->save();

        $listing->update([
            'status' => 'sold',
            'buyer_id' => $user->id,
            'sold_at' => now(),
        ]);

        // Tambahkan ke kas bank sampah
        $stats = SystemStat::first();
        if ($stats) {
            $stats->bank_sampah_cash += $listing->total_price;
            $stats->save();
        }

        return back()->with('success', 'Pembelian berhasil! Rp ' . number_format($listing->total_price, 0, ',', '.') . ' telah ditransfer ke kas Bank Sampah.');
    }
}

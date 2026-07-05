<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WasteDeposit;
use App\Models\SystemStat;
use App\Models\UmkmPartner;
use Illuminate\Http\Request;

class DashboardDampakController extends Controller
{
    /**
     * Halaman utama publik: dashboard dampak lingkungan.
     */
    public function index()
    {
        $stats = SystemStat::first();
        $totalWarga = User::where('role', 'warga')->count();
        $totalUmkm = UmkmPartner::count();
        $totalDeposits = WasteDeposit::where('status', 'approved')->count();

        // Hitung total berat sampah terkelola
        $totalWeight = 0;
        $approvedDeposits = WasteDeposit::where('status', 'approved')->get();
        foreach ($approvedDeposits as $dep) {
            $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
            if ($details) {
                $totalWeight += array_sum($details);
            }
        }

        return view('welcome', compact('stats', 'totalWarga', 'totalUmkm', 'totalDeposits', 'totalWeight'));
    }
}

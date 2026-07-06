<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WasteDeposit;
use App\Models\SystemStat;
use App\Models\UmkmPartner;
use App\Models\Voucher;
use App\Models\Withdrawal;
use App\Models\UmkmProduct;
use App\Models\WasteListing;
use App\Models\Settlement;
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
        $totalVouchers = Voucher::count();

        // Hitung total berat sampah terkelola
        $totalWeight = 0;
        $approvedDeposits = WasteDeposit::where('status', 'approved')->get();
        foreach ($approvedDeposits as $dep) {
            $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
            if ($details) {
                $totalWeight += array_sum($details);
            }
        }

        return view('welcome', compact('stats', 'totalWarga', 'totalUmkm', 'totalDeposits', 'totalWeight', 'totalVouchers'));
    }

    /**
     * Dasbor Dampak Real-time khusus pengguna terautentikasi (semua role).
     */
    public function dampakRealtime(Request $request)
    {
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $start = null;
        $end = null;

        if ($startDateInput && $endDateInput) {
            $start = \Carbon\Carbon::parse($startDateInput)->startOfDay();
            $end = \Carbon\Carbon::parse($endDateInput)->endOfDay();
        }

        // Base queries
        $depositQuery = WasteDeposit::where('status', 'approved');
        $voucherQuery = Voucher::query();
        $withdrawalQuery = Withdrawal::where('status', 'approved');

        // Apply filters
        if ($start && $end) {
            $depositQuery->whereBetween('created_at', [$start, $end]);
            $voucherQuery->whereBetween('created_at', [$start, $end]);
            $withdrawalQuery->whereBetween('created_at', [$start, $end]);
        }

        // Totals
        $totalWarga = User::where('role', 'warga')->count(); // Warga is cumulative all-time
        $totalUmkm = UmkmPartner::count(); // UMKM is cumulative all-time
        $totalDeposits = $depositQuery->count();
        $totalVouchers = $voucherQuery->count();

        // Hitung total berat sampah terkelola
        $totalWeight = 0;
        $approvedDeposits = $depositQuery->get();
        foreach ($approvedDeposits as $dep) {
            $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
            if ($details) {
                $totalWeight += array_sum($details);
            }
        }

        // Hitung total perputaran uang (Voucher + Withdrawal Cash)
        $totalVoucherRp = $voucherQuery->sum('points_spent') * 10;
        $totalWithdrawalRp = $withdrawalQuery->sum('equivalent_rp');
        $totalCashDistributed = $totalVoucherRp + $totalWithdrawalRp;

        // Tentukan format dan labels grafik berdasarkan selisih waktu
        $diffInDays = ($start && $end) ? $start->diffInDays($end) : null;
        
        // Gunakan harian jika rentang <= 31 hari
        if ($diffInDays !== null && $diffInDays <= 31) {
            $format = 'd M';
            $months = [];
            $tempDate = clone $start;
            while ($tempDate->lte($end)) {
                $months[] = $tempDate->translatedFormat($format);
                $tempDate->addDay();
            }
        } else {
            // Gunakan bulanan
            $format = 'M Y';
            $months = [];

            if ($start && $end) {
                $tempDate = clone $start;
                while ($tempDate->lte($end)) {
                    $months[] = $tempDate->translatedFormat($format);
                    $tempDate->addMonth();
                }
            } else {
                // Default 6 bulan terakhir
                for ($i = 5; $i >= 0; $i--) {
                    $months[] = now()->subMonths($i)->translatedFormat($format);
                }
            }
        }

        // Grouping data di level PHP untuk kompatibilitas database universal
        $depositsGrouped = $approvedDeposits->groupBy(function ($deposit) use ($format) {
            return $deposit->created_at->translatedFormat($format);
        });

        $vouchersGrouped = $voucherQuery->get()->groupBy(function ($voucher) use ($format) {
            return $voucher->created_at->translatedFormat($format);
        });

        $withdrawalsGrouped = $withdrawalQuery->get()->groupBy(function ($withdrawal) use ($format) {
            return $withdrawal->created_at->translatedFormat($format);
        });

        $weightTrend = [];
        $co2Trend = [];
        $vouchersTrend = [];
        $cashTrend = [];

        foreach ($months as $m) {
            // 1. Berat Sampah (kg) & CO2
            $weight = 0;
            if (isset($depositsGrouped[$m])) {
                foreach ($depositsGrouped[$m] as $dep) {
                    $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
                    if ($details) {
                        $weight += array_sum($details);
                    }
                }
            }
            $weightTrend[] = round($weight, 1);
            $co2Trend[] = round($weight * 1.2, 1);

            // 2. Voucher
            $vouchersTrend[] = isset($vouchersGrouped[$m]) ? $vouchersGrouped[$m]->count() : 0;

            // 3. Tunai Rp
            $cashTrend[] = isset($withdrawalsGrouped[$m]) ? (int)$withdrawalsGrouped[$m]->sum('equivalent_rp') : 0;
        }

        // Hitung data spesifik role
        $role = auth()->user()->role;
        $roleData = [];

        if ($role === 'warga') {
            $personalDepositsQuery = WasteDeposit::where('user_id', auth()->id())->where('status', 'approved');
            $personalVouchersQuery = Voucher::where('user_id', auth()->id());
            
            if ($start && $end) {
                $personalDepositsQuery->whereBetween('created_at', [$start, $end]);
                $personalVouchersQuery->whereBetween('created_at', [$start, $end]);
            }
            
            $personalDeposits = $personalDepositsQuery->get();
            $personalWeight = 0;
            foreach ($personalDeposits as $dep) {
                $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
                if ($details) {
                    $personalWeight += array_sum($details);
                }
            }
            
            $roleData = [
                'personal_weight' => $personalWeight,
                'personal_deposits_count' => $personalDepositsQuery->count(),
                'personal_vouchers_count' => $personalVouchersQuery->count(),
                'personal_points_earned' => $personalDeposits->sum('total_points')
            ];
        } elseif ($role === 'bank_sampah') {
            $pendingDeposits = WasteDeposit::where('status', 'pending')->count();
            $pendingSettlements = Settlement::where('status', 'pending')->count();
            $systemStat = SystemStat::first();
            $cashBalance = $systemStat ? $systemStat->bank_sampah_cash : 0;
            $unpaidSettlementAmount = Settlement::where('status', 'pending')->sum('total_amount');
            
            $roleData = [
                'pending_deposits' => $pendingDeposits,
                'pending_settlements' => $pendingSettlements,
                'cash_balance' => $cashBalance,
                'unpaid_settlement_amount' => $unpaidSettlementAmount
            ];
        } elseif ($role === 'umkm') {
            $partner = UmkmPartner::where('user_id', auth()->id())->first();
            if ($partner) {
                $productIds = UmkmProduct::where('umkm_partner_id', $partner->id)->pluck('id');
                $shopVouchersQuery = Voucher::whereIn('umkm_product_id', $productIds);
                $shopSettlementsQuery = Settlement::where('umkm_partner_id', $partner->id);
                
                if ($start && $end) {
                    $shopVouchersQuery->whereBetween('created_at', [$start, $end]);
                    $shopSettlementsQuery->whereBetween('created_at', [$start, $end]);
                }
                
                $claimedVouchers = $shopVouchersQuery->count();
                $totalRevenue = $shopVouchersQuery->sum('points_spent') * 10;
                $pendingSettlementAmount = $shopSettlementsQuery->where('status', 'pending')->sum('total_amount');
                $activeProducts = UmkmProduct::where('umkm_partner_id', $partner->id)->count();
                
                $roleData = [
                    'claimed_vouchers' => $claimedVouchers,
                    'total_revenue' => $totalRevenue,
                    'pending_settlement_amount' => $pendingSettlementAmount,
                    'active_products' => $activeProducts,
                    'has_shop' => true,
                    'store_name' => $partner->store_name
                ];
            } else {
                $roleData = ['has_shop' => false];
            }
        } elseif ($role === 'pembeli') {
            $buyerListingsQuery = WasteListing::where('buyer_id', auth()->id())->where('status', 'sold');
            
            if ($start && $end) {
                $buyerListingsQuery->whereBetween('sold_at', [$start, $end]);
            }
            
            $listings = $buyerListingsQuery->get();
            $buyerWeight = 0;
            foreach ($listings as $listing) {
                $details = is_array($listing->weight_details) ? $listing->weight_details : json_decode($listing->weight_details, true);
                if ($details) {
                    $buyerWeight += array_sum($details);
                }
            }
            
            $roleData = [
                'buyer_weight' => $buyerWeight,
                'buyer_purchases_count' => $buyerListingsQuery->count(),
                'buyer_spent' => $buyerListingsQuery->sum('total_price'),
                'buyer_balance' => auth()->user()->point_balance * 10
            ];
        }

        return view('dampak.realtime', compact(
            'totalWarga',
            'totalUmkm',
            'totalDeposits',
            'totalWeight',
            'totalVouchers',
            'totalCashDistributed',
            'months',
            'weightTrend',
            'co2Trend',
            'vouchersTrend',
            'cashTrend',
            'startDateInput',
            'endDateInput',
            'roleData'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WasteType;
use App\Models\WasteDeposit;
use App\Models\UmkmPartner;
use App\Models\UmkmProduct;
use App\Models\Voucher;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WargaController extends Controller
{
    /**
     * Dasbor Warga: ringkasan saldo, riwayat, dan statistik pribadi.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $deposits = WasteDeposit::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        $vouchers = Voucher::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        $walletTransactions = WalletTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $totalDeposits = WasteDeposit::where('user_id', $user->id)->where('status', 'approved')->count();
        $totalWeight = 0;
        $approvedDeposits = WasteDeposit::where('user_id', $user->id)->where('status', 'approved')->get();
        foreach ($approvedDeposits as $dep) {
            if ($dep->weight_details) {
                $details = is_array($dep->weight_details) ? $dep->weight_details : json_decode($dep->weight_details, true);
                $totalWeight += array_sum($details ?: []);
            }
        }

        $wasteTypes = WasteType::all();

        return view('warga.dashboard', compact('user', 'deposits', 'vouchers', 'withdrawals', 'walletTransactions', 'totalDeposits', 'totalWeight', 'wasteTypes'));
    }

    /**
     * Halaman setor sampah: form pengajuan & kalkulator estimasi.
     */
    public function setor()
    {
        // Pastikan tipe sampah tambahan tersedia
        \App\Models\WasteType::firstOrCreate(
            ['name' => 'Organik (Sisa Makanan & Dapur)'],
            [
                'icon' => '🍂',
                'points_per_kg' => 50,
                'price_per_kg' => 500,
            ]
        );
        \App\Models\WasteType::firstOrCreate(
            ['name' => 'Residu (Popok, Pembalut, Tissue)'],
            [
                'icon' => '🗑️',
                'points_per_kg' => 10,
                'price_per_kg' => 100,
            ]
        );

        $wasteTypes = WasteType::all();
        return view('warga.setor', compact('wasteTypes'));
    }

    /**
     * Simpan pengajuan setor sampah.
     */
    public function storDeposit(Request $request)
    {
        $user = Auth::user();

        $weightDetails = [];
        $totalPoints = 0;
        $wasteTypes = WasteType::all()->keyBy('id');

        foreach ($request->input('weights', []) as $typeId => $weight) {
            $weight = floatval($weight);
            if ($weight > 0) {
                $weightDetails[$typeId] = $weight;
                if (isset($wasteTypes[$typeId])) {
                    $totalPoints += (int) ($weight * $wasteTypes[$typeId]->points_per_kg);
                }
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo_proof')) {
            $photoPath = $request->file('photo_proof')->store('deposits', 'public');
        } elseif ($request->input('captured_photo')) {
            $base64Data = $request->input('captured_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($matches[1]);
                if (in_array($type, ['jpg', 'jpeg', 'png'])) {
                    $data = base64_decode($data);
                    if ($data !== false) {
                        $fileName = 'captured_' . time() . '_' . uniqid() . '.' . $type;
                        \Illuminate\Support\Facades\Storage::disk('public')->put('deposits/' . $fileName, $data);
                        $photoPath = 'deposits/' . $fileName;
                    }
                }
            }
        }

        WasteDeposit::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'collection_method' => $request->input('method', 'antar'),
            'schedule_date' => $request->input('schedule_date', now()->addDay()),
            'address' => $request->input('address', $user->address),
            'weight_details' => json_encode($weightDetails),
            'total_points' => $totalPoints,
            'notes' => $request->input('notes'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'photo_proof' => $photoPath,
        ]);

        return redirect()->route('warga.dashboard')->with('success', 'Pengajuan setoran sampah berhasil dikirim! Menunggu verifikasi operator Bank Sampah.');
    }

    /**
     * Katalog UMKM Mitra: browse produk & tukar poin (terintegrasi dengan peta).
     */
    public function umkm()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $partners = UmkmPartner::with('products')->get();

        // Koordinat default Bank Sampah Lestari
        $bankSampahName = "Bank Sampah Lestari Karawang";
        $bankSampahAddress = "Jl. Tuparev No. 1, RT 01/RW 07, Kel. Karawang Kulon, Karawang";
        $bankSampahLat = -6.3024;
        $bankSampahLon = 107.3065;

        return view('warga.umkm', compact('user', 'partners', 'bankSampahName', 'bankSampahAddress', 'bankSampahLat', 'bankSampahLon'));
    }

    /**
     * Tukar poin dengan produk UMKM.
     */
    public function redeemProduct(Request $request, $productId)
    {
        $user = Auth::user();
        $product = UmkmProduct::findOrFail($productId);

        if ($user->point_balance < $product->points_cost) {
            return back()->with('error', 'Saldo poin tidak mencukupi untuk menukarkan produk ini.');
        }

        if ($product->stock <= 0) {
            return back()->with('error', 'Stok produk habis.');
        }

        // Kurangi saldo poin
        $user->point_balance -= $product->points_cost;
        $user->save();

        // Kurangi stok
        $product->stock -= 1;
        $product->save();

        // Buat voucher
        $voucher = Voucher::create([
            'user_id' => $user->id,
            'umkm_product_id' => $product->id,
            'code' => 'WL-' . strtoupper(Str::random(8)),
            'points_spent' => $product->points_cost,
            'status' => 'unused',
        ]);

        return redirect()->route('warga.dashboard')->with('success', 'Voucher berhasil dibuat! Kode: ' . $voucher->code);
    }

    /**
     * Ajukan penarikan tunai.
     */
    public function withdraw(Request $request)
    {
        $user = Auth::user();
        $points = (int) $request->input('points_amount', 0);

        if ($points <= 0 || $points > $user->point_balance) {
            return back()->with('error', 'Jumlah poin tidak valid atau melebihi saldo.');
        }

        // 1 poin ≈ Rp 10
        $equivalentRp = $points * 10;

        $user->point_balance -= $points;
        $user->save();

        Withdrawal::create([
            'user_id' => $user->id,
            'bank_name' => $request->input('bank_name', 'BCA'),
            'account_number' => $request->input('account_number', ''),
            'account_name' => $request->input('account_name', $user->name),
            'points_amount' => $points,
            'equivalent_rp' => $equivalentRp,
            'status' => 'pending',
        ]);

        return redirect()->route('warga.dashboard')->with('success', 'Pengajuan pencairan tunai sebesar ' . number_format($equivalentRp, 0, ',', '.') . ' rupiah berhasil dikirim.');
    }

    /**
     * Tampilan modul Pulsa & Tagihan.
     */
    public function bills()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('warga.bills', compact('user', 'transactions'));
    }

    /**
     * Proses transaksi Top-Up & Tagihan.
     */
    public function payBill(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $type = $request->input('transaction_type'); // topup / bill
        $biller = $request->input('biller_name');
        $accountNumber = $request->input('account_number');
        $nominalRp = floatval($request->input('nominal_rp', 0));

        // 1 poin = Rp 10. Jadi biaya poin = nominal_rp / 10
        $pointsSpent = intval($nominalRp / 10);

        if ($pointsSpent <= 0) {
            return back()->with('error', 'Nominal transaksi tidak valid.');
        }

        if ($user->point_balance < $pointsSpent) {
            return back()->with('error', 'Saldo poin Anda tidak mencukupi untuk transaksi ini. Dibutuhkan ' . number_format($pointsSpent, 0, ',', '.') . ' poin.');
        }

        // Potong Poin Warga
        $user->point_balance -= $pointsSpent;
        $user->save();

        // Buat nomor referensi transaksi
        $refNumber = 'WL' . date('YmdHis') . strtoupper(Str::random(4));

        $tx = WalletTransaction::create([
            'user_id' => $user->id,
            'transaction_type' => $type,
            'biller_name' => $biller,
            'account_number' => $accountNumber,
            'points_spent' => $pointsSpent,
            'nominal_rp' => $nominalRp,
            'ref_number' => $refNumber,
            'status' => 'success',
        ]);

        return back()->with('receipt', $tx)->with('success', 'Transaksi ' . $biller . ' berhasil diproses!');
    }

    /**
     * Tampilkan halaman pengaturan & edit profil warga.
     */
    public function settings()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('warga.settings', compact('user'));
    }

    /**
     * Simpan perubahan profil warga & password.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Validate basic fields
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ];

        // If trying to change password
        if ($request->filled('current_password') || $request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Verify current password if changing password
        if ($request->filled('current_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        if ($request->boolean('remove_profile_photo')) {
            $this->deleteProfilePhoto($user);
            $user->profile_photo = null;
        }

        if ($request->hasFile('profile_photo')) {
            $this->deleteProfilePhoto($user);
            $user->profile_photo = $request->file('profile_photo')->store('profiles', 'public');
        }

        // Update fields
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->save();

        Auth::setUser($user->fresh());

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    private function deleteProfilePhoto(User $user): void
    {
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }
    }

}

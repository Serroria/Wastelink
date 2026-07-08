<?php

namespace App\Http\Controllers;

use App\Models\Settlement;
use App\Models\UmkmPartner;
use App\Models\UmkmProduct;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UmkmController extends Controller
{
    /**
     * Dasbor UMKM Mitra.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $partner = UmkmPartner::where('user_id', $user->id)->with('products')->first();

        if (! $partner) {
            return view('umkm.dashboard', ['partner' => null, 'vouchers' => collect(), 'settlements' => collect()]);
        }

        $productIds = $partner->products->pluck('id');
        $vouchers = Voucher::whereIn('umkm_product_id', $productIds)
            ->with(['user', 'product'])
            ->orderByDesc('created_at')
            ->get();

        $settlements = Settlement::where('umkm_partner_id', $partner->id)
            ->orderByDesc('created_at')
            ->get();

        return view('umkm.dashboard', compact('partner', 'vouchers', 'settlements'));
    }

    /**
     * Mendaftar sebagai UMKM Mitra Baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'description' => 'required|string|max:1000',
        ]);

        if (UmkmPartner::where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Anda sudah memiliki pengajuan atau toko UMKM.');
        }

        UmkmPartner::create([
            'user_id' => Auth::id(),
            'store_name' => $request->store_name,
            'category' => $request->category,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'pending', // Menunggu persetujuan
            'latitude' => -6.3024, // Bisa disesuaikan dengan input map nantinya
            'longitude' => 107.3065,
        ]);

        return back()->with('success', 'Pengajuan kemitraan berhasil dikirim. Menunggu persetujuan Operator Bank Sampah.');
    }

    /**
     * UMKM Menambahkan Produk/Voucher Baru
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points_cost' => ['required', 'integer', 'min:10'],
            'stock' => ['required', 'integer', 'min:1'],
        ]);

        $partner = UmkmPartner::where('user_id', Auth::id())->first();

        if (! $partner || $partner->status !== 'approved') {
            return back()->with('error', 'Toko Anda belum disetujui.');
        }

        UmkmProduct::create([
            'umkm_partner_id' => $partner->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'points_cost' => $validated['points_cost'],
            'price_value' => $validated['points_cost'] * 10, // Asumsi 1 poin = Rp 10
            'stock' => $validated['stock'],
        ]);

        return back()->with('success', 'Produk/Voucher berhasil ditambahkan ke katalog.');
    }

    /**
     * UMKM Memperbarui Data Produk/Voucher
     */
    public function updateProduct(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points_cost' => ['required', 'integer', 'min:10'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $partner = UmkmPartner::where('user_id', Auth::id())->first();
        $product = UmkmProduct::findOrFail($id);

        // Keamanan: Pastikan produk ini benar-benar milik UMKM yang sedang login
        if (! $partner || $product->umkm_partner_id !== $partner->id) {
            return back()->with('error', 'Akses ditolak. Anda tidak dapat mengubah produk ini.');
        }

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'points_cost' => $validated['points_cost'],
            'price_value' => $validated['points_cost'] * 10, // Menyesuaikan dengan harga poin baru
            'stock' => $validated['stock'],
        ]);

        return back()->with('success', 'Produk/Voucher berhasil diperbarui!');
    }

    /**
     * UMKM Menghapus Produk/Voucher
     */
    public function deleteProduct($id)
    {
        $partner = UmkmPartner::where('user_id', Auth::id())->first();
        $product = UmkmProduct::findOrFail($id);

        // Keamanan: Pastikan produk ini benar-benar milik UMKM yang sedang login
        if (! $partner || $product->umkm_partner_id !== $partner->id) {
            return back()->with('error', 'Akses ditolak. Anda tidak dapat menghapus produk ini.');
        }

        $product->delete();

        return back()->with('success', 'Produk/Voucher berhasil dihapus dari katalog.');
    }

    /**
     * Validasi kode voucher warga.
     */
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = strtoupper(trim($request->input('code', '')));
        $user = Auth::user();
        $partner = UmkmPartner::where('user_id', $user->id)->first();

        if (! $partner || $partner->status !== 'approved') {
            return back()->with('error', 'Anda belum terdaftar sebagai mitra UMKM.');
        }

        $productIds = $partner->products->pluck('id');
        $result = DB::transaction(function () use ($code, $productIds): array {
            $voucher = Voucher::where('code', $code)
                ->whereIn('umkm_product_id', $productIds)
                ->lockForUpdate()
                ->first();

            if (! $voucher) {
                return ['error' => 'Kode voucher tidak ditemukan atau bukan milik toko Anda.'];
            }

            if ($voucher->status === 'used') {
                return ['error' => 'Voucher ini sudah pernah digunakan pada '.$voucher->used_at->format('d M Y H:i').'.'];
            }

            if ($voucher->status === 'claimed') {
                return ['error' => 'Voucher ini sudah diklaim sebelumnya.'];
            }

            $voucher->update([
                'status' => 'used',
                'used_at' => now(),
            ]);

            return [
                'code' => $voucher->code,
                'product_name' => $voucher->product->name,
            ];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', 'Voucher '.$result['code'].' berhasil divalidasi! Produk: '.$result['product_name'].'. Silakan serahkan barang kepada warga.');
    }

    /**
     * Ajukan klaim settlement ke Bank Sampah.
     */
    public function claimSettlement()
    {
        $user = Auth::user();
        $partner = UmkmPartner::where('user_id', $user->id)->first();

        if (! $partner || $partner->status !== 'approved') {
            return back()->with('error', 'Anda belum terdaftar sebagai mitra UMKM.');
        }

        $result = DB::transaction(function () use ($partner): array {
            $productIds = $partner->products->pluck('id');
            $usedVouchers = Voucher::whereIn('umkm_product_id', $productIds)
                ->where('status', 'used')
                ->lockForUpdate()
                ->get();

            if ($usedVouchers->isEmpty()) {
                return ['error' => 'Tidak ada voucher yang perlu diklaim saat ini.'];
            }

            $totalAmount = 0;
            $voucherIds = [];
            foreach ($usedVouchers as $voucher) {
                $totalAmount += $voucher->product->price_value;
                $voucherIds[] = $voucher->id;
                $voucher->update(['status' => 'claimed', 'claimed_at' => now()]);
            }

            Settlement::create([
                'umkm_partner_id' => $partner->id,
                'total_amount' => $totalAmount,
                'voucher_ids' => $voucherIds,
                'status' => 'pending',
            ]);

            return ['total_amount' => $totalAmount];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', 'Klaim settlement berhasil diajukan sebesar Rp '.number_format($result['total_amount'], 0, ',', '.').'. Menunggu pembayaran dari Bank Sampah.');
    }
}

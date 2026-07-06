<?php

namespace App\Http\Controllers;

use App\Models\UmkmPartner;
use App\Models\UmkmProduct;
use App\Models\Voucher;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UmkmController extends Controller
{
    /**
     * Dasbor UMKM Mitra.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $partner = UmkmPartner::where('user_id', $user->id)->with('products')->first();

        if (!$partner) {
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
            'category' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string'
        ]);

        UmkmPartner::create([
            'user_id' => Auth::id(),
            'store_name' => $request->store_name,
            'category' => $request->category,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'pending', // Menunggu persetujuan
            'latitude' => -6.3024, // Bisa disesuaikan dengan input map nantinya
            'longitude' => 107.3065
        ]);

        return back()->with('success', 'Pengajuan kemitraan berhasil dikirim. Menunggu persetujuan Operator Bank Sampah.');
    }

    /**
     * UMKM Menambahkan Produk/Voucher Baru
     */
    public function storeProduct(Request $request)
    {
        $partner = UmkmPartner::where('user_id', Auth::id())->first();

        if (!$partner || $partner->status !== 'approved') {
            return back()->with('error', 'Toko Anda belum disetujui.');
        }

        UmkmProduct::create([
            'umkm_partner_id' => $partner->id,
            'name' => $request->name,
            'description' => $request->description,
            'points_cost' => $request->points_cost,
            'price_value' => $request->points_cost * 10, // Asumsi 1 poin = Rp 10
            'stock' => $request->stock
        ]);

        return back()->with('success', 'Produk/Voucher berhasil ditambahkan ke katalog.');
    }

    /**
     * UMKM Memperbarui Data Produk/Voucher
     */
    public function updateProduct(Request $request, $id)
    {
        $partner = UmkmPartner::where('user_id', Auth::id())->first();
        $product = UmkmProduct::findOrFail($id);

        // Keamanan: Pastikan produk ini benar-benar milik UMKM yang sedang login
        if (!$partner || $product->umkm_partner_id !== $partner->id) {
            return back()->with('error', 'Akses ditolak. Anda tidak dapat mengubah produk ini.');
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'points_cost' => $request->points_cost,
            'price_value' => $request->points_cost * 10, // Menyesuaikan dengan harga poin baru
            'stock' => $request->stock
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
        if (!$partner || $product->umkm_partner_id !== $partner->id) {
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
        $code = strtoupper(trim($request->input('code', '')));
        $user = Auth::user();
        $partner = UmkmPartner::where('user_id', $user->id)->first();

        if (!$partner) {
            return back()->with('error', 'Anda belum terdaftar sebagai mitra UMKM.');
        }

        $productIds = $partner->products->pluck('id');
        $voucher = Voucher::where('code', $code)
            ->whereIn('umkm_product_id', $productIds)
            ->first();

        if (!$voucher) {
            return back()->with('error', 'Kode voucher tidak ditemukan atau bukan milik toko Anda.');
        }

        if ($voucher->status === 'used') {
            return back()->with('error', 'Voucher ini sudah pernah digunakan pada ' . $voucher->used_at->format('d M Y H:i') . '.');
        }

        if ($voucher->status === 'claimed') {
            return back()->with('error', 'Voucher ini sudah diklaim sebelumnya.');
        }

        // Tandai voucher sebagai terpakai
        $voucher->update([
            'status' => 'used',
            'used_at' => now(),
        ]);

        return back()->with('success', 'Voucher ' . $code . ' berhasil divalidasi! Produk: ' . $voucher->product->name . '. Silakan serahkan barang kepada warga.');
    }

    /**
     * Ajukan klaim settlement ke Bank Sampah.
     */
    public function claimSettlement()
    {
        $user = Auth::user();
        $partner = UmkmPartner::where('user_id', $user->id)->first();

        if (!$partner) {
            return back()->with('error', 'Anda belum terdaftar sebagai mitra UMKM.');
        }

        // Cari voucher yang sudah dipakai tapi belum diklaim
        $productIds = $partner->products->pluck('id');
        $usedVouchers = Voucher::whereIn('umkm_product_id', $productIds)
            ->where('status', 'used')
            ->get();

        if ($usedVouchers->isEmpty()) {
            return back()->with('error', 'Tidak ada voucher yang perlu diklaim saat ini.');
        }

        // Hitung total & buat settlement
        $totalAmount = 0;
        $voucherIds = [];
        foreach ($usedVouchers as $v) {
            $totalAmount += $v->product->price_value;
            $voucherIds[] = $v->id;
            $v->update(['status' => 'claimed', 'claimed_at' => now()]);
        }

        Settlement::create([
            'umkm_partner_id' => $partner->id,
            'total_amount' => $totalAmount,
            'voucher_ids' => json_encode($voucherIds),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Klaim settlement berhasil diajukan sebesar Rp ' . number_format($totalAmount, 0, ',', '.') . '. Menunggu pembayaran dari Bank Sampah.');
    }
}

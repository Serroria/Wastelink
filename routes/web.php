<?php

use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\DashboardDampakController;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

// Halaman utama publik (Dashboard Dampak)
Route::get('/', [DashboardDampakController::class, 'index'])->name('home');
Route::get('/dampak/realtime', [DashboardDampakController::class, 'dampakRealtime'])->middleware('auth')->name('dampak.realtime');

// Universal Settings (semua role)
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Auth Page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Register Routes
Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

// Demo Auth: switch role tanpa login form
Route::post('/demo/switch-role', [DemoAuthController::class, 'switchRole'])->name('demo.switch-role');
Route::post('/demo/logout', [DemoAuthController::class, 'logout'])->name('demo.logout');

// Google Socialite Auth
Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('auth.google.callback');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('guest')->name('password.update');

// ===== WARGA =====
Route::prefix('warga')->name('warga.')->middleware(['auth', 'role:warga'])->group(function () {
    Route::get('/dashboard', [WargaController::class, 'dashboard'])->name('dashboard');
    Route::get('/setor', [WargaController::class, 'setor'])->name('setor');
    Route::post('/setor', [WargaController::class, 'storDeposit'])->name('setor.store');
    Route::get('/umkm', [WargaController::class, 'umkm'])->name('umkm');
    Route::post('/redeem/{productId}', [WargaController::class, 'redeemProduct'])->name('redeem');
    Route::post('/withdraw', [WargaController::class, 'withdraw'])->name('withdraw');
    Route::get('/bills', [WargaController::class, 'bills'])->name('bills');
    Route::post('/bills/pay', [WargaController::class, 'payBill'])->name('bills.pay');
    Route::get('/settings', [WargaController::class, 'settings'])->name('settings');
    Route::post('/settings', [WargaController::class, 'updateSettings'])->name('settings.update');
});

// ai detection image
Route::post('/warga/setor/analyze-ai', [TrashController::class, 'analyzeAi'])->name('warga.setor.analyze-ai')->middleware(['auth', 'role:warga']);
// ===== BANK SAMPAH =====
Route::prefix('bank-sampah')->name('bank-sampah.')->middleware(['auth', 'role:bank_sampah'])->group(function () {
    Route::get('/dashboard', [BankSampahController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi', [BankSampahController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/verifikasi/{id}', [BankSampahController::class, 'processDeposit'])->name('verifikasi.process');
    Route::get('/stok', [BankSampahController::class, 'stok'])->name('stok');
    Route::post('/stok/listing', [BankSampahController::class, 'createListing'])->name('stok.listing');
    Route::post('/stok/{id}/cancel', [BankSampahController::class, 'cancelListing'])->name('stok.cancel');
    Route::get('/settlement', [BankSampahController::class, 'settlement'])->name('settlement');
    Route::post('/settlement/withdraw/{id}', [BankSampahController::class, 'approveWithdrawal'])->name('settlement.withdraw');
    Route::post('/settlement/pay/{id}', [BankSampahController::class, 'paySettlement'])->name('settlement.pay');
    Route::post('/umkm/approve/{id}', [BankSampahController::class, 'approvePartner'])->name('umkm.approve');
    Route::post('/umkm/reject/{id}', [BankSampahController::class, 'rejectPartner'])->name('umkm.reject');
});

// ===== UMKM MITRA =====
Route::prefix('umkm')->name('umkm.')->middleware(['auth', 'role:umkm'])->group(function () {
    Route::get('/dashboard', [UmkmController::class, 'dashboard'])->name('dashboard');
    Route::post('/validate-voucher', [UmkmController::class, 'validateVoucher'])->name('validate-voucher');
    Route::post('/claim-settlement', [UmkmController::class, 'claimSettlement'])->name('claim-settlement');
    Route::post('/register', [UmkmController::class, 'register'])->name('register');
    Route::post('/product', [UmkmController::class, 'storeProduct'])->name('product.store');
    Route::put('/product/{id}', [UmkmController::class, 'updateProduct'])->name('product.update'); // Baris baru
    Route::delete('/product/{id}', [UmkmController::class, 'deleteProduct'])->name('product.destroy');
});

// ===== PEMBELI INDUSTRI =====
Route::prefix('pembeli')->name('pembeli.')->middleware(['auth', 'role:pembeli'])->group(function () {
    Route::get('/dashboard', [PembeliController::class, 'dashboard'])->name('dashboard');
    Route::post('/buy/{id}', [PembeliController::class, 'buyListing'])->name('buy');
    Route::post('/topup', [PembeliController::class, 'topup'])->name('topup');
});

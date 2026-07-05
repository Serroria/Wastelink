<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\DashboardDampakController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\PembeliController;

// Halaman utama publik (Dashboard Dampak)
Route::get('/', [DashboardDampakController::class, 'index'])->name('home');

// Auth Page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Register Routes
Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'store'])->middleware('guest');

// Demo Auth: switch role tanpa login form
Route::post('/demo/switch-role', [DemoAuthController::class, 'switchRole'])->name('demo.switch-role');
Route::post('/demo/logout', [DemoAuthController::class, 'logout'])->name('demo.logout');

// Google Socialite Auth
Route::get('/auth/google', [\App\Http\Controllers\SocialiteController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\SocialiteController::class, 'callback'])->name('auth.google.callback');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'edit'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'update'])->middleware('guest')->name('password.update');

// ===== WARGA =====
Route::prefix('warga')->name('warga.')->group(function () {
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

// ===== BANK SAMPAH =====
Route::prefix('bank-sampah')->name('bank-sampah.')->group(function () {
    Route::get('/dashboard', [BankSampahController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi', [BankSampahController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/verifikasi/{id}', [BankSampahController::class, 'processDeposit'])->name('verifikasi.process');
    Route::get('/stok', [BankSampahController::class, 'stok'])->name('stok');
    Route::post('/stok/listing', [BankSampahController::class, 'createListing'])->name('stok.listing');
    Route::get('/settlement', [BankSampahController::class, 'settlement'])->name('settlement');
    Route::post('/settlement/withdraw/{id}', [BankSampahController::class, 'approveWithdrawal'])->name('settlement.withdraw');
    Route::post('/settlement/pay/{id}', [BankSampahController::class, 'paySettlement'])->name('settlement.pay');
});

// ===== UMKM MITRA =====
Route::prefix('umkm')->name('umkm.')->group(function () {
    Route::get('/dashboard', [UmkmController::class, 'dashboard'])->name('dashboard');
    Route::post('/validate-voucher', [UmkmController::class, 'validateVoucher'])->name('validate-voucher');
    Route::post('/claim-settlement', [UmkmController::class, 'claimSettlement'])->name('claim-settlement');
});

// ===== PEMBELI INDUSTRI =====
Route::prefix('pembeli')->name('pembeli.')->group(function () {
    Route::get('/dashboard', [PembeliController::class, 'dashboard'])->name('dashboard');
    Route::post('/buy/{id}', [PembeliController::class, 'buyListing'])->name('buy');
});

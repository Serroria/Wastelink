<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jenis Sampah & Tarif
        Schema::create('waste_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Plastik, Kertas, Logam, Kaca, Organik
            $table->string('icon')->nullable(); // emoji icon
            $table->string('unit')->default('kg');
            $table->integer('points_per_kg')->default(0);
            $table->decimal('price_per_kg', 10, 2)->default(0); // harga jual B2B
            $table->timestamps();
        });

        // Penyetoran Sampah
        Schema::create('waste_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('collector_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, approved, revised, rejected
            $table->string('collection_method')->default('antar'); // antar, jemput
            $table->date('schedule_date')->nullable();
            $table->text('address')->nullable();
            $table->json('weight_details')->nullable(); // {"1": 2.5, "2": 4.0}
            $table->integer('total_points')->default(0);
            $table->string('photo_proof')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Data Mitra UMKM
        Schema::create('umkm_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('store_name');
            $table->string('category')->default('Sembako');
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Katalog Produk UMKM
        Schema::create('umkm_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_partner_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_cost')->default(0);
            $table->decimal('price_value', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // Voucher Belanja
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('umkm_product_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->integer('points_spent')->default(0);
            $table->string('status')->default('unused'); // unused, used, claimed
            $table->timestamp('used_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        // Penarikan Tunai Warga
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->integer('points_amount')->default(0);
            $table->decimal('equivalent_rp', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });

        // Lapak Jual Sampah B2B
        Schema::create('waste_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('weight_details')->nullable();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('status')->default('available'); // available, sold
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
        });

        // Klaim Pembayaran UMKM (Settlement)
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_partner_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->json('voucher_ids')->nullable();
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Statistik Sistem
        Schema::create('system_stats', function (Blueprint $table) {
            $table->id();
            $table->decimal('bank_sampah_cash', 12, 2)->default(0);
            $table->decimal('total_co2_saved', 10, 2)->default(0);
            $table->decimal('total_landfill_saved', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_stats');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('waste_listings');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('umkm_products');
        Schema::dropIfExists('umkm_partners');
        Schema::dropIfExists('waste_deposits');
        Schema::dropIfExists('waste_types');
    }
};

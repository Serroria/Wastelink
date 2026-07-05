<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // topup, bill
            $table->string('biller_name'); // e.g. DANA, Gopay, PLN, PDAM
            $table->string('account_number'); // HP or Customer ID
            $table->integer('points_spent'); // Points deducted
            $table->decimal('nominal_rp', 12, 2); // Equivalent Rupiah value
            $table->string('ref_number')->unique(); // Unique transaction reference number
            $table->string('status')->default('success'); // success, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

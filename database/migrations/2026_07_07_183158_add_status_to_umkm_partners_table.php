<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('umkm_partners', 'status')) {
            Schema::table('umkm_partners', function (Blueprint $table) {
                $table->string('status')->default('approved')->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('umkm_partners', 'status')) {
            Schema::table('umkm_partners', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};

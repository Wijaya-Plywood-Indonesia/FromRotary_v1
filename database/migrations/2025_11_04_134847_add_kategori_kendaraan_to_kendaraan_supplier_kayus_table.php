<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kendaraan_supplier_kayus', function (Blueprint $table) {
            $table->string('kategori_kendaraan')
                ->nullable()
                ->after('pemilik_kendaraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendaraan_supplier_kayus', function (Blueprint $table) {
            $table->dropColumn('kategori_kendaraan');
        });
    }
};

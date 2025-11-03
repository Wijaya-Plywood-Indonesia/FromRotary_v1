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
        Schema::table('turun_kayus', function (Blueprint $table) {
            //
            $table->foreignId('id_kendaraan')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('tanggal')
                ->constrained('kendaraan_supplier_kayus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turun_kayus', function (Blueprint $table) {
            //
            $table->dropForeign(['id_kendaraan']);
            $table->dropColumn('id_kendaraan');
        });
    }
};

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
        Schema::table('kayu_masuks', function (Blueprint $table) {
            //

            $table->foreignId('id_supplier_kayus')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('seri')
                ->constrained('supplier_kayus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('id_kendaraan_supplier_kayus')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('id_supplier_kayus')
                ->constrained('kendaraan_supplier_kayus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('id_dokumen_kayus')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('id_kendaraan_supplier_kayus')
                ->constrained('dokumen_kayus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kayu_masuks', function (Blueprint $table) {
            //
            $table->dropForeign(['id_kendaraan_supplier_kayus']);
            $table->dropColumn('id_kendaraan_supplier_kayus');
            $table->dropForeign(['id_dokumen_kayus']);
            $table->dropColumn('id_dokumen_kayus');
        });
    }
};

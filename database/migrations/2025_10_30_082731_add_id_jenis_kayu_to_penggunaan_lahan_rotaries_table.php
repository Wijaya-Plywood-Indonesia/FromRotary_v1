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
        Schema::table('penggunaan_lahan_rotaries', function (Blueprint $table) {
            //
            $table->foreignId('id_jenis_kayu')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('id_produksi')
                ->constrained('jenis_kayus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggunaan_lahan_rotaries', function (Blueprint $table) {
            //
            $table->dropForeign(['id_jenis_kayu']);
            $table->dropColumn('id_jenis_kayu');
        });
    }
};

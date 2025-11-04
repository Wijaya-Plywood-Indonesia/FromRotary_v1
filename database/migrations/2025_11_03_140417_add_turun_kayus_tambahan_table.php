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
            $table->foreignId('id_kayu_masuk')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('tanggal')
                ->constrained('kayu_masuks')
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
            $table->dropForeign(['id_kayu_masuk']);
            $table->dropColumn('id_kayu_masuk');
        });
    }
};

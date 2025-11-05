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
        Schema::table('riwayat_kayus', function (Blueprint $table) {
            $table->foreignId('id_tempat_kayu')
                ->nullable()
                ->after('tanggal_habis') // Pastikan 'tanggal_habis' sudah ada
                ->constrained('tempat_kayus') // Merujuk ke tabel 'tempat_kayus'
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kayus', function (Blueprint $table) {
            $table->dropForeign(['id_tempat_kayu']);
            $table->dropColumn('id_tempat_kayu');
        });
    }
};

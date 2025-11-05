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
            $table->date('tanggal_masuk')->nullable()->after('id');

            // Kolom lain dari INSERT query Anda yang mungkin juga hilang
            $table->date('tanggal_digunakan')->nullable()->after('tanggal_masuk');
            $table->date('tanggal_habis')->nullable()->after('tanggal_digunakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kayus', function (Blueprint $table) {
            $table->dropColumn(['tanggal_masuk', 'tanggal_digunakan', 'tanggal_habis']);
        });
    }
};

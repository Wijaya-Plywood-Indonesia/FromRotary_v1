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
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            // KOLOM STATUS
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'ditolak'])
                ->default('menunggu')
                ->after('id_kayu_masuk')
                ->comment('Status proses turun kayu');

            // KOLOM FOTO (path ke file)
            $table->string('foto', 255)
                ->nullable()
                ->after('status')
                ->comment('Path foto pekerja saat turun kayu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            $table->dropColumn(['status', 'foto']);
        });
    }
};
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
            $table->foreignId('id_produksi_rotary')
                ->nullable()
                ->constrained('produksi_rotaries')
                ->nullOnDelete()
                ->after('tanggal_habis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kayus', function (Blueprint $table) {
            //
        });
    }
};

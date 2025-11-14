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
            $table->string('nama_supir') // Tipe data string
                ->nullable()         // Boleh kosong (opsional, tapi disarankan)
                ->after('foto');        // Posisi kolom (ganti 'id' dengan nama kolom lain jika perlu)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            $table->dropColumn('nama_supir');
        });
    }
};

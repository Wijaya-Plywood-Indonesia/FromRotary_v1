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
        Schema::table('tempat_kayus', function (Blueprint $table) {
            $table->foreignId('id_lahan')
                ->nullable()
                ->after('id_kayu_masuk') // Anda bisa letakkan setelah 'id' atau di mana saja
                ->constrained('lahans') // Referensi ke tabel lahans
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempat_kayus', function (Blueprint $table) {
            $table->dropForeign(['id_lahan']);
            $table->dropColumn('id_lahan');
        });
    }
};

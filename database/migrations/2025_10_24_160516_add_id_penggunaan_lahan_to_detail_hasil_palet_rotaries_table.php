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
        Schema::table('detail_hasil_palet_rotaries', function (Blueprint $table) {
            //
            $table->foreignId('id_penggunaan_lahan')
                ->nullable()
                ->after('id_produksi') // letakkan setelah kolom id_produksi
                ->constrained('penggunaan_lahan_rotaries')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_hasil_palet_rotaries', function (Blueprint $table) {
            $table->dropForeign(['id_penggunaan_lahan']);
            $table->dropColumn('id_penggunaan_lahan');
        });
    }
};

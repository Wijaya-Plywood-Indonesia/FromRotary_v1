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
            $table->foreignId('id_kayu_masuk')
                ->nullable()
                // ->after('id_lahan')
                ->constrained('kayu_masuks')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempat_kayus', function (Blueprint $table) {
            $table->dropForeign(['id_kayu_masuk']);
            $table->dropColumn('id_kayu_masuk');
        });
    }
};

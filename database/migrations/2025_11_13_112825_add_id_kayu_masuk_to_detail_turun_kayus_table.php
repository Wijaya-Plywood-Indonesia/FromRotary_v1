<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            $table->foreignId('id_kayu_masuk')
                ->after('id_pegawai')
                ->nullable()
                ->constrained('kayu_masuks')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            $table->dropForeign(['id_kayu_masuk']);
            $table->dropColumn('id_kayu_masuk');
        });
    }
};
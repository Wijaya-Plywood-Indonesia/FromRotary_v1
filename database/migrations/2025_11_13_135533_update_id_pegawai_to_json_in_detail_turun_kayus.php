<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            $table->json('id_pegawai')->change(); // Ubah ke JSON
        });
    }

    public function down(): void
    {
        Schema::table('detail_turun_kayus', function (Blueprint $table) {
            $table->integer('id_pegawai')->change();
        });
    }
};
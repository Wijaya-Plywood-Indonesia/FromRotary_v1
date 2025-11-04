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
        Schema::create('dokumen_kayus', function (Blueprint $table) {
            $table->string('nama_legal')->nullable();
            $table->string('dokumen_legal')->nullable();
            $table->string('upload_dokumen')->nullable();
            $table->string('upload_ktp')->nullable();
            $table->string('foto_lokasi')->nullable();
            $table->string('nama_tempat')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('dokumen_kayus', function (Blueprint $table) {
            $table->dropColumn([
                'nama_legal',
                'dokumen_legal',
                'upload_dokumen',
                'upload_ktp',
                'foto_lokasi',
                'nama_tempat',
                'alamat_lengkap',
                'latitude',
                'longitude'
            ]);
        });
    }
};

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
            $table->id();
            $table->string('dokumen_legal');
            $table->string('upload_dokumen')->nullable();
            $table->string('upload_ktp')->nullable();
            $table->string('foro_lokasi')->nullable();
            //untuk google Maps. 
            $table->string('alamat_lengkap')->nullable(); // hasil dari autocomplete Google Maps
            $table->decimal('latitude', 10, 7)->nullable();  // -6.1753924 (misalnya)
            $table->decimal('longitude', 10, 7)->nullable(); // 106.827153 (misalnya)
            $table->string('nama_tempat')->nullable(); // opsional, misal "Desa Jatirogo"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_kayus');
    }
};

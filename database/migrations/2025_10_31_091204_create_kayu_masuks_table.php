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
        Schema::create('kayu_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_Dokumen_angkut');
            $table->string('upload_dokumen_angkut');
            $table->dateTime('tgl_kayu_masuk');
            $table->integer('seri');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kayu_masuks');
    }
};

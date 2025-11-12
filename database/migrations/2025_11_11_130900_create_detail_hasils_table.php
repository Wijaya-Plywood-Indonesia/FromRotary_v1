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
        Schema::create('detail_hasil', function (Blueprint $table) {
            $table->id();
            $table->string('no_palet');
            $table->string('kw')->nullable();
            $table->string('isi')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('jenis_kayu')->nullable();
            $table->string('dryer')->nullable();
            $table->timestamp('timestamp')->nullable(); // Sesuai ERD

            // Foreign Key ke tabel induk
            $table->foreignId('id_produksi_dryer') // Nama kolom sesuai ERD
                ->references('id')
                ->on('produksi_press_dryer') // Nama tabel induk yang benar
                ->cascadeOnDelete();

            // Ukuran  
            $table->foreignId('id_ukuran')
                ->references('id')
                ->on('ukurans')
                ->cascadeOnDelete();

            // Jenis Kayu
            $table->foreignId('id_jenis_kayu')
                ->references('id')
                ->on('jenis_kayus')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_hasil');
    }
};
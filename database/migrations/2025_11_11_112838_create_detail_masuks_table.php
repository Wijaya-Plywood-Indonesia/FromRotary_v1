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
        Schema::create('detail_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_palet');
            $table->string('kw')->nullable();
            $table->string('isi')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('jenis_kayu')->nullable();
            $table->timestamp('timestamp')->nullable(); // Sesuai ERD

            // Foreign Key ke tabel induk
            $table->foreignId('id_produksi_dryer')
                ->references('id')
                ->on('produksi_press_dryer')
                ->cascadeOnDelete();


            // Foreign ke Ukuran
            $table->foreignId('id_ukuran')
                ->references('id')
                ->on('ukurans')
                ->cascadeOnDelete();

            // Foreign ke Jenis Kayu
            $table->foreignId('id_jenis_kayu')
                ->references('id')
                ->on('jenis_kayus')
                ->cascadeOnDelete();
            $table->timestamps(); // Standar created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_masuk');
    }
};
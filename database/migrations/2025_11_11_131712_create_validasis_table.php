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
        Schema::create('validasi', function (Blueprint $table) {
            $table->id();
            $table->string('role')->nullable(); // (pengawas, kepala produksi)
            $table->string('status')->nullable();

            // Foreign Key ke tabel induk
            $table->foreignId('id_produksi_dryer') // Nama kolom sesuai ERD
                ->references('id')
                ->on('produksi_press_dryer') // Nama tabel induk yang benar
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi');
    }
};
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
        Schema::create('validasi_hasil_rotaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lahan')
                ->constrained('lahans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('id_produksi')
                ->constrained('produksi_rotary')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('jumlah_batang')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi_hasil_rotaries');
    }
};

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
        Schema::create('ganti_pisau_rotaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produksi')
                ->constrained('produksi_rotary')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->time('jam_mulai_ganti_pisau');
            $table->time('jam_selesai_ganti');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ganti_pisau_rotaries');
    }
};

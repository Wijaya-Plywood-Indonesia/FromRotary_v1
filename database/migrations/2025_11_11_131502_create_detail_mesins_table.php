<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_mesins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mesin_dryer');
            $table->string('jam_kerja_mesin');
            $table->timestamps();
            $table->foreignId('id_produksi_dryer')
                  ->constrained('produksi_press_dryers')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_mesins');
    }
};

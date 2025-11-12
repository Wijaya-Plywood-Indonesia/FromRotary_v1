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
        Schema::create('detail_mesin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mesin_dryer')->nullable();

            $table->decimal('jam_kerja_mesin', 8, 2)->nullable();

            // Foreign Key ke tabel induk
            $table->foreignId('id_produksi_dryer')
                ->references('id')
                ->on('produksi_press_dryer')
                ->cascadeOnDelete();

            // Foreign key ke mesin


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_mesin');
    }
};
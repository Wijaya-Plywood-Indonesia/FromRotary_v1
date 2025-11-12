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
        Schema::create('detail_pegawais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pegawai');
            $table->string('tugas');
            $table->timestamp('masuk')->nullable();
            $table->timestamp('pulang')->nullable();
            $table->string('ijin')->nullable();
            $table->string('ket')->nullable();
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
        Schema::dropIfExists('detail_pegawais');
    }
};

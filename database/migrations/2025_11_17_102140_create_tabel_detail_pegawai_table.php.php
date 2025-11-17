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
        Schema::create('detail_pegawais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pegawai')
                ->nullable()
                ->constrained('pegawais')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('tugas');
            $table->time('masuk')->nullable();
            $table->time('pulang')->nullable();

            $table->string('ijin')->nullable();
            $table->string('ket')->nullable();

            $table->foreignId('id_produksi_dryer')
                ->nullable()
                ->constrained('produksi_press_dryers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
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

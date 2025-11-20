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
        Schema::create('pegawai_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_repair')
                ->constrained('repairs')
                ->cascadeOnDelete();

            $table->foreignId('id_pegawai')
                ->constrained('pegawais')
                ->restrictOnDelete();

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('ijin', 100)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->integer('nomor_meja')->nullable();   // nomor meja yang dikerjakan pegawai ini
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_repairs');
    }
};

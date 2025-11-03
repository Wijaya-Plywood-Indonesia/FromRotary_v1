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
        Schema::create('tempat_kayus', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah_batang');
            $table->integer('poin');

            $table->foreignId('id_kayu_masuk')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('poin')
                ->constrained('kayu_masuks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempat_kayus');
    }
};

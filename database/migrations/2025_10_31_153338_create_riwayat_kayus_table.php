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
        Schema::create('riwayat_kayus', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_masuk');
            $table->data('tanggal_digunakan');
            $table->date('tanggal_habis');
            $table->foreignId('id_tempat_kayu')
                ->nullable() // tambahkan nullable jika tabel sudah berisi data agar tidak error saat migrasi
                ->after('tanggal_habis')
                ->constrained('tempat_kayus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_kayus');
    }
};

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
        Schema::create('detail_pegawai', function (Blueprint $table) {
            $table->id();

            // Kolom ini mungkin merujuk ke tabel 'pegawais'
            // Saya buat sebagai integer, Anda bisa tambahkan ->constrained() jika tabelnya ada
            $table->unsignedBigInteger('id_pegawai')->nullable();

            $table->string('tugas')->nullable();
            $table->time('masuk')->nullable();
            $table->time('pulang')->nullable();
            $table->string('ijin')->nullable();
            $table->text('ket')->nullable(); // 'ket' (keterangan) lebih baik pakai text

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
        Schema::dropIfExists('detail_pegawai');
    }
};
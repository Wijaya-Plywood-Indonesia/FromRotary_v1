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
<<<<<<<< HEAD:database/migrations/2025_11_25_141228_create_detail_pegawai_hp_table.php
        Schema::create('detail_pegawai_hp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produksi_hp')
                ->constrained('produksi_hp')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('id_mesin')
                ->constrained('mesins')
========
        Schema::create('rencana_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produksi_repair')
                ->constrained('produksi_repairs')
>>>>>>>> dian:database/migrations/2025_11_29_132716_create_rencana_pegawais_table.php
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('id_pegawai')
                ->constrained('pegawais')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
<<<<<<<< HEAD:database/migrations/2025_11_25_141228_create_detail_pegawai_hp_table.php
            $table->text('tugas');
            $table->time('masuk');
            $table->time('pulang');
            $table->string('ijin')->nullable();
            $table->string('ket')->nullable();
========
            $table->integer('nomor_meja');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->string('ijin')->nullable();
            $table->string('keterangan')->nullable();
>>>>>>>> dian:database/migrations/2025_11_29_132716_create_rencana_pegawais_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2025_11_25_141228_create_detail_pegawai_hp_table.php
        Schema::dropIfExists('detail_pegawai_hp');
========
        Schema::dropIfExists('rencana_pegawais');
>>>>>>>> dian:database/migrations/2025_11_29_132716_create_rencana_pegawais_table.php
    }
};

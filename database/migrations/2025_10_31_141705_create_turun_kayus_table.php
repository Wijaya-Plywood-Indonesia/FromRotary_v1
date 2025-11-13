<?php
// File: ...database/migrations/..._create_turun_kayus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('turun_kayus', function (Blueprint $table) {
            $table->id();
            $table->string('kendala'); // Sesuai Model

            $table->timestamps();

            // Kolom 'id_kendaraan' tidak ada di sini,
            // jadi ini tidak akan dibuat.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turun_kayus');
    }
};
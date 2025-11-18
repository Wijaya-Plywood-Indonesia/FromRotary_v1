<?php
// database/migrations/xxxx_create_repairs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('jumlah_meja');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
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
        Schema::create('kendaraan_supplier_kayus', function (Blueprint $table) {
            $table->id();
            $table->string('nopol_kendaraan');
            $table->string('jenis_kendaraan');
            $table->string('pemilik_kendaraan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan_supplier_kayus');
    }
};

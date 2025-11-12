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
        Schema::create('produksi_press_dryers', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_produksi')->nullable();
            $table->string("shift", '10')->nullable();
            $table->string("kendala", 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_press_dryers');
    }
};

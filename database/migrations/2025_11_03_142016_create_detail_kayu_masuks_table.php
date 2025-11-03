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
        Schema::create('detail_kayu_masuks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kayu_masuk')
                ->nullable()
                ->constrained('kayu_masuks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('diameter');
            $table->integer('panjang');
            $table->integer('grade');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kayu_masuks');
    }
};

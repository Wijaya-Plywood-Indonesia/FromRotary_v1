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
        Schema::create('bahan_repairs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_repair')
                ->constrained('repairs')
                ->cascadeOnDelete();

            $table->foreignId('id_ukuran')
                ->constrained('ukurans')
                ->restrictOnDelete();

            $table->foreignId('id_jenis')
                ->constrained('jenis_kayus')
                ->restrictOnDelete();

            $table->string('kw', 10);
            $table->integer('total_lembar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_repairs');
    }
};

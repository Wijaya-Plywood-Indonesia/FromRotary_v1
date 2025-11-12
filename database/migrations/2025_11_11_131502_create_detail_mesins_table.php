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
        Schema::create('detail_mesin', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->unsignedBigInteger('id_mesin_dryer')->nullable();

            $table->decimal('jam_kerja_mesin', 8, 2)->nullable();

            // Foreign Key ke tabel induk
            $table->foreignId('id_produksi_dryer')
                ->references('id')
                ->on('produksi_press_dryer')
                ->cascadeOnDelete();

            // Foreign key ke mesin


=======
            $table->unsignedBigInteger('id_mesin_dryer');
            $table->string('jam_kerja_mesin');
>>>>>>> d4d04f7caa43051e9ddb0c5abb86a3e8e5dc0c6b
            $table->timestamps();
            $table->foreignId('id_produksi_dryer')
                  ->constrained('produksi_press_dryers')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_mesin');
    }
};
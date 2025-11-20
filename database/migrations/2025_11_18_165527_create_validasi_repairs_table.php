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
        Schema::create('validasi_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_repair')
                ->constrained('repairs')
                ->cascadeOnDelete();

            $table->string('role', 50);
            $table->string('status', 50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi_repairs');
    }
};

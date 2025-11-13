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
        Schema::table('detail_pegawais', function (Blueprint $table) {

            // Tambah foreign key baru
            $table->foreignId('id_pegawai')
                ->nullable()
                ->after('id')
                ->constrained('pegawais')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {

    }
};

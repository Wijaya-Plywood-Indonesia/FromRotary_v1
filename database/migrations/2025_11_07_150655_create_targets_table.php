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
        Schema::create('targets', function (Blueprint $table) {
            $table->id('id_target');
            $table->unsignedBigInteger('id_mesin');
            $table->unsignedBigInteger('id_ukuran');
            $table->unsignedBigInteger('id_jenis_kayu');

            $table->string('kode_ukuran')->nullable();
            $table->integer('target');
            $table->integer('orang');
            $table->integer('jam');

            // kolom virtual / generated
            $table->decimal('targetperjam', 15, 2)->virtualAs('`target` / `jam`');
            $table->decimal('targetperorang', 15, 2)->virtualAs('`target` / `orang`');

            $table->decimal('gaji', 15, 2);
            $table->decimal('potongan', 15, 2)->virtualAs('`gaji` / `targetperorang`');

            $table->timestamps();
            $table->string('status')->default('diajukan');

            // foreign keys
            $table->foreign('id_mesin')->references('id')->on('mesins')->onDelete('restrict');
            $table->foreign('id_ukuran')->references('id')->on('ukurans')->onDelete('restrict');
            $table->foreign('id_jenis_kayu')->references('id')->on('jenis_kayus')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};

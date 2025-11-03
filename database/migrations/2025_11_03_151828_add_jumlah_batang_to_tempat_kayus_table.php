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
        Schema::table('tempat_kayus', function (Blueprint $table) {
            $table->integer('jumlah_batang')->default(0)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempat_kayus', function (Blueprint $table) {
            $table->dropColumn('jumlah_batang');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kendaraan_supplier_kayus', function (Blueprint $table) {
            // Kita gunakan 'id_supplier' agar konsisten dengan form
            $table->foreignId('id_supplier')
                ->nullable()
                ->after('id') // Posisikan setelah ID (opsional)
                ->constrained('supplier_kayus') // Merujuk ke tabel 'supplier_kayus'
                ->onDelete('set null'); // Jika supplier dihapus, set null
        });
    }

    public function down(): void
    {
        Schema::table('kendaraan_supplier_kayus', function (Blueprint $table) {
            $table->dropForeign(['id_supplier']);
            $table->dropColumn('id_supplier');
        });
    }
};
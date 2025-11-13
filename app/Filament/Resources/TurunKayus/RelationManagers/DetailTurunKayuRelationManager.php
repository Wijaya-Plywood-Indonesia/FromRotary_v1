<?php

namespace App\Filament\Resources\TurunKayus\RelationManagers;

use App\Filament\Resources\DetailTurunKayus\Schemas\DetailTurunKayuForm;
use App\Filament\Resources\DetailTurunKayus\Tables\DetailTurunKayusTable;
use App\Models\TurunKayu;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Models\DetailTurunKayu; // Pastikan Anda mengimpor Model yang benar

class DetailTurunKayuRelationManager extends RelationManager
{
    // --- PERBAIKAN ---
    // Nama relasi diubah menjadi huruf kecil 'detailTurunKayu'
    // agar cocok dengan nama method relasi di Model TurunKayu Anda.
    protected static string $relationship = 'detailTurunKayu';

    public function form(Schema $schema): Schema
    {
        return DetailTurunKayuForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailTurunKayusTable::configure($table);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil ID pegawai (bisa satu atau banyak)
        $pegawaiIds = $data['id_pegawai'] ?? [];
        if (!is_array($pegawaiIds)) {
            $pegawaiIds = [$pegawaiIds];
        }

        // Ambil ID kayu masuk (yang sudah difilter)
        $kayuMasukId = $data['id_kayu_masuk'];

        // Hapus data filter agar Filament tidak bingung
        unset($data['id_pegawai']);
        unset($data['id_kayu_masuk']);
        // Hapus juga key filter sementara
        unset($data['id_supplier_kayus']);
        unset($data['id_kendaraan_supplier_kayus']);

        // Dapatkan record 'TurunKayu' (induk) yang sedang dibuka
        $turunKayu = $this->ownerRecord;

        foreach ($pegawaiIds as $pegawaiId) {
            DetailTurunKayu::create([
                'id_turun_kayu' => $turunKayu->id, // ID Induk
                'id_pegawai' => $pegawaiId,          // ID Pegawai dari loop
                'id_kayu_masuk' => $kayuMasukId,     // ID Kayu Masuk dari form
            ]);
        }

        // Kembalikan array kosong agar Filament tidak mencoba membuat record standar
        return [];
    }
}
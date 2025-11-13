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
        $pegawaiIds = $data['id_pegawai'] ?? [];
        $kayuMasukId = $data['id_kayu_masuk'] ?? null;
        $turunKayuId = $this->ownerRecord->id;

        // Validasi wajib
        if (empty($pegawaiIds) || !$kayuMasukId || !$turunKayuId) {
            return [];
        }

        // Pastikan $pegawaiIds adalah array
        $pegawaiIds = is_array($pegawaiIds) ? $pegawaiIds : [$pegawaiIds];

        // Loop & insert 1 baris per pegawai
        foreach ($pegawaiIds as $pegawaiId) {
            DetailTurunKayu::create([
                'id_turun_kayu' => $turunKayuId,
                'id_pegawai' => $pegawaiId,
                'id_kayu_masuk' => $kayuMasukId,
            ]);
        }

        // Kembalikan array kosong → Filament tidak insert lagi
        return [];
    }
}
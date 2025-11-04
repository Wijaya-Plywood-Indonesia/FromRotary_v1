<?php

namespace App\Filament\Resources\TurunKayus\RelationManagers;

use App\Filament\Resources\DetailTurunKayus\Schemas\DetailTurunKayuForm;
use App\Filament\Resources\DetailTurunKayus\Tables\DetailTurunKayusTable;
use App\Models\DetailTurunKayu;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailTurunKayuRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailTurunKayu';

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

        if (!is_array($pegawaiIds)) {
            $pegawaiIds = [$pegawaiIds];
        }

        unset($data['id_pegawai']);

        // karena ini Relation Manager, otomatis ada $this->ownerRecord (TurunKayu yang sedang dibuka)
        $turunKayu = $this->ownerRecord;

        foreach ($pegawaiIds as $pegawaiId) {
            DetailTurunKayu::create([
                'id_turun_kayus' => $turunKayu->id,
                'id_pegawai' => $pegawaiId,
            ]);
        }

        // return kosong supaya Filament gak nyoba buat 1 record lagi
        return [];
    }
}

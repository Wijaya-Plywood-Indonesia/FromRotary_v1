<?php

namespace App\Filament\Resources\DetailTurunKayus\Pages;

use App\Filament\Resources\DetailTurunKayus\DetailTurunKayuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailTurunKayu extends CreateRecord
{
    protected static string $resource = DetailTurunKayuResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $pegawaiIds = $data['id_pegawai'] ?? [];

        // Pastikan dalam bentuk array
        if (!is_array($pegawaiIds)) {
            $pegawaiIds = [$pegawaiIds];
        }

        unset($data['id_pegawai']);

        $record = static::getModel()::create($data);

        // Pastikan relasi "pegawaiTurunKayus" ada di model
        $record->pegawaiTurunKayus()->sync($pegawaiIds);

        return $data;
    }
}

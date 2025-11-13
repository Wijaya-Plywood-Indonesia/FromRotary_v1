<?php

namespace App\Filament\Resources\ValidasiPressDryers\Pages;

use App\Filament\Resources\ValidasiPressDryers\ValidasiPressDryerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateValidasiPressDryer extends CreateRecord
{
    protected static string $resource = ValidasiPressDryerResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 👇 ambil id_produksi_dryer dari URL query, misal ?produksi_dryer_id=5
        if (request()->has('produksi_dryer_id')) {
            $data['id_produksi_dryer'] = request()->query('produksi_dryer_id');
        } else {
            // Kalau tidak dikirim, bisa fallback ke ID tertentu
            // (atau tampilkan error jika kamu mau wajibkan)
            $data['id_produksi_dryer'] = 1; // contoh fallback
        }

        return $data;
    }
}

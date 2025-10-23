<?php

namespace App\Filament\Resources\ProduksiRotaries\Pages;

use App\Filament\Resources\ProduksiRotaries\ProduksiRotaryResource;
use App\Models\ProduksiRotary;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduksiRotary extends CreateRecord
{
    protected static string $resource = ProduksiRotaryResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        $mesinIds = $data['id_mesin'];
        unset($data['id_mesin']);

        $created = null;

        foreach ($mesinIds as $idMesin) {
            $created = ProduksiRotary::create([
                ...$data,
                'id_mesin' => $idMesin,
            ]);
        }

        // Return satu model terakhir agar kompatibel
        return $created;
    }
}

<?php

namespace App\Filament\Resources\LaporanProduksis\Pages;

use App\Filament\Resources\LaporanProduksis\LaporanProduksiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLaporanProduksi extends EditRecord
{
    protected static string $resource = LaporanProduksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

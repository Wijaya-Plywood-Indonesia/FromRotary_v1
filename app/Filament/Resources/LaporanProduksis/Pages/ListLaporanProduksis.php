<?php

namespace App\Filament\Resources\LaporanProduksis\Pages;

use App\Filament\Resources\LaporanProduksis\LaporanProduksiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaporanProduksis extends ListRecords
{
    protected static string $resource = LaporanProduksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

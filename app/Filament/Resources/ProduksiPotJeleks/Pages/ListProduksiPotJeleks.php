<?php

namespace App\Filament\Resources\ProduksiPotJeleks\Pages;

use App\Filament\Resources\ProduksiPotJeleks\ProduksiPotJelekResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProduksiPotJeleks extends ListRecords
{
    protected static string $resource = ProduksiPotJelekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ProduksiDempuls\Pages;

use App\Filament\Resources\ProduksiDempuls\ProduksiDempulResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiDempul extends ViewRecord
{
    protected static string $resource = ProduksiDempulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

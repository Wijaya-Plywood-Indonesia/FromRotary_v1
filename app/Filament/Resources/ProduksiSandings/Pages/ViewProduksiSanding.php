<?php

namespace App\Filament\Resources\ProduksiSandings\Pages;

use App\Filament\Resources\ProduksiSandings\ProduksiSandingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiSanding extends ViewRecord
{
    protected static string $resource = ProduksiSandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

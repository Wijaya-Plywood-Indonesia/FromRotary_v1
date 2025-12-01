<?php

namespace App\Filament\Resources\ProduksiRepairs\Pages;

use App\Filament\Resources\ProduksiRepairs\ProduksiRepairResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiRepair extends ViewRecord
{
    protected static string $resource = ProduksiRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

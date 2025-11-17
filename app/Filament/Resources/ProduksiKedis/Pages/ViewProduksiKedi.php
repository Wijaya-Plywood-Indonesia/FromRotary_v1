<?php

namespace App\Filament\Resources\ProduksiKedis\Pages;

use App\Filament\Resources\ProduksiKedis\ProduksiKediResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiKedi extends ViewRecord
{
    protected static string $resource = ProduksiKediResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

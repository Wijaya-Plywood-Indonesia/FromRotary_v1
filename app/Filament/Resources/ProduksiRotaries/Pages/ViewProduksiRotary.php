<?php

namespace App\Filament\Resources\ProduksiRotaries\Pages;

use App\Filament\Resources\ProduksiRotaries\ProduksiRotaryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

use App\Filament\Resources\ProduksiRotaries\Widgets\ProduksiSummaryWidget;

class ViewProduksiRotary extends ViewRecord
{
    protected static string $resource = ProduksiRotaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
     protected function getHeaderWidgets(): array
    {
        return [
            ProduksiSummaryWidget::class,
        ];
    }
}

<?php

namespace App\Filament\Resources\ProduksiRotaries\Pages;

use App\Filament\Resources\ProduksiRotaries\ProduksiRotaryResource;
use App\Filament\Resources\ProduksiRotaries\Widgets\ProduksiSummaryWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiRotary extends ViewRecord
{
    protected static string $resource = ProduksiRotaryResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            ProduksiSummaryWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

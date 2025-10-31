<?php

namespace App\Filament\Resources\KayuMasuks\Pages;

use App\Filament\Resources\KayuMasuks\KayuMasukResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKayuMasuk extends ViewRecord
{
    protected static string $resource = KayuMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\KayuMasuks\Pages;

use App\Filament\Resources\KayuMasuks\KayuMasukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKayuMasuks extends ListRecords
{
    protected static string $resource = KayuMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

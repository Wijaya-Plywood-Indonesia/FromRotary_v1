<?php

namespace App\Filament\Resources\ValidasiHasilDryers\Pages;

use App\Filament\Resources\ValidasiHasilDryers\ValidasiHasilDryerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListValidasiHasilDryers extends ListRecords
{
    protected static string $resource = ValidasiHasilDryerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

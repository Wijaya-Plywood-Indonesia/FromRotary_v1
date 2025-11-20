<?php

namespace App\Filament\Resources\PegawaiRepairs\Pages;

use App\Filament\Resources\PegawaiRepairs\PegawaiRepairResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPegawaiRepairs extends ListRecords
{
    protected static string $resource = PegawaiRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

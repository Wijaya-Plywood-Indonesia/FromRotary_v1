<?php

namespace App\Filament\Resources\PegawaiRepairs\Pages;

use App\Filament\Resources\PegawaiRepairs\PegawaiRepairResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPegawaiRepair extends ViewRecord
{
    protected static string $resource = PegawaiRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

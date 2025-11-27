<?php

namespace App\Filament\Resources\RencanaRepairs\Pages;

use App\Filament\Resources\RencanaRepairs\RencanaRepairResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRencanaRepair extends ViewRecord
{
    protected static string $resource = RencanaRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

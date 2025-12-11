<?php

namespace App\Filament\Resources\Repairs\Pages;

use App\Filament\Resources\Repairs\RepairResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRepair extends ViewRecord
{
    protected static string $resource = RepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

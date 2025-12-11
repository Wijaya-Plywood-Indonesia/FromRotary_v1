<?php

namespace App\Filament\Resources\Repairs\Pages;

use App\Filament\Resources\Repairs\RepairResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRepair extends EditRecord
{
    protected static string $resource = RepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

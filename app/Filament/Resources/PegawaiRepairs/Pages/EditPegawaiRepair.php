<?php

namespace App\Filament\Resources\PegawaiRepairs\Pages;

use App\Filament\Resources\PegawaiRepairs\PegawaiRepairResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPegawaiRepair extends EditRecord
{
    protected static string $resource = PegawaiRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

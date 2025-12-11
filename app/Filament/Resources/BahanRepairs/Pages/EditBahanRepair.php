<?php

namespace App\Filament\Resources\BahanRepairs\Pages;

use App\Filament\Resources\BahanRepairs\BahanRepairResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBahanRepair extends EditRecord
{
    protected static string $resource = BahanRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

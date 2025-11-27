<?php

namespace App\Filament\Resources\RencanaTargets\Pages;

use App\Filament\Resources\RencanaTargets\RencanaTargetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRencanaTarget extends EditRecord
{
    protected static string $resource = RencanaTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

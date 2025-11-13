<?php

namespace App\Filament\Resources\ValidasiHasilDryers\Pages;

use App\Filament\Resources\ValidasiHasilDryers\ValidasiHasilDryerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditValidasiHasilDryer extends EditRecord
{
    protected static string $resource = ValidasiHasilDryerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\NotaKayus\Pages;

use App\Filament\Resources\NotaKayus\NotaKayuResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNotaKayu extends ViewRecord
{
    protected static string $resource = NotaKayuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

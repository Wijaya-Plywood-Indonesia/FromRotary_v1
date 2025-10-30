<?php

namespace App\Filament\Resources\HargaKayus\Pages;

use App\Filament\Resources\HargaKayus\HargaKayuResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHargaKayu extends EditRecord
{
    protected static string $resource = HargaKayuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TurunKayus\Pages;

use App\Filament\Resources\TurunKayus\TurunKayuResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTurunKayu extends ViewRecord
{
    protected static string $resource = TurunKayuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}

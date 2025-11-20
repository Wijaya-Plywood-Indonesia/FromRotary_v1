<?php

namespace App\Filament\Resources\BahanRepairs\Pages;

use App\Filament\Resources\BahanRepairs\BahanRepairResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBahanRepairs extends ListRecords
{
    protected static string $resource = BahanRepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

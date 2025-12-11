<?php

namespace App\Filament\Resources\Repairs\Pages;

use App\Filament\Resources\Repairs\RepairResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRepairs extends ListRecords
{
    protected static string $resource = RepairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

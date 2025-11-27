<?php

namespace App\Filament\Resources\RencanaTargets\Pages;

use App\Filament\Resources\RencanaTargets\RencanaTargetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRencanaTargets extends ListRecords
{
    protected static string $resource = RencanaTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

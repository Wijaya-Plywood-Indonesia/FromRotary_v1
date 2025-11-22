<?php

namespace App\Filament\Resources\NotaBarangMasuks\Pages;

use App\Filament\Resources\NotaBarangMasuks\NotaBarangMasukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotaBarangMasuks extends ListRecords
{
    protected static string $resource = NotaBarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Nota BM'),
        ];
    }
}

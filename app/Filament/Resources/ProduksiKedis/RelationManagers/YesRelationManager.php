<?php

namespace App\Filament\Resources\ProduksiKedis\RelationManagers;

use App\Filament\Resources\ValidasiKedis\ValidasiKediResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class YesRelationManager extends RelationManager
{
    protected static string $relationship = 'validasiKedi';

    protected static ?string $relatedResource = ValidasiKediResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

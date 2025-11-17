<?php

namespace App\Filament\Resources\ProduksiKedis\RelationManagers;

use App\Filament\Resources\DetailMasukKedis\DetailMasukKediResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DetailMasukKediRelationManager extends RelationManager
{
    protected static string $relationship = 'detailMasukKedi';

    protected static ?string $relatedResource = DetailMasukKediResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }

}

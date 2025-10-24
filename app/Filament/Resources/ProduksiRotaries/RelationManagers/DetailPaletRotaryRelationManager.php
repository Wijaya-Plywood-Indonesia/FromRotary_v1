<?php

namespace App\Filament\Resources\ProduksiRotaries\RelationManagers;

use App\Filament\Resources\DetailHasilPaletRotaries\Schemas\DetailHasilPaletRotaryForm;
use App\Filament\Resources\DetailHasilPaletRotaries\Tables\DetailHasilPaletRotariesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailPaletRotaryRelationManager extends RelationManager
{
    protected static ?string $title = 'Hasil';
    protected static string $relationship = 'detailPaletRotary';

    public function form(Schema $schema): Schema
    {
        return DetailHasilPaletRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailHasilPaletRotariesTable::configure($table);
    }
}

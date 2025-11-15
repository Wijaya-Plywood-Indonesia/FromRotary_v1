<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;

use App\Filament\Resources\DetailMasuks\Schemas\DetailMasukForm;
use App\Filament\Resources\DetailMasuks\Tables\DetailMasuksTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Ukuran;

class DetailMasuksRelationManager extends RelationManager
{
        protected static ?string $title = 'Modal';
    protected static string $relationship = 'detailMasuks';

    public function form(Schema $schema): Schema
    {
        return DetailMasukForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailMasuksTable::configure($table);
    }
}
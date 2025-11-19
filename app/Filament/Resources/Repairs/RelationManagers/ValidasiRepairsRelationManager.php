<?php

namespace App\Filament\Resources\Repairs\RelationManagers;

use App\Filament\Resources\ValidasiRepairs\Tables\ValidasiRepairsTable;
use App\Filament\Resources\ValidasiRepairs\Schemas\ValidasiRepairForm;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ValidasiRepairsRelationManager extends RelationManager
{
    protected static string $relationship = 'validasiRepairs';

    public function form(Schema $schema): Schema
    {
        return ValidasiRepairForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ValidasiRepairsTable::configure($table);
    }
}

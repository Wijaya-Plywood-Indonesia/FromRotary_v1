<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;

use App\Filament\Resources\ValidasiHasilRotaries\Schemas\ValidasiHasilRotaryForm;
use App\Filament\Resources\ValidasiPressDryers\Tables\ValidasiPressDryersTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ValidasiPressDryersRelationManager extends RelationManager
{
        protected static ?string $title = 'Validasi';
    protected static string $relationship = 'validasiPressDryers';

    public function form(Schema $schema): Schema
    {
        return ValidasiHasilRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ValidasiPressDryersTable::configure($table);
    }
}

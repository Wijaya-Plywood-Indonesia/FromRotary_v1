<?php

namespace App\Filament\Resources\ProduksiRotaries\RelationManagers;


use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\ValidasiHasilRotaries\Tables\ValidasiHasilRotariesTable;
use App\Filament\Resources\ValidasiHasilRotaries\Schemas\ValidasiHasilRotaryForm;

class DetailValidasiHasilRotaryRelationManager extends RelationManager
{
    protected static ?string $title = 'Validasi';
    protected static string $relationship = 'detailValidasiHasilRotary';
    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Schema $schema): Schema
    {
        return ValidasiHasilRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ValidasiHasilRotariesTable::configure($table);
    }
}

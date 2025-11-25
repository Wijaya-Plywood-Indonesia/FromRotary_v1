<?php

namespace App\Filament\Resources\ProduksiRotaries\RelationManagers;

use App\Filament\Resources\PenggunaanLahanRotaries\Schemas\PenggunaanLahanRotaryForm;
use App\Filament\Resources\PenggunaanLahanRotaries\Tables\PenggunaanLahanRotariesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailLahanRotaryRelationManager extends RelationManager
{
    protected static ?string $title = 'Lahan';
    protected static string $relationship = 'detailLahanRotary';
    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Schema $schema): Schema
    {
        return PenggunaanLahanRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PenggunaanLahanRotariesTable::configure($table);
    }
}

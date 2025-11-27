<?php

namespace App\Filament\Resources\ProduksiHotPresses\RelationManagers;

use App\Filament\Resources\VeneerBahanHps\Schemas\VeneerBahanHpForm;
use App\Filament\Resources\VeneerBahanHps\Tables\VeneerBahanHpsTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VeneerBahanHpRelationManager extends RelationManager
{
    protected static string $relationship = 'veneerBahanHp';

    public function form(Schema $schema): Schema
    {
        return VeneerBahanHpForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return VeneerBahanHpsTable::configure($table);
    }
}

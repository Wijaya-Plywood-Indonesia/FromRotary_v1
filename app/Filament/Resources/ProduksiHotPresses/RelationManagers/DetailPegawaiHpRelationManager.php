<?php

namespace App\Filament\Resources\ProduksiHotPresses\RelationManagers;

use App\Filament\Resources\DetailPegawaiHps\Schemas\DetailPegawaiHpForm;
use App\Filament\Resources\DetailPegawaiHps\Tables\DetailPegawaiHpsTable;
use App\Filament\Resources\DetailPegawaiHps\Schemas\DetailPegawaiHpsForm;
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

class DetailPegawaiHpRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPegawaiHp';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return DetailPegawaiHpForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailPegawaiHpsTable::configure($table);
    }
}

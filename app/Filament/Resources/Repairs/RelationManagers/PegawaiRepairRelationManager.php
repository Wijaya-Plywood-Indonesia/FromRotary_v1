<?php

namespace App\Filament\Resources\Repairs\RelationManagers;

use App\Filament\Resources\PegawaiRepairs\Tables\PegawaiRepairsTable;
use App\Filament\Resources\PegawaiRepairs\Schemas\PegawaiRepairForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PegawaiRepairRelationManager extends RelationManager
{
    protected static string $relationship = 'pegawaiRepairs';

    public function form(Schema $schema): Schema
    {
        return PegawaiRepairForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PegawaiRepairsTable::configure($table);
    }
}

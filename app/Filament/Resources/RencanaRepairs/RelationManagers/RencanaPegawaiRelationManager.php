<?php

namespace App\Filament\Resources\RencanaRepairs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\RencanaPegawais\Schemas\RencanaPegawaiForm;
use App\Filament\Resources\RencanaPegawais\Tables\RencanaPegawaisTable;

class RencanaPegawaiRelationManager extends RelationManager
{
    protected static string $relationship = 'pegawaiRepairs';

    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Schema $schema): Schema
    {
        return RencanaPegawaiForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RencanaPegawaisTable::configure($table);
    }
}

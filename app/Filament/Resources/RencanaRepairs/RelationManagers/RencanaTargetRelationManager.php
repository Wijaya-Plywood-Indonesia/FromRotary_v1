<?php

namespace App\Filament\Resources\RencanaRepairs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\RencanaTargets\Schemas\RencanaTargetForm;
use App\Filament\Resources\RencanaTargets\Tables\RencanaTargetsTable;

class RencanaTargetRelationManager extends RelationManager
{
    protected static string $relationship = 'rencanaTargets';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return RencanaTargetForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RencanaTargetsTable::configure($table);
    }
}

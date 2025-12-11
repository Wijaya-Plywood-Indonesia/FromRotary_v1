<?php

namespace App\Filament\Resources\Repairs\RelationManagers;

use App\Filament\Resources\BahanRepairs\Tables\BahanRepairsTable;
use App\Filament\Resources\BahanRepairs\Schemas\BahanRepairForm;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Form;

class BahanRepairsRelationManager extends RelationManager
{
    protected static string $relationship = 'bahanRepairs';

    public function form(Schema $schema): Schema
    {
        return BahanRepairForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return BahanRepairsTable::configure($table);
    }
}

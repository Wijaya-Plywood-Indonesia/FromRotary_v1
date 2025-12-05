<?php

namespace App\Filament\Resources\ProduksiSandings\RelationManagers;

use App\Filament\Resources\ModalSandings\Schemas\ModalSandingForm;
use App\Filament\Resources\ModalSandings\Tables\ModalSandingsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use ModalSanding;

class ModalSandingRelationManager extends RelationManager
{
    protected static string $relationship = 'modalSandings';

    public function form(Schema $schema): Schema
    {
        return ModalSandingForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ModalSandingsTable::configure($table);
    }
}

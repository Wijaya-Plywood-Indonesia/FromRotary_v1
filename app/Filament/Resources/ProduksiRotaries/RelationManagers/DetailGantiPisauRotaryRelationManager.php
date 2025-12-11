<?php

namespace App\Filament\Resources\ProduksiRotaries\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\GantiPisauRotaries\Tables\GantiPisauRotariesTable;
use App\Filament\Resources\GantiPisauRotaries\Schemas\GantiPisauRotaryForm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;

class DetailGantiPisauRotaryRelationManager extends RelationManager
{
    protected static ?string $title = 'Kendala';
    protected static string $relationship = 'detailGantiPisauRotary';
    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Schema $schema): Schema
    {
        return GantiPisauRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return GantiPisauRotariesTable::configure($table) ;
    }
}
